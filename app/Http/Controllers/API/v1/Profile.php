<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\UUID;
use App\Models\User as UserModel;
use App\Models\Post as PostModel;
use App\Models\RelationShip as RelationShipModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
class Profile extends Controller
{
    //
    public function viewProfile(Request $request){
        $user = $request->user();
        if(!isset($user) && $user->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $user_id = $request->has('user_id')? $request->user_id : $request->user()->id;
        $checkUserApplication = UserModel::find($user_id);
        if(!isset($checkUserApplication) || $checkUserApplication->application_id != $request->application_id) return APIResponse::FAIL(['friend' => ['Không tìm thấy đối tượng']]);
        $posts = PostModel::where('user_id', '=', $user_id)->count();
        $friends = RelationShipModel::where('user_id', '=', $user_id)->where('status', '=', $this->getStatusConfirm())->count();
        return APIResponse::SUCCESS([
            'profile' => $checkUserApplication,
            'posts' => $posts,
            'friends' => $friends
        ]);
    }

    public function changeAvatar(Request $request){
        $rule = [
            'avatar' => 'required'
        ];
        $messages = [
            'avatar.required' => 'Ảnh đại diện không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $path = 'avatar';
        $UUID = UUID::guidv4();
        $name = $UUID.".jpg";
        $request->file('avatar')->storeAs($path, $name);
        $user->avatar = $path.'/'.$name;
        $user->save();
        return APIResponse::SUCCESS([
            'avatar' => $path.'/'.$name
        ]);
    }

    public function updateDetail(Request $request){
        $user = $request->user();
        if(!isset($user) && $user->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        if($request->has('name'))
            $user->name = $request->name;
        if($request->has('email'))
            $user->email = $request->email;
        if($request->has('description'))
            $user->description = $request->description;
        $user->save();
        return APIResponse::SUCCESS([
            'user' => $user
        ]);
    }

    public function changeBackground(Request $request){
        $rule = [
            'background' => 'required'
        ];
        $messages = [
            'background.required' => 'Ảnh bìa không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $path = 'background';
        $UUID = UUID::guidv4();
        $name = $UUID.".jpg";
        $request->file('background')->storeAs($path, $name);
        $user->background = $path.'/'.$name;
        $user->save();
        return APIResponse::SUCCESS([
            'background' => $path.'/'.$name
        ]);
    }

    public function searchUser(Request $request){
        $rule = [
            'keyword' => 'required'
        ];
        $messages = [
            'keyword.required' => 'Keyword không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user) && $user->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $search = mb_strtolower(trim(request()->input('keyword')));
        $data = UserModel::whereRaw('LOWER(`name`) LIKE ? ',['%'.$search.'%'])->where('application_id', '=', $request->application_id)->get();
        return APIResponse::SUCCESS([
            'users' => $data
        ]);
    }
}
