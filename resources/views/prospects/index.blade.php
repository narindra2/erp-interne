<x-base-layout>
    <div class="col-xxl-12 h-xl-100" id="category-section">
        <div class="card shadow-sm  mb-3 mb-xl-1">
            <div class="card-body py-5">
                <div class="d-flex justify-content-end mb-5">
                    <div class="filter-datatable">
                        @include('filters.filters-basic', ["inputs" => $basic_filter ,"filter_for" => "prospectTable"])
                    </div>
                    &nbsp; &nbsp;
                    <div class="me-4 my-2 ml-3">
                        <div class="d-flex align-items-center position-relative my-1">
                            <input type="text" id="search-prospect" autocomplete="off"
                                class="form-control form-control-solid form-select-sm w-200px ps-9 "
                                placeholder="{{ trans('lang.search') }}">
                        </div>
                    </div>
                    <div class="me-4 my-2">
                        <a id="do-reload" title="Recharger"
                            class="btn btn-sm btn-outline btn-outline-dashed btn-outline-default">
                            <i class="fas fa-sync-alt" style="width: 10px;"></i>
                        </a>
                    </div>
                    <div class="me-4 my-2 ml-3">
                        @php
                        echo modal_anchor(url('/prospect/prospect-modal-form'), '<i class="fas fa-plus"></i> Ajouter ',
                        ['title' => "Ajouter", 'class' => 'btn btn-sm btn-light-info' , "data-modal-lg" => true]);
                        @endphp
                    </div>
                </div>

                <table id="prospectTable" class="table table-row-dashed table-row-gray-200 align-middle  table-hover ">
                </table>
            </div>
        </div>
    </div>
    <!--end::Col-->
    @section('scripts')
    <script>
        $(document).ready(function() {
           
            function format(d) {
                return (
                    '<dl>' +
                    '<dt>Full name:</dt>' +
                    '<dd>' +
                    d.created_at +
                    '</dd>' +
                    '<dt>Extension number:</dt>' +
                    '<dd>' +
                    d.created_at +
                    '</dd>' +
                    '<dt>Extra info:</dt>' +
                    '<dd>And any further details here (images etc)...</dd>' +
                    '</dl>'
                );
            }
            dataTableInstance.prospectTable = $("#prospectTable").DataTable({
                processing: true,
                dom : "tpr",
                columns: [ 
                    {className: 'dt-control', orderable: false, data: null, defaultContent: ''},
                    {data :"company" , title: 'Nom de la société '},
                    // {data :"company_contact" , title: 'Contact'},
                    {data :"manager" , title: 'Dirigeant'},
                    {data :"site" , title: 'Site web'},
                    {data :"prospect" , title: 'Prospect'},
                    {data :"created_at" , title: 'Date de recherche'},
                    {data :"updated_at" , title: 'Date de traitement'},
                    {data: "status", title: "Statut"},
                    {data :"actions"},
                ],
                ajax: {
                    url: url("/prospect/data-list"),
                    data: function(data) {
                        <?php foreach(inputs_filter_datatable($basic_filter) as $input ) { ?>
                            data.{{ $input }} = $("#{{ $input }}").val();
                        <?php } ?>
                    }
                },
                language: {
                    url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
                },
                initComplete: function(settings, json) {
                    KTApp.initBootstrapTooltips();
                    
                }
            });
            dataTableInstance.prospectTable.on('click', 'td.dt-control', function (e) {
                let tr = e.target.closest('tr');
                let row = table.row(tr);
            
                if (row.child.isShown()) {
                    // This row is already open - close it
                    row.child.hide();
                }
                else {
                    // Open this row
                    row.child(format(row.data())).show();
                }
            });
            $(document).on("click" ,".copyElement",function(){
                        let _this = $(this);
                       
                        let text = _this.prev('span').text();
                        
                            try {
                                navigator.clipboard.writeText(text);
                                toastr.options.closeMethod = 'fadeOut';
                                toastr.success("Copié");
                                _this.addClass("text-success")

                            } catch (err) {
                                _this.addClass("text-danger")
                                toastr.error("Erreur : " +  err);
                            }
                            setTimeout(() => {
                                _this.removeClass("text-success")
                            }, 2000);
                        });
            $('#search-prospect').on('keyup', function() {
                dataTableInstance.prospectTable.search(this.value).draw();
            });
            $('#do-reload').on('click', function(e) {
                dataTableInstance.prospectTable.ajax.reload();
                setTimeout(() => {
                    KTApp.initBootstrapTooltips();
                }, 1000);
            });
            $('.prospectTable').on('change', function() {
                dataTableInstance.prospectTable.ajax.reload();
                setTimeout(() => {
                    KTApp.initBootstrapTooltips();
                }, 1000);
            });
        });
    </script>
    @endsection
</x-base-layout>