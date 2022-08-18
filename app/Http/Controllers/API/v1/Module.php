<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application as ApplicationModel;
use App\Models\Module as ModuleModel;
use App\Models\SelectModule as SelectModuleModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
class Module extends Controller
{
    //
    public function appSelectModule($application_id, $module_id){
        if(!isset($application_id)) return APIResponse::FAIL(['application' => ["Không thể truy cập ứng dụng"]]);
        if(!isset($module_id)) return APIResponse::FAIL(['module' => ["Không tìm thấy tính năng cần thêm"]]);  
        $findModule = ModuleModel::find($module_id);
        if(!isset($findModule)) return APIResponse::FAIL(['module' => ["Không tìm thấy tính năng yêu cầu"]]);
        $findModuleInApplication = SelectModuleModel::where("application_id", '=', $application_id)->where("module_id", '=', $module_id)->first();
        if(isset($findModuleInApplication)) return APIResponse::SUCCESS("Đã thêm module ".$findModule->name." vào ứng dụng");
        SelectModuleModel::insert(["application_id" => $application_id, "module_id" => $module_id]);
        return APIResponse::SUCCESS("Đã thêm Tính năng ".$findModule->name." vào ứng dụng");
    }

    public function appUnSelectModule($application_id, $module_id){
        if(!isset($application_id)) return APIResponse::FAIL(['application' => ["Không thể truy cập ứng dụng"]]);
        if(!isset($module_id)) return APIResponse::FAIL(['module' => ["Không tìm thấy tính năng cần xoá"]]);  
        $findModule = ModuleModel::find($module_id);
        if(!isset($findModule)) return APIResponse::FAIL(['module' => ["Không tìm thấy tính năng yêu cầu"]]);
        $findModuleInApplication = SelectModuleModel::where("application_id", '=', $application_id)->where("module_id", '=', $module_id)->first();
        if(!isset($findModuleInApplication)) return APIResponse::SUCCESS("Tính năng ".$findModule->name." không nằm trong ứng dụng");
        $findModuleInApplication->delete();
        return APIResponse::SUCCESS("Tính năng ".$findModule->name." đã xoá khỏi ứng dụng");
    }

}
