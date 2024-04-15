<div class="d-flex  align-items-end gap-2 gap-lg-3">
    <div class="m-0">
        <a href="#" title ="Filtre avancé"  id="filters-advenced" class="btn btn-sm btn-flex btn-light btn-active-light fw-bolder menu-dropdown" data-kt-menu-trigger="click"
            data-kt-menu-placement="top-end">
            <span class="svg-icon svg-icon-5 svg-icon-gray-500 me-1">
                {!! theme()->getSvgIcon("icons/duotune/general/gen031.svg") !!}
            </span>
            Avancé
        </a>
        <div class="menu menu-sub menu-sub-dropdown w-200 w-lg-700px p-5 p-lg-5" data-kt-menu="true" id="dropdown-filter-custom">
            <div class="px-7 py-5">
                <div class="fs-5 text-dark fw-bolder">Combiner les filtres</div>
            </div>
            <div class="separator border-gray-200"></div>
            <div class="px-7 py-5">
                {!! view('suivis.crud.custom-filter-inputs', ['options' => $options])->render() !!}
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-sm btn-light btn-active-light-dark me-2" data-kt-menu-dismiss="true">Quitter</button>
                    <button type="submit" id="applic-custom-filter" class="btn btn-sm btn-active-light-primary">{{ isset($create) ? "Enregistrer" : "Appliquer sur le tableau" }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#applic-custom-filter').on('click', function(e) {
            dataTableInstance.suiviTable.ajax.reload();
            /*
            setTimeout(() => {
                $("#user_ids").val("0");
                $("#folder_ids").val("0");
                $("#type_project").val("0");
                $("#version_ids").val("0");
                $("#montage_ids").val("0");
                $("#status_ids").val("0");
            }, 1000);
             */
        });
    })
</script>
