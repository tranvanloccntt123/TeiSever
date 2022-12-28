<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Validator;
class Authentication extends Controller
{
    //
    public function signIn(){
        return view("admin.login");
    }

    public function signInSubmit(Request $request){
        $rule = [
            'username' => 'required',
            'password' => 'required',
        ];
        $messages = [
            'password.required' => 'Mật khẩu không được để trống',
            'username.required' => 'Bạn chưa điền tên đăng nhập'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return redirect()->route('sign-up')->withErrors($validator);
        if(Auth::guard('manager')->attempt(['username' => $request->username, 'password' => $request->password, 'role' => 0])){
            return redirect()->route('m.applications');
        }
    }
}
