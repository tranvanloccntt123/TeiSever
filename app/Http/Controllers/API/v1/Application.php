<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application as ApplicationModel;
use App\Models\SelectModule as SelectModuleModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
class Application extends Controller
{
    //
    public function create(Request $request){
        $rule = ['name' => 'required|max:255', 'type_id' => 'required|numeric'];
        $messages = [
            'name.required' => 'Tên không được bỏ trống',
            'name.max:255' => "Độ dài của tên lớn nhất là 255 kí tự",
            "type_id.required" => "Loại ứng dụng không được bỏ trống",
            "type_id.numeric" => "Loại ứng dụng không tồn tại"
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        ApplicationModel::insert(['name' => $request->name, 'price' => $request->has("price")? $request->price : 0, 'type_id' => $request->type_id]);
        return APIResponse::SUCCESS("Ứng dụng đã được đăng kí", true);
    }

    public function delete(Request $request){
        $rule = ['id' => 'required'];
        $messages = [
            'id.required' => 'ID không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        ApplicationModel::find($request->id)->delete();
        return APIResponse::SUCCESS("Ứng dụng đã được xóa");
    }

    public function detail($id){
        $application = ApplicationModel::find($id);
        if(!isset($application)) return APIResponse::FAIL(['application' => ["Không thể truy cập ứng dụng"]]);
        return APIResponse::DATA($application);
    }

    public function edit(Request $request){
        if(!$request->has("id")) return APIResponse::FAIL(['application' => ["Không thể truy cập ứng dụng"]]);
        $detail = ApplicationModel::find($request->id);
        $detail->type_id = $request->has("type_id")? $request->type_id : $detail->type_id;
        $detail->note = $request->has("note")? $request->note : $detail->note;
        if($detail->note == NULL) $detail->note = "";
        $detail->save();
        SelectModuleModel::where("application_id", "=", $request->id)->delete();
        return APIResponse::SUCCESS("Ứng dụng cập nhật thành công");
    }
    
}
