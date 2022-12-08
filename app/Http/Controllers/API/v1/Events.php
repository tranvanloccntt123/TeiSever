<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Core\Event as EventCore;
use Illuminate\Http\Request;
use App\Http\Controllers\API\v1\Response as APIResponse;
use App\Http\Resources\EventCollection;
use Validator;
class Events extends Controller
{
    public $isApi = true;
    private $eventCore;
    public function __construct(){
        $this->eventCore = new EventCore();
    }
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
        $uuid = $this->eventCore->create($request);
        $this->getRealTime()->sendEvents($request->application_id, $request->input('join'), $uuid);
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
        $find = $this->eventCore->getDetail($request->uuid);
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
        $find = $this->eventCore->getEventsInDay($user, $request->day, $request->month, $request->year);
        return APIResponse::SUCCESS(new EventCollection($find));
    }

    public function getScheduleFromDate(Request $request){
        $rule = [
            "day" => "required",
            "month" => "required",
            "year" => "required",
            "hour" => "required",
            "minute" => "required"
        ];
        $messages = [
            'day.required' => 'Ngày diễn ra sự kiện không được để trống',
            'month.required' => 'Tháng diễn ra sự kiện không được để trống',
            'year.required' => 'Năm diễn ra sự kiện không được để trống',
            'hour.required' => 'Giờ diễn ra sự kiện không được để trống',
            'minute.required' => 'Phút diễn ra sự kiện không được để trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $find = $this->eventCore->getEventAtDate($user, $request->day, $request->month, $request->year, $request->hour, $request->minute);
        return APIResponse::SUCCESS(new EventCollection($find));
    }

    public function deleteSchedule(Request $request){
        $rule = [
            "uuid" => "required"
        ];
        $messages = [
            'uuid.required' => 'ID không được để trống',
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        $this->eventCore->deleteEvent($user, $request->uuid);
        return APIResponse::SUCCESS(['event' => 'Xóa thành công sự kiện'], $this->isApi);
    }
}
