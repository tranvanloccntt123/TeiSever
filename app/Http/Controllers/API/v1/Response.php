<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Response extends Controller
{
    public static $SUCCESS = "success";
    public static $FAIL = "Fail";

    public static function FAIL($messages, $isApi = true){
        $response = [
            "status" => Response::$FAIL,
            "message" => $messages
        ];
        return $isApi? response($response, 400) : $response;
    }

    public static function SUCCESS($message, $isApi = true){
        $response = [
            "status" => Response::$SUCCESS,
            "message" => $message
        ];
        return $isApi? response($response, 200) : $response;
    }

    public static function DATA($data, $isApi = true){
        return [
            "status" => Response::$SUCCESS,
            "data" => $data
        ];
    }
}
