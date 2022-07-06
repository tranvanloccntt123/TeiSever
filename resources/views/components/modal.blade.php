<!-- Button trigger modal -->
<button type="button" class="{{$classOpenButton}}" data-bs-toggle="modal" data-bs-target="#{{$id}}">
    {{$title}}
</button>
<form action="{{$action}}" method="{{$method}}" enctype="{{$enctype}}">
    <!-- Config slot element -->
    @props(['footer'])

    <!-- Modal -->
    <div class="modal fade" id="{{$id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="{{$id}}Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="{{$id}}Label">{{$modalTitle}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            {{ $slot }}
            </div>
            @isset($footer)
                <div class="modal-footer">        
                    {{$footer}}    
                </div>
            @endisset

        </div>
        </div>
    </div>
</form>