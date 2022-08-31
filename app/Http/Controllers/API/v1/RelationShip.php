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

function getStatus(StatusType $status){
    switch($status){
        case StatusType::request: return 0;
        case StatusType::confirm: return 1;
        case StatusType::cancel: return -1;
    }
}

class RelationShip extends Controller
{
    //
    public function create(Request $request){
        $rule = [
            'application_id' => 'required',
            'friend' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống',
            'friend.required' => 'Đối tượng không được để trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $checkUserApplication = User::find($friend);
        if(!isset($checkUserApplication) || $checkUserApplication->application_id != $request->appication_id) return APIResponse::FAIL(['friend' => ['Không tìm thấy đối tượng']]);
    }

    public function getList(Request $request){
        $rule = [
            'application_id' => 'required'
        ];
        $messages = [
            'application_id.required' => 'Application ID is không được bỏ trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $user = $request->user();
        if(!isset($user)) return APIResponse::FAIL(['username' => ["Không tìm thấy thông tin của người dùng"]]);
        $list = RelationShipModel::where('relationships.user_id', '=', $user->id)
            ->where('relationships.status', '=', getStatus(StatusType::confirm))
            ->where('relationships.application_id', '=', $request->application_id)
            ->where('users.application_id', '=', $request->application_id)
            ->join('users', 'relationships.friend', '=', 'users.id')
            ->select('users.*')
            ->paginate(15);
        return APIResponse::SUCCESS($list);
    }
}
