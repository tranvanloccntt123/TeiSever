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
        $rule = ['name' => 'required|max:255'];
        $messages = [
            'name.required' => 'Name is required',
            'name.max:255' => "Name's length is 255"
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        ApplicationModel::insert(['name' => $request->name, 'price' => $request->has("price")? $request->price : 0]);
        return APIResponse::SUCCESS("Application is created");
    }
}
