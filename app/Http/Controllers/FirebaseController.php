<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
class FirebaseController
{
    //
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function test(){
        $this->database->getReference('test/name')->set('New name');
    }
}

class CloudMessaginService{
    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function sendMessaging(){

    }

    public function test(){
        $message = CloudMessage::withTarget(/* see sections below */)
            ->withNotification(Notification::create('Title', 'Body'))
            ->withData(['key' => 'value']);
        
        $this->messaging->send($message);
    }
}