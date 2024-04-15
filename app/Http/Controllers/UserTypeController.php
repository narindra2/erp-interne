<?php

namespace App\Http\Controllers;

use App\Models\UserType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserTypeController extends Controller
{
    private $gate_userType = 'user_type';
    //
    public function index()
    {
        Gate::authorize($this->gate_userType);
        $data = array();
        $data['title'] = 'Gestion rôle';
        $data['types'] = UserType::whereDeleted(0)->paginate(10);
        return view('user&userType.usertype', $data);
    }

    public function addOrUpdate(Request $request)
    {
        Gate::authorize($this->gate_userType);
        $data = $request->validate([
            'name' => ['required'],
            'user_type_id' => ['required_unless:type,null']
        ]);
        UserType::updateOrCreate(
            ['id' => $request->user_type_id],
            ['name' => $request->name]
        );

        if ($request->user_type_id != null) {

        }

        return back();
    }

    public function delete(Request $request)
    {
        Gate::authorize($this->gate_userType);
        try {
            $type_id = $request->type_id;
            $userType = UserType::findOrFail($type_id);
            $userType->deleted = 1;
            $userType->save();

            
        }
        catch(Exception $e) {}
        return back();
    }
}
