<div class="row">
    <div class="col-12">
        <table class="table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Ứng dụng</th>
                    <th class="row align-item-end">
                        <x-modal id="modal-add-application" title="Add" modal-title="Register Application">
                            @csrf
                            <div class="mb-3">
                                <label for="create-app-label" class="form-label">Name Application</label>
                                <input type="text" class="form-control" id="create-app-name" placeholder="Product manager">
                            </div>
                            <x-slot:footer>
                                <button class="btn btn-secondary">
                                    Cancel
                                </button>
                                <button class="btn btn-primary">
                                    Apply
                                </button>
                            </x-slot>
                        </x-modal>
                    </th>
                </tr>
            </thead>
            <tbody>
                @for ($i = 0; $i < count($data); $i++)
                    <tr>
                        <td>{{$i + 1}}</td>
                        <td>{{$data[$i]["name"]}}</td>
                        <td>
                            <button class="btn btn-link">Query</button>
                            <button class="btn btn-danger">Del</button>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>
</div>