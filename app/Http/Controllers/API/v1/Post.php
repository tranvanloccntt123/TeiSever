<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UUID;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User as UserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;

use Validator;

enum MessageType{
    case text;
    case image;
    case video;
    case file;
    case audio;
}
class Post extends Controller
{
    //
    public function create(Request $request){
        $rule = [
            "content" => "required",
            "application_id" => "required"
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
            'content.required' => 'Nôị dung không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $UUID = UUID::guidv4();
        if($request->has('media')){
            $path = 'post'; 
            $name = $UUID.'.jpg';
            $request->file('media')->storeAs($path, $name);
        }
        PostModel::create(['user_id' => $user->id, 'content' => $request->content, 'UUID' => $UUID, 'type' => $request->has('media')? 'image' : '', 'media' => $request->has('media')? $path.'/'.$name : '']);
        return APIResponse::SUCCESS(['UUID' => $UUID]);
    }

    public function delete(Request $request){
        $rule = [
            "application_id" => "required",
            'uuid' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
            'uuid.required' => 'UUID is không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $find = PostModel::where('UUID', 'LIKE', $request->uuid)->first();
        if(isset($find)) $find->delete();
        return APIResponse::SUCCESS(['post' => 'Xóa thành công']);
    }

    public function list(Request $request){
        $user = $request->user();
        $user_id = $user->id;
        if($request->has('user_id'))
            $user_id = $request->user_id;
        $findUser = UserModel::find($user_id);
        if(!isset($findUser)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $data = PostModel::where('user_id', '=', $user_id)->leftJoin('users','users.id', '=', 'posts.user_id')->select('posts.*', 'users.avatar', 'users.name', 'users.background');
        if($request->has('left_id'))
            $data = $data->where('id', '>', $request->left_id);
        return APIResponse::SUCCESS($data->paginate(15));
    }
}
