<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event as EventModel;
use App\Models\EventUsers as EventUserModel;
use App\Models\User as UserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use App\Http\Resources\EventCollection;
use Validator;
use DB;
class Event extends Controller
{
    //
    public function create(Request $request){
        $uuid = UUID::guidv4();
        $id = EventModel::insertGetId([
            "application_id" => $request->application_id ,
            "title" => $request->title, 
            "description" => $request->description, 
            "config" => $request->has('config')? $request->config : "{}", 
            "day" => $request->day,
            "month" => $request->month,
            "year" => $request->year,
            "hour" => $request->hour,
            "minute" => $request->minute,
            "UUID" => $uuid
        ]);
        foreach ($request->input('join') as $key => $value) {
            EventUserModel::insert(['user_id' => $value, 'event_id' => $id]);
        }
        return $uuid;
    }

    public function getDetail($uuid){
        $find = EventModel::where('UUID', 'LIKE', $uuid)
        ->leftJoin("event_users", "event_users.event_id", "=", "events.id")
        ->leftJoin("users", "event_users.user_id", "=", "users.id")
        ->select("events.*", "users.name as name", "users.avatar", "event_users.user_id")
        ->get();
        return $find;
    }

    public function getEventsInDay($user, $day, $month, $year){
        $find = EventModel::where('day', '=', $day)
        ->where('month', '=', $month)
        ->where('year', '=', $year)
        ->where('user_id', '=', $user->id)
        ->leftJoin("event_users", "event_users.event_id", "=", "events.id")
        ->leftJoin("users", "event_users.user_id", "=", "users.id")
        ->select("events.*", "users.name as name", "users.avatar", "event_users.user_id")
        ->get();
        return $find;
    }

    public function getEventAtDate($user, $day, $month, $year, $hour, $minute){
        $find = EventModel::where('day', '=', $day)
            ->where('user_id', '=', $user->id)
            ->whereRaw("TIMESTAMP(CONCAT(events.year, '-', events.month, '-', events.day, ' ', events.hour, ':', events.minute, ':00')) >= TIMESTAMP('".$year."-".$month."-".$day." ".$request->hour.":".$minute.":00')")
            ->leftJoin("event_users", "event_users.event_id", "=", "events.id")
            ->leftJoin("users", "event_users.user_id", "=", "users.id")
            ->select("events.*", "users.name as name", "users.avatar", "event_users.user_id")
            ->get();
        return $find;
    }

    public function deleteEvent($user, $uuid){
        $find = EventModel::where('UUID', 'LIKE', $uuid)->first();
        if(!isset($find)) return APIResponse::FAIL(["event" => "Sự kiện không tồn tại"]);
        EventUserModel::where('user_id', '=', $user->id)->where('event_id', '=', $find->id)->delete();
    }
}
