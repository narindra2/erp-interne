<form action="{{ url('/prospect/save-prospect-info') }}" class="form" method="POST" id="save-prospect-info">
    <div class="card-body">
        @csrf
        <div class="separator separator-content border-info mb-2 "><span class="w-250px fw-bold">Information de la société </span></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Nom de la société  : </span>
                    <textarea id="note" name="name_company" placeholder="Nom de la société ...  "  autocomplete="off" class="form-control form-control form-control-solid" rows="2" data-kt-autosize="true" data-rule-required="fales"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Télèphone : </span>
                    <input type="text"  class="form-control  form-control-sm form-control-solid" autocomplete="off" name="phone_company" value="" placeholder="Télèphone .. ">
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">E-mail : </span>
                    <input type="email"  class="form-control  form-control-sm form-control-solid" autocomplete="off"  name="email_company" value="" placeholder="E-mail ....">

                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Linkedin : </span>
                    <textarea id="linkedin_company" name="linkedin_company" placeholder="Linkedin ... "  autocomplete="off" class="form-control form-control form-control-solid" rows="2" data-kt-autosize="true" data-rule-required="fales"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Site web : </span>
                    <textarea id="site_company" name="site_company" placeholder="Site web ... "  autocomplete="off" class="form-control form-control form-control-solid" rows="2" data-kt-autosize="true" data-rule-required="fales"></textarea>
                </div>
            </div>
           
        </div>
      
        <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">  Type de prospect: </span>
                    <textarea id="site_company"  placeholder="Type de prospect . "  autocomplete="off" class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">  Taille du société (Nombre des salariés): </span>
                    <textarea id="size_company" name="size_company" placeholder="Nombre des salairiés ...  "  autocomplete="off" class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales"></textarea>
                </div>
            </div>
        </div>
    
        <div class="separator separator-content border-info mt-3 mb-2 "><span class="w-250px fw-bold">Info du dirigeant </span></div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Nom de dirigeant : </span>
                    <input   type="text"  id="name_manager" name="name_manager" placeholder=" Non du dirigeant... "  autocomplete="off" class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales"/>
                </div>
            </div>
           
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">Tél du dirirgeant : </span>
                    <input   type="text"  id="tel_manager" name="tel_manager" placeholder=" Tél du dirirgeant ... "  autocomplete="off" class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6">E-mail du dirigeant : </span>
                    <input   type="email"  id="email_manager" name="email_manager" placeholder=" E-mail du dirigeant... "  autocomplete="off" class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales"/>
                </div>
            </div>
           
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card-title d-flex flex-column">   
                    <span class="text-gray-700 pt-1 fw-semibold fs-6"> Compte dirirgeant ou descisionnaire  : </span>
                    <textarea id="site_manager" name="site_manager" placeholder="Compte dirirgeant ou descisionnaire ... "  autocomplete="off" class="form-control form-control form-control-solid" rows="1" data-kt-autosize="true" data-rule-required="fales"></textarea>
                </div>
            </div>
          
           
        </div>
       
   </div>
<div class="d-flex justify-content-end mt-5" id="save-purchase">
        <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-dark btn-sm mr-2 ">
            Quitter          
        </button> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            
        <button type="submit" id="save-prospect-btn"  class="btn btn-info font-weight-bold mr-2 btn-sm ">Sauvegarder</button>
</div>
</form>


<style>
    #modal-dialog{
        min-width: 980px;
    }
    .form .form-control  {
        background-color: #F5F8FA;
        border-color: #F5F8FA;
        color: #7239ea !important;
        transition: color 0.2s ease, background-color 0.2s ease;
    }
    .form .select2-container--bootstrap5 .select2-selection--single.form-select-solid .select2-selection__rendered {
        color: #7239ea;
    }
    .nice-input-file{
        position: absolute;
        height: 37px;
        background: #5014D0;
        width: 123px;
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 6px 0 0 6px;
    }
</style>
<script>
    
    $(document).ready(function() {
        KTApp.initSelect2();
        $("#save-prospect-info").appForm({
            onSuccess: function(response) {
                dataTableInstance.purchasesTable.ajax.reload();
            },
        });
    });
</script>