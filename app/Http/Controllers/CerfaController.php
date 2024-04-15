<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectCerfaRequest;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Project;
use Auth;
use Exception;
use Illuminate\Http\Request;

class CerfaController extends Controller
{
    public function index()
    {
        return view('cerfa.index');
    }

    public function data_list()
    {
        $data = [];
        $customers =  Customer::whereDeleted(0)->get();
        foreach ($customers as $customer) {
            $data[] = $this->_make_row($customer);
        }
        return ["data" => $data];
    }

    public function _make_row($customer)
    {
        $id = $customer->id;
        $row = [
            'DT_RowId'=> row_id("customers",$customer->id),
            'customer_name' => $customer->fullname,
            'email' => $customer->email,
            'type' => $customer->type->name,
            "action" => anchor(url("/cerfa/form/$id"), '<i class=" text-hover-primary fas fa-edit" style="font-size:15px"></i>', ["class" => "btn btn-sm btn-clean "])
            ." " .modal_anchor(url("/cerfa/customer/delete-modal"), '<i class=" text-hover-danger fas fa-trash " style="font-size:12px" ></i>', ['title' => trans('lang.delete'), 'data-post-customer_id' => $id])
        ];
        return $row;
    }

    public function customerProjectForm(Customer $customer)
    {
        $customer_types = CustomerType::dropdown();
        $civilities = Customer::$civility;
        $address_option = Project::$address_project_declarant;
        $data = [];
        $data['customer_id'] = $customer->id || "";
        $projects = Project::where("customer_id",$customer->id)->first();
        return view('cerfa.cerfa-form', $data, compact('customer',"customer_types","projects","civilities","address_option"));
    }

    public function store(StoreProjectCerfaRequest $request, Customer $customer, Project $project)
    {
        try {
            $society_data = [];
            $customer_address_data = [];
            if($request["customer_type"] == 2){
                $society_data = [
                    'denomination' => $request->denomination,
                    'social_reason' => $request->social_reason,
                    'society_type' => $request->society_type,
                    'siret_number' => $request->siret_number,
                ];
            }

            if($request["address"] == 1){
                // $customer_address_data = $request->only("c_way_number","c_locality", "c_postal_code", "c_town");
                $customer_address_data = [
                    'c_way_number' => $request->c_way_number,
                    'c_locality' => $request->c_locality,
                    'c_postal_code' => $request->c_postal_code,
                    'c_town' => $request->c_town,
                ];
            }

            $customer = Customer::updateOrCreate([
                'id' => $request->customer_id
            ], [
                'civility' => $request->civility,
                'lastname' => $request->lastname,
                'firstname' => $request->firstname,
                'birthday_place' => $request->birthday_place,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'customer_type_id' =>  $request->input("customer_type"),
                'birthday' =>  to_date($request->input("birthday")),
                'birthday' =>  to_date($request->input("birthday")),
            ] + $society_data + $customer_address_data);

            Project::updateOrCreate([
                // 'id' => $request->project_id
                'id' => $request->project_id
            ],[
                'way_number' => $request->way_number,
                'locality' => $request->locality,
                'postal_code' => $request->postal_code,
                'town' => $request->town,
                'customer_id' => $customer->id,
            ]);

            return ["success" => true,"message" => "Enregistrement effectué"];
        }
        catch(Exception $e) {
            return ["success" => false,"message" => "Echec d'enregistrement: " . $e->getMessage()];
        }
    }

    public function delete_customer_modal(Request $request )
    {
        $customer= Customer::find($request->customer_id);
        return view("users.delete-user-modal",compact("customer"));
    }

    public function delete_customer(Request $request )
    {
        $auth = Auth::user();
        if (!$auth->isAdmin() && !$auth->isHR()) {
            return ["success" => false, "message" => "Accès refusé"];
        }
        $customer= Customer::find($request->customer_id);
        $customer->deleted = 1;
        if ($customer->save()) {
            return ["success" => true, "message" => trans("lang.success_deleted")];
        }else{
            return ["success" => false, "message" => trans("lang.error_occured")];
        }
    }

}
