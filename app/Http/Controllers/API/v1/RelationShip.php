<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RelationShip as RelationShipModel;
use App\Models\User as UserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;

class RelationShip extends Controller
{
    //
    public function create(Request $request){
        $rule = [
            'application_id' => 'required',
            'friend' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
            'friend.required' => 'Đối tượng không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $checkUserApplication = User::find($friend);
        if(!isset($checkUserApplication) || $checkUserApplication->application_id != $request->appication_id) return APIResponse::FAIL(['friend' => ['Không tìm thấy đối tượng']]);
        
    }
}
