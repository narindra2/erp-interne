
@php
    $details = $purchase_model->details;
    // dump($purchase_model);
@endphp
<div class="d-flex flex-column flex-md-row rounded border p-2">
    <ul class="nav nav-tabs nav-pills flex-row border-0 flex-md-column me-5 mb-3 mb-md-0 fs-6 min-w-lg-200px" role="tablist">
        @foreach ($details as $detail)
            <li class="nav-item w-100 me-0 mb-md-2" role="presentation">
                <a class="nav-link w-100 btn btn-flex btn-active-light-info {{ $loop->index == 0 ? 'active ' : '' }}" data-bs-toggle="tab" href="#tab-{{ $loop->index + 1 }}" aria-selected="true" role="tab">
                    <i class="ki-duotone ki-icons/duotune/general/gen001.svg fs-2 text-info me-3"></i>                        <span class="d-flex flex-column align-items-start">
                        <span class="fs-7">{{  $detail->itemType->name }}</span>
                    </span>
                </a>
            </li>
            <div class="separator border-info"></div> 
        @endforeach
    </ul>

    <div class="tab-content" id="myTabContent">
        
        @foreach ($details as $detail)
            <div class="tab-pane fade {{ $loop->index == 0 ? 'active show' : '' }} " id="tab-{{ $loop->index + 1 }}" role="tabpanel"  style="">
                
                <table class="table  table-stock table-rounded table-row-bordered border "  >
                    <thead class=" ">
                        <tr class=" fw-bold fs-6  text-white bg-info text-center">
                            <th >{{  $detail->itemType->name }}</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Age</th>
                            <th>Start date</th>
                            <th>Salary</th>
                        </tr>
                    </thead>
                </table>
                <table class="table table-stock table-rounded table-row-bordered border "  >
                    <thead class=" ">
                        <tr class=" fw-bold fs-6  text-white bg-info text-center">
                            <th >{{  $detail->itemType->name }}</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Age</th>
                            <th>Start date</th>
                            <th>Salary</th>
                        </tr>
                    </thead>
                    <tbody >
                        <tr class="text-center">
                            <td></td>
                            <td>System Architect</td>
                            <td>Edinburgh</td>
                            <td>61</td>
                            <td>2011/04/25</td>
                            <td>$320,800</td>
                        </tr>
                       
                    </tbody>
                </table>
        </div>   
        @endforeach
       


    </div>
</div>

<style>
     .nav-link {
        display: block;
        padding: 0.5rem 1rem;
        color: rgb(59, 56, 56);
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
    }
    #modal-dialog{
        min-width: 980px;
    }
    #kt_vtab_pane_4{
        width: max-content!important;
    }
    .table-stock{
       width: 135% !important;
    }

</style>