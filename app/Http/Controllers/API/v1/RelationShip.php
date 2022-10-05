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

enum RelationShipDescription{
    case friend;
    case daughter;
    case husband;
    case wife;
    case son;
    case grandfather;
    case grandmother;
    case father;
    case mother;
    case lover;
}

class RelationShip extends Controller
{
    //
    public function create(Request $request){
        $rule = [
            'friend' => 'required',
            'status' => 'required'
        ];
        $messages = [
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
                    $relationStatus = $this->getStatus($request->status);
                    $relationWhoRequest = $findRelationShip->who_request == $user->id;
                    return APIResponse::SUCCESS([
                        'status' => $relationStatus,
                        'personRequest' => $relationWhoRequest
                    ]);
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
                $relationStatus = $this->getStatus($request->status);
                $relationWhoRequest = $user->id;
                return APIResponse::SUCCESS([
                    'status' => $relationStatus,
                    'personRequest' => $relationWhoRequest
                ]);
            }
            case StatusType::cancel->name: {
                RelationShipModel::where('user_id', '=', $user->id)->where('friend', '=', $request->friend)->delete();
                RelationShipModel::where('user_id', '=', $request->friend)->where('friend', '=', $user->id)->delete();
                return APIResponse::SUCCESS(['status' => -1, 'personRequest' => false]);
            }
        }
        return APIResponse::FAIL(["relation" => "Yêu cầu xử lý thất bại"]);
    }

    public function getList(Request $request){
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
            'user_id' => 'required'
        ];
        $messages = [
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
            'user_id' => 'required',
            'description' => 'required',
            'start' => 'required'
        ];
        $messages = [
            'user_id.required' => 'Đối tượng khôngg được bỏ trống',
            'description.required' => 'Mô tả không được bỏ trống',
            'start.required' => 'thời điểm bắt đầu không được bỏ trống'
         ];
         //date('Y-m-d H:i:s','1299762201428')
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $findRelationShip = RelationShipModel::where('user_id', '=', $user->id)->where('friend', '=', $request->user_id)->first();
        if(!isset($findRelationShip)) return APIResponse::FAIL(['relation' => ["Bạn cần phải kết bạn trước đó"]]);
        
        $findRelationShip->description = $request->description;
        $findRelationShip->start = $request->start;
        $findRelationShip->save();

        $findRelationShipOther = RelationShipModel::where('user_id', '=', $request->user_id)->where('friend', '=', $user->id)->first();
        $findRelationShipOther->description = $request->description;
        $findRelationShipOther->start = $request->start;
        $findRelationShipOther->save();

        return APIResponse::SUCCESS([
            'relation' => $findRelationShip,
        ]); 
    }

    public function getRelationShipDescription(Request $request){
        $user = $request->user();
        $findRelationShip = RelationShipModel::where('user_id', '=', $user->id)->where("description", "!=", "")->get();
        return APIResponse::SUCCESS([
            'relation' => $findRelationShip
        ]);
    }
}
