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
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $checkUserApplication = UserModel::find($request->friend);
        if(!isset($checkUserApplication) || $checkUserApplication->application_id != $request->application_id) return APIResponse::FAIL(['friend' => ['Không tìm thấy đối tượng']]);
        $findRelationShip = RelationShipModel::where('user_id', '=', $user->id)->where('friend', '=', $request->friend)->first();
        if(isset($findRelationShip)) {
            $findRelationShip->status = $this->getStatus($request->status);
            $findRelationShip->save();
            $findRevertRelationShip = RelationShipModel::where('user_id', '=', $request->friend)->where('friend', '=', $user->id)->first();
            $findRevertRelationShip->status = $this->getStatus($request->status);
            $findRevertRelationShip->save();
        } else {
            RelationShipModel::create([
                'user_id' => $user->id,
                'friend' => $reqeust->friend,
                'application_id' => $request->application_id,
                'status' => $this->getStatus($request->status),
            ]);
            RelationShipModel::create([
                'user_id' => $reqeust->friend, 
                'friend' => $user->id,
                'application_id' => $request->application_id,
                'status' => $this->getStatus($request->status),
            ]);
        }
        return APIResponse::SUCCESS("Yêu cầu đã được xử lý thành công");
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
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
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
}
