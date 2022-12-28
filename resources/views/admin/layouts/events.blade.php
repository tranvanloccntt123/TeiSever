<table class="table">
    <thead>
        <tr>
            <th>STT</th>
            <th>Tiêu đề</th>
            <th>Mô tả</th>
            <th>Tham gia</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < count($data["events"]); $i++)
        <tr>
            <td>{{$i + 1}}</td>
            <td>{{$data["events"][$i]["title"]}}</td>
            <td>{{$data["events"][$i]["title"]}}</td>
            <td>
                <div style="display: flex">
                    <div class="avatar" style="margin-right: 15px">
                        <img class="w-px-40 h-auto rounded-circle" src="{{asset("storage/app/".$data["events"][$i]["avatar"])}}" >
                    </div>
                    {{$data["events"][$i]["name"]}}
                </div>
            </td>
            <td>
                <x-modal action="{{route('m.events.delete.submit')}}" method="POST" id='modal-delete-application-{{md5($data["events"][$i]["id"])}}' title="Xoá" modal-title="Hủy sự kiện" class-open-button="btn btn-danger" enctype="application/json">
                    <input type="hidden" name="id" value="{{$data["events"][$i]["id"]}}">
                    <input type="hidden" name="user_id" value="{{$data["events"][$i]["user_id"]}}">
                    <span><strong>Xoá Sự kiện</strong> sẽ không thể khôi phục lại</span>
                    <x-slot:footer>
                        <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">
                            Huỷ
                        </button>
                        <button type="submit" class="btn btn-danger">
                            Chấp thuận
                        </button>
                    </x-slot>
                </x-modal>
            </td>
        </tr>
        @endfor
    </tbody>
</table>