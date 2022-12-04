<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CloudMessaginService;
use App\Http\Controllers\RealTimeService;
use Illuminate\Http\Request;
use App\Models\Message as MessageModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use App\Http\Resources\ListChatCollection;
use App\Http\Resources\ChatCollection;
use App\Http\Resources\ChatResource;
use App\Http\Controllers\Core\Chat as ChatCore;
use Validator;

enum MessageType{
    case text;
    case image;
    case video;
    case file;
    case audio;
}

class Chat extends Controller
{
    private $cloudMessage;

    private $realTime;

    private $chatCore;

    public function __construct(){
        $this->cloudMessage = new CloudMessaginService();
        $this->realTime = new RealTimeService();
        $this->chatCore = new ChatCore();
    }

    public function getOrCreateGroupMessage(Request $request){
        $user = $request->user();
        $rule = [
            'user_id' => 'required',            
        ];
        $messages = [
            'user_id.required' => 'Đối tượng khôngg được bỏ trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $findGroupMessage = $this->chatCore->getOrCreateGroupMessage($request, $iser);
        if(isset($findGroupMessage))
            return APIResponse::SUCCESS(['room' => $findGroupMessage]);
        return APIResponse::FAIL(['room' => 'Không thể tìm thấy phòng chat']);
    }
    //get list group
    public function getListMessage(Request $request){
        $user = $request->user();
        $listGroup = $this->chatCore->getListGroup($request, $user);
        return APIResponse::SUCCESS(new ListChatCollection($listGroup));
    }
    //get list message in room
    public function getCurrentMessages(Request $request){
        $user = $request->user();
        $rule = [
            'id' => 'required'
        ];
        $messages = [
            'id.required' => 'ID không được bỏ trống'
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $data = $this->chatCore->getListMessage($request);
        return APIResponse::SUCCESS(new ChatCollection($data));
    }
    //get message by uuid
    public function getDetailMessage(Request $request){
        $user = $request->user();
        $rule = [
            'UUID' => 'required'
        ];
        $messages = [
            'UUID.required' => 'Không tìm thấy ID tin nhắn'
         ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $data = $this->chatCore->getDetailMessage($request->UUID);
        if(!isset($data)) return APIResponse::FAIL(['id' => ["Không tìm thấy ID tin nhắn"]]);
        return APIResponse::SUCCESS(new ChatResource($data));
    }

    public function sendMessage(Request $request){
        $user = $request->user();
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
        $uuid = $this->chatCore->sendMessage($request, $user);
        $users = $this->chatCore->getListUserInGroup($request->id);
        $this->realTime->sendMessageInChat(
            $request->application_id, 
            $request->id, 
            $users, 
            $user, 
            $uuid
        );
        return APIResponse::SUCCESS(['UUID' => $uuid]);
    }
}
