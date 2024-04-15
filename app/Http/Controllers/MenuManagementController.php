<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Submenu;
use App\Models\UserType;
use App\View\Components\Sidebar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class MenuManagementController extends Controller
{
    public static $_SUBMENU = 'submenu_';
    public static $_TYPE = 'type_';

    public function access()
    {
        $keys = array('user_type', 'users');
        foreach($keys as $key)  Gate::authorize($key);
    }

    //
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $data = array();
        $data['title'] = 'Gestion de menu';
        $data['types'] = UserType::whereDeleted(0)->where('id', '<>', UserType::$_ADMIN)->get();
        $data['menus'] = Menu::with(['submenu'])->get();
        return view('menuManagement.form', $data);
    }

    public function save(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        $userTypesId = UserType::whereDeleted(0)->pluck('id');
        foreach ($request->except('_token') as $submenu_ => $types) {
            $permission = [];
            $submenu = Submenu::find(str_replace('submenu_', '', $submenu_));
            foreach ($userTypesId as $userTypeId) {
                $permission[$userTypeId] = 0;
                if (isset($types['type_' . $userTypeId])) {
                    $permission[$userTypeId] = 1;
                }
            }
            $submenu->permission = serialize($permission);
            $submenu->save();
        }
        //Delete cache in sidebar
        Sidebar::deleteCache();
        return redirect('/menuConfiguration');
    }
}
