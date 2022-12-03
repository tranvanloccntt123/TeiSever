<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User as UserModel;
use App\Models\TokenDevice as TokenDeviceModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
use Auth;
use DB;

class TokenDevice extends Controller
{
    //
    public function sendTokenDevice(Request $request){
        $rule = [
            'token' => 'required',            
        ];
        $messages = [
            'token.required' => 'Không lấy được định danhh'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $findToken = TokenDeviceModel::where('user_id', '=', $user->id)->where('devices', 'LIKE', $token)->first();
        if(isset($findToken)) return APIResponse::FAIL(['device' => 'Đăng ký thiết bị thành công']);
        TokenDeviceModel::insert(['user_id' => $user->id, 'devices' => $token]);
        return APIResponse::FAIL(['device' => 'Đăng ký thiết bị thành công']);
    }

    public function clearTokenDevice(Request $request){
        $rule = [
            'token' => 'required',            
        ];
        $messages = [
            'token.required' => 'Không lấy được định danhh'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        TokenDeviceModel::where('user_id', '=', $user->id)->where('device', 'LIKE', $token)->delete();
        return APIResponse::FAIL(['device' => 'Xóa thiết bị thành công']);
    }
}
