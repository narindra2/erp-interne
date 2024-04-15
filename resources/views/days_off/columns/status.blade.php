<?php $status_dayoff = ""; 
   $class = "";
?>
@if ($is_canceled)
    <?php $status_dayoff = "is_canceled"; $class="warning" ?>
@elseif($status == "in_progress_dayoff" || $status == "in_progress" )
    <?php $status_dayoff = $status; $class="primary" ?>
@elseif($status == "finish_dayoff" || $status=="refused")
    <?php $status_dayoff = $status; $class="danger" ?>
@elseif($status == "validated")
    <?php $status_dayoff = $status; $class="success" ?>
@endif

<span class="badge badge-light-{{$class}} fw-bolder fs-8 px-2 py-1 ms-2">{{ trans("lang.{$status_dayoff}") }}</span>   
