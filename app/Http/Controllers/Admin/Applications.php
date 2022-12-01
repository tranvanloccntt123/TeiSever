<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application as ApplicationModel;
use App\Models\ApplicationType as ApplicationTypeModel;
use App\Models\Module as ModuleModel;
use App\Models\SelectModule as SelectModuleModel;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Admin\Config;
use App\Http\Controllers\API\v1\Application as ApplicationAPI;
use App\Http\Controllers\API\v1\Response as ResponseAPI;
class Applications extends Controller
{
    //
    private $title = "applications";
    public function index()
    {
        $config = new Config();
        $config->title= $this->title;
        $config->data = [
            'applications' => ApplicationModel::join("application_type","applications.type_id","=","application_type.id")->select("applications.*", "application_type.flag")->get(),
            'types' => ApplicationTypeModel::get()
        ];
        $config->layouts = [
            "application"
        ];
        return $config->resource();
    }
    public function returnBack($result){
        if($result["status"] == ResponseAPI::$SUCCESS_STATUS) return redirect()->back()->withSuccess($result["message"]);
        return redirect()->back()->withErrors($result["message"]);
    }
    public function create(Request $request){
        if ($request->isMethod('get')) return redirect()->back()->withErrors(["message" => "Không thể đăng kí ứng dụng"]);
        $application = (new ApplicationAPI());
        $application->isApi = false;
        $result = $application->create($request);
        return $this->returnBack($result);
    }
    public function delete(Request $request){
        if ($request->isMethod('get')) return redirect()->back()->withErrors(["message" => "Không thể xoá ứng dụng"]);
        $application = (new ApplicationAPI());
        $application->isApi = false;
        $result = $application->delete($request);
        return $this->returnBack($result);
    }
    public function manager(Request $request, $id){
        $config = new Config();
        $application = ApplicationModel::find($id);
        if(!isset($application)) return redirect()->back()->withErrors(["Không thể truy cập ứng dụng"]);
        $config->title = $application["name"];
        $config->layouts = [
            "application-manager"
        ];
        $config->data = [
            "app" => $application,
            'types' => ApplicationTypeModel::get(),
            'modules' => ModuleModel::where("type_id","=", $application->type_id)->orWhere("type_id","=", NULL)->get(),
            'select_modules' => ApplicationModel::find($id)->modules()->get()
        ];
        return $config->resource(TRUE);
    }

    public function edit(Request $request){
        $application = (new ApplicationAPI());
        $application->isApi = false;
        $result = $application->edit($request);
        return $this->returnBack($result);
    }

    public function docs(Request $request, $id){
        $config = new Config();
        $config->title= $this->title;
        $config->data = [
            'types' => ApplicationTypeModel::get()
        ];
        $config->layouts = [
            "application-document"
        ];
        return $config->resource();
    }
}
