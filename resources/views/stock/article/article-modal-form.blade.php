<form class="form" id="articleForm" method="POST" action="{{ url("/stock/article/save") }}">
    <div class="card-body ">
        @csrf
        <input type="hidden" name="id" value="{{ $article->id }}">
        @if (!$article->id)
            <div class="alert alert-danger d-flex align-items-center p-5">
                <div class="d-flex flex-column">
                    <h4 class="mb-1 text-danger">A lire</h4>
                    <span>Pour éviter le doublon ,rassurez-vous que le nom de l'article que vous allez ajouter ici n'est pas encore enregistré.</span>
                </div>
            </div> 
        @endif
       
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Nom de l'article</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="name" placeholder="Nom de l'article ... " data-rule-required="true"  data-msg-required="@lang('lang.required_input')" autocomplete="off" value="{{ $article->name }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Code de l'article</label>
            <div class="col-6">
                <input type="text" class="form-control form-control-sm form-control-solid" name="code" placeholder="Code de l'article ... "  autocomplete="off" value="{{ $article->code }}">
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Catégorie</label>
            <div class="col-6">
                <select id="category_id" name="category_id" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="false"  data-msg-required="@lang('lang.required_input')">
                    <option disabled  >-- Catégorie --</option>
                    <option selected value="non-definie"> Non définie</option>
                    @foreach ($categories as $categories)
                        <option value="{{ $categories->id }}"  @if ( $categories->id == $article->category_id  )  selected  @endif>{{ $categories->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group row mb-5">
            <label class="col-form-label col-4">Sous catégorie</label>
            <div class="col-6">
                <select id="sub_cat" name="sub_category" class="form-select form-select-sm form-select-solid" data-control="select2" data-dropdown-parent="#ajax-modal" data-rule-required="false"  data-msg-required="@lang('lang.required_input')">
                    <option disabled  >-- Sous categorie --</option>
                    <option  selected value="non-definie" > Non définie</option>
                    @foreach ($sub_cats as $sub_cat)
                        <option value="{{ $sub_cat }}"  @if ($article->sub_category == $sub_cat )  selected  @endif>{{ ucfirst($sub_cat)  }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex justify-content-end">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" class="btn btn-light-light btn-sm mr-2 "> @lang('lang.cancel')</button>
            <button type="submit" id="submit" class=" btn btn-sm btn-light-info  mr-2">
                @include('partials.general._button-indicator', ['label' => trans('lang.save'),"message" => trans('lang.sending')])
            </button>
        </div>
    </div>
</form>
<style>
    #modal-dialog{
        min-width: 700px;
    }
</style>
<script>
$(document).ready(function() {
    KTApp.initSelect2();
    KTApp.initBootstrapPopovers();
    $("#articleForm").appForm({
        onSuccess: function(response) {
            if (response.row_id) {
                dataTableUpdateRow(dataTableInstance.articlesDataTable, response.row_id,response.data) 
            }else{
                dataTableaddRowIntheTop(dataTableInstance.articlesDataTable ,response.data)
            }
        },
    });
})
</script>
