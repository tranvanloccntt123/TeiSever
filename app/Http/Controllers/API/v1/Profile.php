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
        $rule = [
            'application_id' => 'required',
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user_id = $request->has('user_id')? $request->user_id : $request->user()->id;
        $checkUserApplication = UserModel::find($user_id);
        if(!isset($checkUserApplication) || $checkUserApplication->application_id != $request->application_id) return APIResponse::FAIL(['friend' => ['Không tìm thấy đối tượng']]);
        $posts = PostModel::where('user_id', '=', $user_id)->count();
        $friends = RelationShipModel::where('user_id', '=', $user_id)->where('status', '=', $this->getStatusConfirm())->count();
        $relationShips = -1;
        if($request->user()->id == $user_id) $relationShips = 3;
        else{
            $findRelationShip = RelationShipModel::where('user_id', '=', $request->user()->id)->where('friend', '=', $user_id)->first();
            if(isset($findRelationShip)) $relationShips = $findRelationShip->status;
        }
        return APIResponse::SUCCESS([
            'profile' => $checkUserApplication,
            'posts' => $posts,
            'friends' => $friends,
            'relation_ship' => $relationShips
        ]);
    }

    public function changeAvatar(Request $request){
        $rule = [
            'application_id' => 'required',
            'avatar' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống',
            'avatar.required' => 'Ảnh đại diện không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
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

    public function changeBackground(Request $request){
        $rule = [
            'application_id' => 'required',
            'background' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống',
            'background.required' => 'Ảnh bìa không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
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
}
