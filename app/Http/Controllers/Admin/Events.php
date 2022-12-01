<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Config;
use App\Models\Event as EventModel;
use App\Http\Resources\EventCollection;
class Events extends Controller
{
    private $title = "events";
    //
    public function index(Request $request){
        $config = new Config();
        $config->title= $this->title;
        $config->data = [
            "events" => EventModel::leftJoin("event_users", "event_users.event_id", "=", "events.id")
            ->leftJoin("users", "event_users.user_id", "=", "users.id")
            ->select("events.*", "users.name as name", "users.avatar", "event_users.user_id")
            ->get()
        ];
        $config->layouts = [
            "events"
        ];
        return $config->resource();
    }
}
