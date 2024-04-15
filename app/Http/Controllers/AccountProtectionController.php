<?php

namespace App\Http\Controllers;

use App\Models\EmailActivation;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AccountProtectionController extends Controller
{
    //
    public function index()
    {
        $data = [];
        $data['title'] = '31 - Sécurité compte';
        $data['user'] = Auth::user();
        return view('account-security.index', $data);
    }

    public function changePassword(Request $request)
    {
        $data = $request->validate([
            'actualPassword' => ['required'],
            'newPassword' => ['required', 'min:6'],
            'confirmPassword' => ['required', 'same:newPassword']
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::user();
            $user->changePassword($data['actualPassword'], $data['newPassword']);
            DB::commit();
            return back()->withInput([
                'success' => "Votre mot de passe a été changé avec succès"
            ]);
        }
        catch(Exception $e) {
            DB::rollBack();
            return back()->withInput([
                'actualPassword' => $data['actualPassword'],
                'newPassword' => $data['newPassword'],
                'confirmPassword' => $data['confirmPassword'],
                'error' => $e->getMessage()
            ]);
        }       
    }

    public function addEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email']
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->all()]);
        }
        
        try {
            $user = Auth::user();
            $user->addEmail($request->email);
            return response()->json(['success' => 'OK']);
        }
        catch(Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]]);
        }
    }

    public function reSendCode()
    {
        try {
            auth()->user()->sendCodeToEmail();
            return back();
        }
        catch(Exception $e) {
            // abort(500, 'Erreur de connexion');
            return back()->withInput(['error' => $e->getMessage()]);
        }
        
    }

    public function confirmMail(Request $request)
    {
        try {
            $token = $request->token;
            $emailActivation = EmailActivation::where('token', $token)->first();
            if ($emailActivation == null) {
                return redirect()->route('login')->withInput(['error' => "Jeton corrompu"]);
            }
            if ($emailActivation->deleted == 1) {
                return redirect('login');
            }
            if (Carbon::make($emailActivation->expiration_date) < Carbon::now()) {
                return redirect()->route('login')->withInput(['error' => "Jeton corrompu"]);
            }
            $user = User::find($emailActivation->users_id);
            $user->email_verified_at = Carbon::now();
            $user->save();
            $emailActivation->deleted = 1;
            $emailActivation->save();
            if (Auth::user()->id != $emailActivation->users_id) {
                return redirect()->route('login');
            }   
            return redirect('profile');  
        }
        catch(Exception $e) {
            return redirect()->route('login')->withInput(['error' => $e->getMessage()]);
        }
        
    }
}
