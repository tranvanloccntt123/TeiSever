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
            "start_at" => "required"
        ];
        $messages = [
            'title.required' => 'Tiêu đề không được bỏ trống',
            'description.required' => 'Mô tả không được bỏ trống',
            'join.required' => 'Cần có người tham gia trong sự kiện'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $uuid = UUID::guidv4();
        $id = EventModel::insertGetId([
            "application_id" => $request->application_id ,
            "title" => $request->title, 
            "description" => $request->description, 
            "config" => $request->has('config')? $request->config : "{}", 
            "start_at" => $request->start_at,
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
        return new EventCollection($find);
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
        $find = EventModel::where('start_at', 'LIKE', "%day: ".$request->day."%")
            ->where('start_at', 'LIKE', "%month: ".$request->month."%")
            ->where('start_at', 'LIKE', "%year: ".$request->year."%")
            ->where('user_id', '=', $user->id)
            ->leftJoin("event_users", "event_users.event_id", "=", "events.id")
            ->leftJoin("users", "event_users.user_id", "=", "users.id")
            ->select("events.*", "users.name as name", "users.avatar", "event_users.user_id")
            ->get();
        return new EventCollection($find);
    }
}
