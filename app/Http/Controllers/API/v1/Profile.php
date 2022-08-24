<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User as UserModel;
class Profile extends Controller
{
    //
    public function viewProfile(Request $request){
        $rule = [
            'application_id' => 'required',
            'user_id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
            'user_id.required' => 'Đối tượng không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $checkUserApplication = User::find($friend);
        if(!isset($checkUserApplication) || $checkUserApplication->application_id != $request->appication_id) return APIResponse::FAIL(['friend' => ['Không tìm thấy đối tượng']]);
        $profile = UserModel::where('application_id', '=', $request->applicationn_id)->where('id', '=', $request->user_id)->first();  
        return APIResponse::SUCCESS([
            'profile' => $profile
        ]);
    }
}
