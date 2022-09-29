<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UUID;
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

enum MessageType{
    case text;
    case image;
    case video;
    case file;
    case audio;
}

enum DescriptionType{
    case lover;
    case brother;
    case mother;
    case father;
    case wife;
    case son;
    case daughter;
}

class Chat extends Controller
{
    public function getOrCreateGroupMessage(Request $request){
        $rule = [
            'user_id' => 'required',            
        ];
        $messages = [
            'user_id.required' => 'Đối tượng khôngg được bỏ trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $findGroupMessage;
        if($request->has('single')){
            $findGroupMessage = GroupMessageModel::where('user_id', '=', )
        }
    }

    public function getListMessage(Request $request){
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
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
        return APIResponse::SUCCESS(new ListChatCollection($listGroup));
    }

    public function getCurrentMessages(Request $request){
        $rule = [
            'id' => 'required'
        ];
        $messages = [
            'id.required' => 'ID không được bỏ trống'
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $data = MessageModel::leftJoin('users','users.id', '=', 'messages.user_id')->select('messages.*', 'users.name as userName')->where('group_message_id', '=', $request->id)->orderBy('created_at', 'DESC');
        if($request->has('left_id'))
         $data = $data->where('messages.id', '<', $request->left_id);
        return APIResponse::SUCCESS(new ChatCollection($data->paginate(15)));
    }

    public function getDetailMessage(Request $request){
        $rule = [
            'UUID' => 'required'
        ];
        $messages = [
            'UUID.required' => 'Không tìm thấy ID tin nhắn'
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $data = MessageModel::join('users', 'users.id', '=', 'messages.user_id')->select('messages.*', 'users.name as usertName')->where('UUID', 'LIKE',$request->UUID)->first();
        if(!isset($data)) return APIResponse::FAIL(['id' => ["Không tìm thấy ID tin nhắn"]]);
        return APIResponse::SUCCESS(new ChatResource($data));
    }

    public function sendMessage(Request $request){
        $rule = [
            'id' => 'required',
            'type' => 'required',
            'content' => 'required'
        ];
        $messages = [
            'id.required' => 'Không tìm thấy ID tin nhắn',
            'type.required' => 'Type không được bỏ trống',
            'content.required' => 'Nội dung tin nhắn không được bỏ trống' 
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $UUID = UUID::guidv4();
        switch($request->type){
            case MessageType::text->name: {
                MessageModel::create([
                    'type' => $request->type,
                    'user_id' => $user->id,
                    'group_message_id' => $request->id,
                    'content' => $request->content,
                    'UUID' => $UUID
                ]);
                break;
            }
            case MessageType::image->name: {
                $path = 'message'; 
                $name = $UUID.'.jpg';
                $request->file('content')->storeAs($path, $name);
                MessageModel::create([
                    'type' => $request->type,
                    'user_id' => $user->id,
                    'group_message_id' => $request->id,
                    'content' => $path.'/'.$name,
                    'UUID' => $UUID
                ]);
                break;
            }
        }
        return APIResponse::SUCCESS(['UUID' => $UUID]);
    }
}
