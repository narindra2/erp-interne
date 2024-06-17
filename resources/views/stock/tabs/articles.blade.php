<div class="card shadow-sm">
    <div class="d-flex justify-content-end mx-5 my-5">
        @php
            echo modal_anchor(url('/stock/article/modal-form'), '<button class="btn btn-light-info font-weight-bold mr-2 btn-sm"><i class="fas fa-plus "></i> Ajouter un nouveau article</button>', ['title' => 'Ajouter nouvel article'])
        @endphp
    </div>
    <div class="card-body">
    
        <table id="articlesDataTable" class="table table-row-dashed table-row-gray-200 align-middle gs-0 gy-4 table-hover ">
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        dataTableInstance.articlesDataTable = $("#articlesDataTable").DataTable({
            processing: true,
            // dom: "tpr",
            columns: [{
                    data: "name",
                    title: 'Nom'
                },
                {
                    data: "code",
                    title: 'Code article'
                },
                {
                    data: "category",
                    title: 'Catégorie'
                },
                {
                    data: "actions",
                    searchable: false,
                    orderable: false
                },
            ],
            ajax: {
                url: url("/stock/article/data-list"),
            },
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.11.3/i18n/fr_fr.json"
            },
        });
        $('#search-articles').on('keyup', function() {
                dataTableInstance.articlesDataTable.search(this.value).draw();
       });
    });
</script>
