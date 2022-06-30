<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Response extends Controller
{
    //
    public static function FAIL($messages){
        return [
            "status" => "Fail",
            "message" => $messages
        ];
    }
}
