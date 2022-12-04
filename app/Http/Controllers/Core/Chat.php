<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CloudMessaginService;
use App\Http\Controllers\UUID;
use Illuminate\Http\Request;
use App\Models\User as UserModel;
use App\Models\Message as MessageModel;
use App\Models\GroupMessage as GroupMessageModel;
use App\Models\GroupMessageUser as GroupMessageUserModel;
use App\Models\RoomMessageView as RoomMessageViewModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use App\Http\Resources\ListChatCollection;
use App\Http\Resources\ListChatResource;
use App\Http\Resources\ChatCollection;
use App\Http\Resources\ChatResource;
use Validator;
use Auth;
use DB;

enum MessageType{
    case text;
    case image;
    case video;
    case file;
    case audio;
}

class Chat extends Controller
{
    public function getListUserInGroup($groupId){
        $list = GroupMessageUserModel::where('group_message_id', '=', $groupId)->get();
        return $list;
    }
    //
    public function getOrCreateGroupMessage(Request $request, $user){
        $findGroupMessage;
        if($request->has('single')){
            $findGroupMessage = RoomMessageViewModel::where(function($query) use ($user, $request){
                    $query->where('group_message_user.user_id', '=', $user->id)
                    ->orWhere('group_message_user.user_id', '=', $request->user_id);
                })
                ->where('ROOM_MESSAGE.count_member', '=', 2)
                ->leftJoin('group_message_user', 'group_message_user.group_message_id', '=', 'ROOM_MESSAGE.id')
                ->select('ROOM_MESSAGE.*')
                ->groupBy('id', 'name', 'created_at', 'count_member', 'config', 'updated_at')
                ->first();
            if(!isset($findGroupMessage)){
                $groupId = GroupMessageModel::insertGetId(['name' => '']);
                GroupMessageUserModel::insert(['user_id' => $request->user_id, 'group_message_id' => $groupId]);
                GroupMessageUserModel::insert(['user_id' => $user->id, 'group_message_id' => $groupId]);
                $findGroupMessage = RoomMessageViewModel::where(function($query) use ($user, $request){
                    $query->where('group_message_user.user_id', '=', $user->id)
                    ->orWhere('group_message_user.user_id', '=', $request->user_id);
                })
                ->where('ROOM_MESSAGE.count_member', '=', 2)
                ->leftJoin('group_message_user', 'group_message_user.group_message_id', '=', 'ROOM_MESSAGE.id')
                ->select('ROOM_MESSAGE.*')
                ->groupBy('id', 'name', 'config', 'created_at',  'updated_at', 'count_member')
                ->first();
            }
        }
        return $findGroupMessage;
    }

    public function getListGroup(Request $request, $user){
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

        return $listGroup;
    }

    public function sendMessage(Request $request, $user){
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
        return $UUID;
    }

    public function getDetailMessage($uuid){
        $data = MessageModel::leftJoin('users','users.id', '=', 'messages.user_id')->select('messages.*', 'users.name as userName', 'users.avatar', 'users.background')
            ->where('UUID', 'LIKE',$uuid)
            ->first();
        return $data;
    }

    public function getListMessage(Request $request){
        $data = MessageModel::leftJoin('users','users.id', '=', 'messages.user_id')->select('messages.*', 'users.name as userName', 'users.avatar', 'users.background')->where('group_message_id', '=', $request->id)->orderBy('created_at', 'DESC');
        if($request->has('left_id'))
            $data = $data->where('messages.id', '<', $request->left_id);
        return $data->paginate(15);
    }
}
