<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UUID;
use Illuminate\Http\Request;
use App\Models\Event as EventModel;
use App\Models\EventUsers as EventUserModel;
use App\Models\User as UserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use App\Http\Resources\EventCollection;
use Validator;
class Events extends Controller
{
    //
    public function create(Request $request){
        $rule = [
            "title" => "required",
            "description" => "required",
            "join" => "required",
            "day" => "required",
            "month" => "required",
            "year" => "required",
            "hour" => "required",
            "minute" => "required"
        ];
        $messages = [
            'title.required' => 'Tiêu đề không được bỏ trống',
            'description.required' => 'Mô tả không được bỏ trống',
            'join.required' => 'Cần có người tham gia trong sự kiện',
            'day.required' => 'Ngày không được bỏ trống',
            'month.required' => 'Tháng không được bỏ trống',
            'year.required' => 'Năm không được bỏ trống',
            'hour.required' => 'Giờ không được bỏ trống',
            'minute.required' => 'Phút không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
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
        return APIResponse::SUCCESS(['UUID' => $uuid]);
    }

    public function detail(Request $request){
        $rule = [
            "uuid" => "required"
        ];
        $messages = [
            'uuid.required' => 'ID sự kiện không được bỏ trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $find = EventModel::where('UUID', 'LIKE', $request->uuid)
            ->leftJoin("event_users", "event_users.event_id", "=", "events.id")
            ->leftJoin("users", "event_users.user_id", "=", "users.id")
            ->select("events.*", "users.name as name", "users.avatar", "event_users.user_id")
            ->get();
        return APIResponse::SUCCESS(new EventCollection($find));
    }

    public function get(Request $request){
        $rule = [
            "day" => "required",
            "month" => "required",
            "year" => "required"
        ];
        $messages = [
            'day.required' => 'Ngày diễn ra sự kiện không được để trống',
            'month.required' => 'Tháng diễn ra sự kiện không được để trống',
            'year.required' => 'Năm diễn ra sự kiện không được để trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        //2022-11-13T15:38:13.000Z
        $find = EventModel::where('day', '=', $request->day)
            ->where('month', '=', $request->month)
            ->where('year', '=', $request->year)
            ->where('user_id', '=', $user->id)
            ->leftJoin("event_users", "event_users.event_id", "=", "events.id")
            ->leftJoin("users", "event_users.user_id", "=", "users.id")
            ->select("events.*", "users.name as name", "users.avatar", "event_users.user_id")
            ->get();
        return APIResponse::SUCCESS(new EventCollection($find));
    }
}
