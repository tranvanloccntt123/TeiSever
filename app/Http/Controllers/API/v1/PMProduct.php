<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PMProduct as PMProductModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
class PMProduct extends Controller
{
    //
    public function create(Request $request){
        $rule = ['name' => 'required', "category_id" => "required"];
        $messages = [
            'name.required' => 'Tên sản phẩm không được bỏ trống',
            "category_id.required" => "Danh mục sản phẩm không được bỏ trống"
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        PMProductModel::insert([
            "name" => $request->name,
            "category_id" => $request->category_id,
            "price" => $request->has("price")? $request->price : 0,
            "description" => $request->has("description")? $request->description : 0,
            "options" => $request->has("options")? $request->options : ""
        ]);
        return APIResponse::SUCCESS("Sản phẩm đã được thêm");
    }
    public function get(Request $request){
        return PMProductModell::where("application_id",'=',$request->application_id)->get();
    }
    public function paginate(Request $request){
        return PMProductModell::where("application_id",'=',$request->application_id)->paginate(12);
    }
}
