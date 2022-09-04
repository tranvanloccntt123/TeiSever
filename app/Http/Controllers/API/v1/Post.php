<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UUID;
use Illuminate\Http\Request;
use App\Models\Post as PostModel;
use App\Http\Controllers\API\v1\Response as APIResponse;

use Validator;
class Post extends Controller
{
    //
    public function create(Request $request){
        $rule = ["content" => "required"];
        $messages = [
            'content.required' => 'Nôị dung không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $UUID = UUID::guidv4();
        PostModel::create(['user_id' => $user->id, 'content' => $request->content, 'uuid' => $UUID]);
        return APIResponse::SUCCESS(['UUID' => $UUID]);
    }

    public function list(Request $request){
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        return APIResponse::SUCCESS(PostModel::paginate(15));
    }
}
