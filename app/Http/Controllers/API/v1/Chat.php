<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User as UserModel;
use App\Models\Message as MessageModel;
use App\Models\GroupMessage as GroupMessageModel;
use App\Models\GroupMessageUser as GroupMessageUserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use App\Http\Resources\ListChatCollection;
use App\Http\Resources\ListChatResource;
use App\Http\Resources\ChatCollection;
use App\Http\Resources\ChatResource;
use Validator;
use Auth;
class Chat extends Controller
{

    public function getListMessage(Request $request){
        $rule = [
            'application_id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(["Không tìm thấy thông tin của người dùng"]);
        $listGroup = GroupMessageUserModel::where('group_message_user.user_id','=',$user->id)
            ->join('group_message','group_message.id', '=', 'group_message_user.group_message_id')
            ->join('users','users.id','=','group_message_user.user_id')
            ->select('group_message_user.id', 'group_message_user.user_id', 'group_message_user.group_message_id', 'users.name as userName', 'group_message.name as groupName', 'group_message_user.created_at', 'group_message.updated_at')
            ->paginate(15);
        $collection = $listGroup->getCollection()->map(function ($item, $key) use ($user) {
            if($item->groupName != '') return $item;
            $findUserInGroup = GroupMessageUserModel::where('user_id', '<>', $user->id)->get();
            if(count($findUserInGroup) != 1) return $item;
            $findCurrentUser = UserModel::find($findUserInGroup[0]->user_id);
            if(!isset($findCurrentUser)) return $item;
            $item['userName'] = $findCurrentUser['name'];
            $item['groupName'] = $findCurrentUser['name'];
            return $item;
        });
        $listGroup->setCollection($collection);
        return new ListChatCollection($listGroup);
    }

    public function getCurrentMessages(Request $request){
        $rule = [
            'application_id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(["Không tìm thấy thông tin của người dùng"]);
        $data = MessageModel::orderBy('created_at', 'DESC')->leftJoin('users','users.id', '=', 'messages.user_id')->select('messages.*', 'users.name as userName')->paginate(15);
        return APIResponse::SUCCESS(new ChatCollection($data));
    }

    public function getDetailMessage(Request $request){
        $rule = [
            'application_id' => 'required',
            'id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
            'id.required' => 'Không tìm thấy ID tin nhắn'
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(["Không tìm thấy thông tin của người dùng"]);
        $data = MessageModel::join('users', 'users.id', '=', 'messages.user_id')->select('messages.*', 'users.name as usertName')->find($request->id);
        if(!isset($data)) return APIResponse::FAIL(['id' => "Không tìm thấy ID tin nhắn"]);
        return APIResponse::SUCCESS(new ChatResource($data));
    }
}
