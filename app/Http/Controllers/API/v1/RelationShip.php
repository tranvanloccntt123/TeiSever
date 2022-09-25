<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RelationShip as RelationShipModel;
use App\Models\User as UserModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;

enum StatusType{
    case request;
    case confirm;
    case cancel;
}

class RelationShip extends Controller
{
    //
    public function create(Request $request){
        $rule = [
            'application_id' => 'required',
            'friend' => 'required',
            'status' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống',
            'friend.required' => 'Đối tượng không được để trống',
            'status.required' => 'Trạng thái không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user) && $user->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $checkUserApplication = UserModel::find($request->friend);
        if(!isset($checkUserApplication) || $checkUserApplication->application_id != $request->application_id) return APIResponse::FAIL(['friend' => ['Không tìm thấy đối tượng']]);
        switch($request->status){
            case StatusType::confirm->name: {
                $findRelationShip = RelationShipModel::where('user_id', '=', $user->id)->where('friend', '=', $request->friend)->first();
                if(isset($findRelationShip)) {
                    if($findRelationShip->who_request == $user->id && $request->status == StatusType::confirm->name) return APIResponse::FAIL(['request' => ["Không tìm thể gửi yêu cầu"]]);
                    $findRelationShip->status = $this->getStatus($request->status);
                    $findRelationShip->save();
                    $findRevertRelationShip = RelationShipModel::where('user_id', '=', $request->friend)->where('friend', '=', $user->id)->first();
                    $findRevertRelationShip->status = $this->getStatus($request->status);
                    $findRevertRelationShip->save();
                    return APIResponse::SUCCESS(["relation" => "Yêu cầu đã được xử lý thành công"]);
                }
                return APIResponse::FAIL(["relation" => "Yêu cầu xử lý thất bại"]);
            }
            case StatusType::request->name:{
                $findRelationShip = RelationShipModel::where('user_id', '=', $user->id)->where('friend', '=', $request->friend)->first();
                if(isset($findRelationShip)) return APIResponse::FAIL(["relation" => "Yêu cầu đã được tạo trước đó"]);
                RelationShipModel::create([
                    'user_id' => $user->id,
                    'friend' => $request->friend,
                    'application_id' => $request->application_id,
                    'status' => $this->getStatus($request->status),
                    'who_request' => $user->id,
                ]);
                RelationShipModel::create([
                    'user_id' => $request->friend, 
                    'friend' => $user->id,
                    'application_id' => $request->application_id,
                    'status' => $this->getStatus($request->status),
                    'who_request' => $user->id
                ]);
                return APIResponse::SUCCESS(["relation" => "Yêu cầu đã được xử lý thành công"]);
            }
            case StatusType::cancel->name: {
                RelationShipModel::where('user_id', '=', $user->id)->where('friend', '=', $request->friend)->delete();
                RelationShipModel::where('user_id', '=', $request->friend)->where('friend', '=', $user->id)->delete();
                return APIResponse::SUCCESS(["relation" => "Yêu cầu đã được xử lý thành công"]);
            }
        }
        return APIResponse::FAIL(["relation" => "Yêu cầu xử lý thất bại"]);
    }

    public function getList(Request $request){
        $rule = [
            'application_id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user) && $user->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $list = RelationShipModel::where('relationships.user_id', '=', $user->id)
            ->where('relationships.status', '=', $this->getStatus(StatusType::confirm->name))
            ->where('relationships.application_id', '=', $request->application_id)
            ->where('users.application_id', '=', $request->application_id)
            ->join('users', 'relationships.friend', '=', 'users.id')
            ->select('users.*');
        if($request->has('left_id'))
            $list = $list->where('relationships.id', '<', $request->left_id);
        return APIResponse::SUCCESS($list->paginate(15));
    }

    public function checkRelation(Request $request){
        $rule = [
            'application_id' => 'required',
            'user_id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống',
            'user_id.required' => 'Đối tượng không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user) && $user->application_id != $request->application_id) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $findRelationShip = RelationShipModel::where('relationships.user_id', '=', $user->id)
        ->where("relationships.friend", "=", $request->user_id)->first();
        $relationStatus = -1;
        $relationWhoRequest = false;
        if($findRelationShip){
            $relationStatus = $findRelationShip->status;
            $relationWhoRequest = $findRelationShip->who_request == $user->id;
        }
        return APIResponse::SUCCESS([
            'status' => $relationStatus,
            'personRequest' => $relationWhoRequest
        ]);
    }

    public function createRelationShipDescription(Request $request){
        $rule = [
            'application_id' => 'required',
            'id' => 'required',
            'description' => 'required',
            'start' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID không được bỏ trống',
            'id.required' => 'Không tìm thấy ID tin nhắn',
            'description.required' => 'Mô tả không được bỏ trống',
            'start.required' => 'thời điểm bắt đầu không được bỏ trống'
         ];
         //date('Y-m-d H:i:s','1299762201428')
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $findRelationShip = RelationShipModel::where('user_id', '=', $user->id)->where('friend', '=', $request->id)->first();
        if(!isset($findRelationShip)) return APIResponse::FAIL(['relation' => ["Bạn cần phải kết bạn trước đó"]]);
        
        $findRelationShip->description = $request->description;
        $findRelationShip->start = $request->start;
        $findRelationShip->save();

        $findRelationShip = RelationShipModel::where('user_id', '=', $request->id)->where('friend', '=', $user->id)->first();
        $findRelationShip->description = $request->description;
        $findRelationShip->start = $request->start;
        $findRelationShip->save();
    }
}
