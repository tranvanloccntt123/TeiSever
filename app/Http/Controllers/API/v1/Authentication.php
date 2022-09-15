<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User as UserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
use Auth;
class Authentication extends Controller
{
    public function createAccount(Request $request){
        $rule = [
            'username' => 'required',
            'name' => 'required',
            'application_id' => 'required',
            'password' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống.',
            'name.required' => 'Bạn chưa điền họ và tên',
            'password.required' => 'Mật khẩu không được để trống',
            'username.required' => 'Bạn chưa điền tên đăng nhập'
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $checkUsername = UserModel::where('username','LIKE', $request->application_id.'.'.$request->username)->first();
        if(isset($checkUsername))
         return APIResponse::FAIL(['username' => ['Tài khoản này đã được đăng kí']]);

        UserModel::create([
            'name' => $request->name,
            'username' => $request->application_id.'.'.$request->username,
            'application_id' => $request->application_id,
            'email' => $request->has('email')? $request->email : null,
            'password' => bcrypt($request->password),
        ]);
        return APIResponse::SUCCESS('Tài khoản đã được đăng kí');
    }

    public function  createToken(Request $request){
        $rule = [
            'username' => 'required',
            'password' => 'required',
            'application_id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống.',
            'password.required' => 'Mật khẩu không được để trống',
            'username.required' => 'Bạn chưa điền tên đăng nhập'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        if(Auth::attempt(['username' => $request->application_id.'.'.$request->username, 'password' => $request->password, 'application_id' => $request->application_id])){
            $token = Auth::user()->createToken('authToken')->plainTextToken;
            return APIResponse::SUCCESS([
                'token' => $token
            ]);
        }
        return APIResponse::FAIL(['sign up' => ['Đăng nhập thất bại']]);
    }
}
