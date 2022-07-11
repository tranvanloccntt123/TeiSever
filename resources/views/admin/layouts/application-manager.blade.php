<form action="{{route('m.applications.edit.submit')}}" method="POST" class="mb-3  clearfix" style="border-bottom: 1px solid lightgray">
    @csrf
    <input type="hidden" name="id" value="{{$data['app']['id']}}">
    <div class="row">
        <div class="mb-3 col-12 col-md-6">
            <label class="form-label" for="edit-app-name">Tên ứng dụng</label>
            <input disabled type="text" name="name" class="form-control" id="edit-app-name" value="{{$data['app']['name']}}">   
        </div>
        <div class="mb-3 col-12 col-md-6">
            <label class="form-label" for="edit-app-name">Loại ứng dụng</label>
            <select name="type_id" class="form-select" aria-label="Loại ứng dụng">
                @foreach ($data["types"] as $item)   
                    @if ($item["id"] == $data['app']['type_id'])
                        <option value='{{$item["id"]}}' selected >{{$item["flag"]}}</option>
                    @else
                        <option value='{{$item["id"]}}' >{{$item["flag"]}}</option>
                    @endif
                @endforeach
            </select> 
        </div>
    </div>
    <div class="mb-3">
        <label for="edit-note">Note</label>
        <textarea id="edit-note" name="note" class="form-control">{{$data["app"]["note"]}}</textarea>
    </div>
    <button class="btn btn-primary float-end mb-3">Cập nhật</button>
</form>

<div class="col-12">
    <form action="{{route('m.module.select.submit')}}" method="POST">
        @csrf
        <input type="hidden" name="id" value="{{$data['app']['id']}}">
        <div class="mb-3">
            <label for="" class="form-label">Tính năng trong ứng dụng</label><br />
            <div class="row">
                @foreach ($data["modules"] as $item)
                    <div class="col-6 col-md-4 col-lg-2">
                        <input id="module-{{$item['id']}}" type="checkbox" name="module_id[]" value="{{$item['id']}}"> <label>{{$item["name"]}}</label>
                    </div>
                @endforeach
            </div>
        </div>
        <button class="btn btn-primary float-end">Cập nhật tính năng</button>
    </form>
</div>

<script>
    let selected = JSON.parse('<?php echo json_encode($data['select_modules']) ?>');
    if(selected){
        for(let i = 0; i < selected.length; i++)
        {
            let element = document.getElementById(`module-${selected[i].id}`);
            element.checked = "checked";
        }
    }
</script>