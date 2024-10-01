<?php

namespace App\Http\Controllers;

use App\Models\ProspectCompany;
use App\Models\ProspectCompanyManager;
use Illuminate\Http\Request;

class ProspectController extends Controller
{
    // https://preview.keenthemes.com/metronic8/demo1/dashboards/delivery.html
    //https://preview.keenthemes.com/metronic8/demo1/dashboards/school.html
   public function index()
    {
        return view('prospects.index', ["basic_filter" => []] );
    }
   public function prospect_info_modal()
    {
        return view('prospects.modal-form', ["basic_filter" => []] );
    }
   public function save_prospect_info(Request $request )
    {
        $data_company = $request->only("name_company","tel_company","email_company","linkedin_company","site_company","size_company");
        $company =  ProspectCompany::updateOrCreate(["id" => $request->company_id] , $data_company);

        $data_company_manager = $request->only("name_manager","tel_manager","email_manager","site_manager");
        /** Check if a field of manegr has on value and od save ity if a filed is not empty */
        foreach ($data_company_manager as $key => $value) {
            if ($value) {
                $data_company_manager["company_id"] = $company->id;
                ProspectCompanyManager::updateOrCreate(["id" => $request->company_manager_id] , $data_company_manager);
                break;
            }
        }
    }
   public function data_list(Request $request )
    {
        $data = [];
        $prospects =  ProspectCompany::with(["managers"])->whereDeleted(0)->get();
        foreach ($prospects as $prospect) {
          $data[] = $this->_make_row($prospect);
        }
        return ["data" =>  $data ];
    }
   public function _make_row( ProspectCompany $prospect )
    {
        $row["company"] = view("prospects.columns.company-info",compact("prospect"))->render();
        $row["company_contact"] = view("prospects.columns.company-contact",compact("prospect"))->render();
        $row["prospect"] = "";
        $row["manager"] = view("prospects.columns.company-info",compact("prospect"))->render();
        $row["site"] = "";
        $row["linkedin"] = "";
        $row["site_mere"] = "";
        $row["created_at"] = $prospect->created_at->format("d-M-Y");
        $row["updated_at"] = $prospect->updated_at->format("d-M-Y");
        $row["status"] = "";
        $row["actions"] = "";
        return $row;
    }
}
