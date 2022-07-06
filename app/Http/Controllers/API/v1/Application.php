<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application as ApplicationModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
class Application extends Controller
{
    //
    public function create(Request $request){
        $rule = ['name' => 'required|max:255', 'type_id' => 'required|numeric'];
        $messages = [
            'name.required' => 'Name is required',
            'name.max:255' => "Name's length is 255",
            "type_id.required" => "Type is required",
            "type_id.numeric" => "Type is numeric"
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        ApplicationModel::insert(['name' => $request->name, 'price' => $request->has("price")? $request->price : 0, 'type_id' => $request->type_id]);
        return APIResponse::SUCCESS("Application is created");
    }

    public function delete(Request $request){
        $rule = ['id' => 'required'];
        $messages = [
            'id.required' => 'ID is required',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        ApplicationModel::find($request->id)->delete();
        return APIResponse::SUCCESS("Application is deleted");
    }
}
