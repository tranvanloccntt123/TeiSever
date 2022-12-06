<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Contract\Database;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
class RealTimeService
{
    public function __construct()
    {
        $this->database = app('firebase.database');
    }

    public function test(){
        $this->database->getReference('test/name')->set('New name');
    }

    public function getRootNotification($applicationId, $userId){
        return "application/user/".$applicationId."/".$userId;
    }

    public function push($root, $data){
        $this->database->getReference($root)->push($data);
    }

    public function sendMessageInChat($application_id, $room, $listUser, $user, $uuid){
        foreach ($listUser as $key => $value) {
            if($value->user_id != $user->id){
                $this->push(
                    $this->getRootNotification($application_id, $value->user_id), 
                    [
                        "current" => $user->id,
                        "room" => $room,
                        "seen" => false,
                        "type" => "message",
                        "uuid" => $uuid
                    ]
                );
            }
        }
    }

    public function sendEvents($application_id, $join, $uuid){
        foreach ($join as $key => $value) {
            $this->push(
                $this->getRootNotification($application_id, $value), 
                [
                    "type" => "schedule",
                    "uuid" => $uuid
                ]
            );
        }
    }
}