<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ErpDocumentationController extends Controller
{
    //
    protected $_AGENCY_DESSINEO = "DESSINEO";
    protected $_AGENY_THIRTY_ONE = "THIRTY ONE";

    public function index() {
        $user = Auth::user();
        $agency = $this->_AGENCY_DESSINEO;
        if ($user->userJob) {
            if ($user->userJob->local == 2) $agency = $this->_AGENY_THIRTY_ONE;
        }
        return view('erp-documentation.index', ['basic_filter' => [], "agency" => $agency]);
    }

    public function data_list() {}

    public function form_modal() {}

    public function store() {}
}
