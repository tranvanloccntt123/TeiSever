<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

enum StatusType{
    case request;
    case confirm;
    case cancel;
}

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, FirebaseController, CloudMessaginService;

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
}
