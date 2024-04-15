<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ToolsDebugController extends Controller
{
    public  $pwd_default = "123456";
    public function index()
    {
        $auth = Auth::user();
        if (!$auth->isRhOrAdmin() && !in_array($auth->registration_number,  Menu::$USER_ALLOWED_PART_ACCESS["debug_tools"])) {
            return  abort(401);
         }
        $users = User::withOut(["userJob"])->select(["id","deleted","name","firstname","registration_number","user_type_id"])->whereDeleted(0)->get();
        $pwd_default = $this->pwd_default;
        return view("tools-debug.index",compact("users","pwd_default"));
    }

    public function do_reset_pwd(Request $request)
    {
        $auth = Auth::user();
        if (!$auth->isRhOrAdmin() && !in_array($auth->registration_number,  Menu::$USER_ALLOWED_PART_ACCESS["debug_tools"])) {
           return ["success" => false ,"message" =>"Acces refusé vous n'est pas  Admin ou RH ou avoir le droit"];
        }
        if (!$request->users) {
           return ["success" => false ,"message" =>"Selectionner un ou des utlisateurs à re-initilliser son mot de passe."];
        }
        $new_pwd = $request->new_pwd ? $request->new_pwd : $this->pwd_default;
        User::whereIn('id',$request->users)->whereDeleted(0)->update(['password' => Hash::make($new_pwd) ]); // do the "reset"
        return ["success" => true ,"message" =>" Re-initillisation mot de passe avec succes"];
    }
    public function do_reset_pwd_all(Request $request)
    {
        $auth = Auth::user();
        if (!$auth->isRhOrAdmin() && !in_array($auth->registration_number,  Menu::$USER_ALLOWED_PART_ACCESS["debug_tools"])) {
           return ["success" => false ,"message" =>"Acces refusé vous n'est pas  Admin ou RH ou avoir le droit"];
        }
        $new_pwd = $request->new_pwd ? $request->new_pwd : $this->pwd_default;
        User::whereNotIn('id',$request->users)->whereDeleted(0)->update(['password' => Hash::make($new_pwd) ]); // do the "reset"
        return ["success" => true ,"message" =>" Re-initillisation de tout les mots de passe avec succes"];
    }
}