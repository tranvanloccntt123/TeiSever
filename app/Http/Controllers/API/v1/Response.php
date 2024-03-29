<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Response extends Controller
{
    public static $SUCCESS_STATUS = "success";
    public static $FAIL_STATUS = "Fail";

    public static function FAIL($messages, $isApi = true){
        $response = [
            "status" => Response::$FAIL_STATUS,
            "message" => $messages
        ];
        return $isApi? response($response, 400) : $response;
    }

    public static function SUCCESS($message, $isApi = true){
        $response = [
            "status" => Response::$SUCCESS_STATUS,
            "message" => $message
        ];
        return $isApi? response($response, 200) : $response;
    }

    public static function DATA($data, $isApi = true){
        return [
            "status" => Response::$SUCCESS_STATUS,
            "data" => $data
        ];
    }
}
