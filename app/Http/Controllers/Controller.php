<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Http\Controllers\CloudMessaginService;
use App\Http\Controllers\RealTimeService;
use Illuminate\Routing\Controller as BaseController;

enum StatusType{
    case request;
    case confirm;
    case cancel;
}

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getRealTime(){
        return new RealTimeService();
    }

    public function getCloudMessage(){
        return new CloudMessaginService();
    }

    public function getStatus($status){
        switch($status){
            case StatusType::request->name: return 0;
            case StatusType::confirm->name: return 1;
            case StatusType::cancel->name: return -1;
        }
    }

    public function getStatusConfirm(){
        return 1;
    }

    public function getStatusRequest(){
        return 0;
    }

    public function getStatusCancel(){
        return -1;
    }

    function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
