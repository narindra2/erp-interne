<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SortableController extends Controller
{
    /** Sortable test */
    public function index()
    {
        $status = $this->_data_status();
        return view("sortable.index" , ["status"=>$status]);
    }

    private  function _data_status()
    {
        /* Ito no izy
         $status = Status::whereDeleted(0)->orderBy("order" , ASC)->get(); mila cree-na le colunm "order" ao am  table "status" default "0"
        */
        $status = [
            ["id" => 1 ,"name" => "status 1" , "entity" => "admin,m2p,dessi,urba,client" ],
            ["id" => 2 ,"name" => "status 2" , "entity" => "admin,m2p,dessi,client" ],
            ["id" => 3 ,"name" => "status 3" , "entity" => "adminclient" ],
            ["id" => 4 ,"name" => "status 4" , "entity" => "admin,dessi,urba,client" ]
        ];
        return  $status ;
    }
    public  function update_status_ordering(Request $request)
    {
        $new_ordering =   $request->new_ordering; //  array ids of status order example = [33 , 55 ,66 ,4 ,12 ,22, 8]
        $i = 1;
        foreach ($new_ordering as  $id) {
            $i++;
        }
        /* Ito no izy
        foreach ($new_ordering as  $id) {
            Status::where("id", $id)->update(["order" => $i]); // mila cree-na le colunm "order" ao am  table "status" default "0"
            $i++;
        }
        */
        return [ "success" =>true , "message" => "Mise a jour ok "];
    }
}
