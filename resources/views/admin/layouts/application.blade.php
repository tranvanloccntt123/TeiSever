<div class="row">
    <div class="col-12">
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Ứng dụng</th>
                    <th>Loại ứng dụng</th>
                    <th class="row align-item-end">
                        <x-modal action="{{route('m.applications.create.submit')}}" method="POST" id="modal-add-application" title="Thêm" modal-title="ĐĂNG KÝ" enctype="application/json">
                            @csrf
                            <div class="mb-3">
                                <label for="create-app-label" class="form-label">Tên ứng dụng <span style="color: red">*</span></label>
                                <input name="name" type="text" class="form-control" id="create-app-name" placeholder="Product manager">
                            </div>
                            <div class="mb-3">
                                <label for="" class="form-label">Loại ứng dụng</label>
                                <select name="type_id" class="form-select" aria-label="Loại ứng dụng">
                                    @foreach ($data["types"] as $item)
                                        <option value='{{$item["id"]}}'>{{$item["flag"]}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <x-slot:footer>
                                <button type="button" data-bs-dismiss="modal" class="btn btn-secondary">
                                    Huỷ
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Chấp thuận
                                </button>
                            </x-slot>
                        </x-modal>
                    </th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < count($data['applications']); $i++)
                    <tr>
                        <td>{{$i + 1}}</td>
                        <td>{{$data['applications'][$i]["name"]}}</td>
                        <td>{{$data['applications'][$i]["flag"]}}</td>
                        <td>
                            <a href="{{route('m.application.query',['id' => $data['applications'][$i]['id']])}}" class="btn btn-link">Truy xuất</a>
                            <a href="{{route('m.application.docs',['id' => $data['applications'][$i]['id']])}}" class="btn btn-link">Tài liệu</a>
                            <x-modal action="{{route('m.applications.delete.submit')}}" method="POST" id="modal-delete-application-{{md5($data['applications'][$i]['id'])}}" title="Xoá" enctype="application/json" modal-title="Xoá ứng dụng" class-open-button="btn btn-danger" >
                                @csrf
                                <input type="hidden" name="id" value="{{$data["applications"][$i]["id"]}}">
                                <span><strong>Xoá ứng dụng</strong> sẽ không thể khôi phục lại ứng dụng</span>
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
    </div>
</div>