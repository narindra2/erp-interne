<tr>
    <td>
       <a href="{{ $file->uri }}" download>{{ $file->originale_name }}</a> <br>
        <i><span class="text-gray-400 mt-1 fw-semibold ">Par : {{ $file->uploader->sortname }} , {{ convert_to_real_time_humains($file->created_at) }}</span><i>
    </td>
    <td class="pe-0 text-center min-w-200px">  @if (auth()->id() == $file->uploader->id ) <span style="cursor: pointer"> <i class="fas fa-trash" ></i></span> @endif </td>
    <td class="pe-0 text-end min-w-200px"><a href="{{ $file->uri }}" download> <i class="fas fa-cloud-download-alt text-primary" title="Télécharger"></i></a></td>
</tr>
&nbsp;