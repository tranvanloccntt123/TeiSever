<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UUID;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Models\User as UserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Illuminate\Support\Facades\Storage;
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
        ];
        $messages = [
            'content.required' => 'Nội dung không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $UUID = UUID::guidv4();
        if($request->has('media')){
            $path = 'post'; 
            $name = $UUID.'.jpg';
            $request->file('media')->storeAs($path, $name);
        }
        PostModel::create(['user_id' => $user->id, 'content' => $request->content, 'UUID' => $UUID, 'type_media' => $request->has('media')? 'image' : '', 'media' => $request->has('media')? $path.'/'.$name : '']);
        return APIResponse::SUCCESS(['UUID' => $UUID]);
    }

    public function update(Request $request){
        $rule = [
            "uuid" => "required"
        ];
        $messages = [
            'uuid.required' => 'UUID không được bỏ trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $findPost = PostModel::where('UUID', 'LIKE', $request->uuid)->first();
        if(!isset($findPost)) return APIResponse::FAIL(["post" => "Không tìm thấy bài viết"]);
        if($request->has("content"))
            $findPost->content = $request->content;
        if($request->has('media')){
            $path = 'post'; 
            if($findPost->media != "")
                Storage::delete($findPost->media);
            $findPost->type_media = $request->has('media')? 'image' : '';
            $UUID = UUID::guidv4();
            $name = $UUID.'.jpg';
            $request->file('media')->storeAs($path, $name);
            $findPost->media = $path.'/'.$name;
        }
        $findPost->save();
        return APIResponse::SUCCESS(["post" => "Bài viết đã được cập nhật"]);
    }

    public function delete(Request $request){
        $rule = [
            'uuid' => 'required'
        ];
        $messages = [
            'uuid.required' => 'UUID is không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
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
        if(!isset($findUser) && $findUser->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $data = PostModel::where('user_id', '=', $user_id)->leftJoin('users','users.id', '=', 'posts.user_id')->select('posts.*', 'users.avatar', 'users.name', 'users.background');
        if($request->has('left_id'))
            $data = $data->where('posts.id', '>', $request->left_id);
        return APIResponse::SUCCESS($data->paginate(15));
    }

    public function getMedia(Request $request){
        $user = $request->user();
        $user_id = $user->id;
        if($request->has('user_id'))
            $user_id = $request->user_id;
        $findUser = UserModel::find($user_id);
        if(!isset($findUser) && $findUser->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        
    }
}
