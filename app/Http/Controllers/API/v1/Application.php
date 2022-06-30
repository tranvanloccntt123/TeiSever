<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application as ApplicationModel;
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
        if($validator->fails()) return [];
        ApplicationModel::insert(['name' => $request->name]);
        return [];
    }
}
