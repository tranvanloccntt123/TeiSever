<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Response extends Controller
{
    public static $SUCCESS = "success";
    public static $FAIL = "Fail";
    
    public static function FAIL($messages){
        return [
            "status" => Response::$FAIL,
            "message" => $messages
        ];
    }

    public static function SUCCESS($message){
        return [
            "status" => Response::$SUCCESS,
            "message" => $message
        ];
    }

    public static function DATA($data){
        return [
            "status" => Response::$SUCCESS,
            "data" => $data
        ]
    }
}
