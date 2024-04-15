<button type="button" data-backdrop="static" data-keyboard="false" id="trigger-ajax-modal" data-bs-toggle="modal" data-bs-target="#ajax-modal"
    style="display: none"></button>
<div class="modal fade"  data-bs-backdrop='static' tabindex="-1" id="ajax-modal" >
    <div id="modal-dialog" class="modal-dialog modal-dialog-centered modal-width">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="ajax-modal-title"></h4>

                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal"
                    aria-label="Close">
                    <span class="svg-icon svg-icon-2x"><i class="fas fa-times"></i></span>
                </div>
            </div>
            <div class="modal-body" id="ajax-modal-callback" style="display: none">

            </div>
            <div class="modal-body modal-body-content" id="ajax-modal-content">
                    
            </div>
            <div id='ajax-modal-original-content' class='center-align'>
                <div class="original-modal-body ">
                    <div class="d-flex justify-content-center">
                        <div class="spinner-border text-primary" role="status">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
