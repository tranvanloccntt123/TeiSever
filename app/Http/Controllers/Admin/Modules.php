<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module as ModuleModel;
use App\Models\SelectModule;
use App\Http\Controllers\API\v1\Response as ResponseAPI;
use App\Http\Controllers\API\v1\Module as ModuleAPI;
class Modules extends Controller
{
    //
    public function appSelectModule(Request $request){
        if(!$request->has("id")) return redirect()->back()->withErrors(["Không thể truy cập ứng dụng"]);
        SelectModule::where("application_id", "=", $request->id)->delete();
        $errors = [];
        $success = "";
        foreach ($request->module_id as $key => $module) {
            $response = (new ModuleAPI())->appSelectModule($request->id, $module);
            if($response["status"] == ResponseAPI::$FAIL) array_push($errors, $response[0]["message"]);
            else $success = "Cập nhật tính năng thành công";
        }
        return redirect()->back()->withErrors($errors)->withSuccess($success);
    }
}
