<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Kreait\Firebase\Exception\Messaging\InvalidMessage;
class CloudMessaginService{
    public $tokens = [];

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function setTokens($tokens){
        $this->tokens = $tokens;
        return $this;
    }

    public function sendMessaging($title, $description, $data){
        try{
            $validateOnly = true;
            $message = CloudMessage::new();
            $message = $message->withNotification(Notification::create($title, $description))
                ->withData($data);
            $this->messaging->sendMulticast($message, $this->tokens, $validateOnly);
        } catch (InvalidMessage $e){
            print_r($e->errors());
        }
    }

    public function test(){
        $message = CloudMessage::withTarget('topic', 'messages')
            ->withNotification(Notification::create('Title', 'Body'))
            ->withData(['key' => 'value']);
        
        $this->messaging->send($message);
    }
}