@foreach ($files as $file)
    <span class="ml-2"><a href="{{ url('/purchases/download-file/') . '/' . $file->id }}" target="_blank"
            rel="noopener noreferrer"><img src="{{ asset(theme()->getMediaUrlPath() . 'svg/files/upload.svg') }}"
                alt="" data-toggle="tooltip" data-placement="bottom" title="{{ $file->filename }}" height="40px"
                width="40px"></a></span>
@endforeach
