<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmationCodeMail;
use App\Mail\SendCodeMail;
use App\Models\ConfirmationCode;
use App\Models\User;
use App\Notifications\SendMailResetPasswordNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    //
    public function index()
    {
        $data = [];
        $data['title'] = '31 - Récuperer compte';
        return view('reset-password.sendRegistrationNumber', $data);
    }

    public function sendCodeToEmail(Request $request)
    {
        $data = $request->validate([
            'registration_number' => ['required']
        ]);

        try {
            DB::beginTransaction();

            $user = User::where('registration_number', $request->registration_number)->first();
            if ($user == null) {
                return back()->withInput(['error' => 'Matricule non trouvé']);
            }
            $confirmationCode = ConfirmationCode::generate($user->id);
            $details = [
                'firstname' => $user->firstname,
                'code' => $confirmationCode->code
            ];
            Mail::to($user->email)->send(new ConfirmationCodeMail($details));
            // $user->notify(new SendMailResetPasswordNotification($details));
            // Mail::to($user->email)->send(new SendCodeMail($details));
            DB::commit();
            return redirect()->route('confirmationCode', ['registration_number' => $user->registration_number]);
        }
        catch(Exception $e) {
            DB::rollBack();
            // return back()->withInput(['error' => "Impossible d'envoyer le code à l'adresse mail, verifier votre connexion"]);
            return back()->withInput(['error' => $e->getMessage()]);    
        }
    }

    public function goToConfirmationCode(Request $request)
    {
        $data = [];
        $data['title'] = '31 - Récuperer compte';
        $data['registration_number'] = $request->registration_number;
        return view('reset-password.checkConfirmationCode', $data);
    }

    public function checkIfCodeIsCorrect(Request $request)
    {
        $data = $request->validate([
            'registration_number' => ['required'],
            'code' => ['required']
        ]);

        try {
            ConfirmationCode::check($request->registration_number, $request->code);
            return redirect()->route('password-form', ['registration_number' => $request->registration_number, 'code' => $request->code]);
        } 
        catch(Exception $e) {
            return back()->withInput(['error' => $e->getMessage()]);
        }   
    }

    public function goToPasswordForm(Request $request)
    {
        $data = [];
        $data['title'] = '31 - Récuperer compte';
        $data['code'] = $request->code;
        $data['registration_number'] = $request->registration_number;
        return view('reset-password.newPassword', $data);
    }

    public function checkNewPassword(Request $request)
    {
        $data = $request->validate([
            'registration_number' => ['required'],
            'code' => ['required'],
            'password' => ['required', 'min:6'],
            'conf_password' => ['required', 'same:password']
        ]);
        ConfirmationCode::check($request->registration_number, $request->code);
        try {
            ConfirmationCode::check($request->registration_number, $request->code);
            $user = User::where('registration_number', $request->registration_number)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            return redirect()->route('login');
        } 
        catch(Exception $e) {
            return back()->withInput(['error' => $e->getMessage()]);
        }
    }
}
