@if (0)
    {!! js_anchor('<i class="text-hover-primary fas fa-user-minus" style="font-size:15px" ></i>', ['data-post-ticket_id' => $ticket->id, 'data-post-user_id' => $user->id, 'data-action-url' => url('/project_member/delete'), 'class' => 'btn btn-sm btn-clean ', 'title' => 'delete', 'data-action' => 'delete']) !!}
@else
<div class="form-check form-check-custom form-check-solid ">
    <input class="form-check-input mx-5 user-item" title="Ajouter" style=" border: 1px solid; border-color: #009EBD;" @if (in_array($user->id,explode(",",$ticket->assign_to))) checked  @endif  type="checkbox" name="user_ids[]" value="{{ $user->id }}" />
</div> 
@endif


