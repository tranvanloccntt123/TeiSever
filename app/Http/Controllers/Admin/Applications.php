<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application as ApplicationModel;
use App\Models\ApplicationType as ApplicationTypeModel;
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
        if($result["status"] == ResponseAPI::$SUCCESS) return redirect()->back()->withSuccess($result["message"]);
        return redirect()->back()->withErrors($result["message"]);
    }
    public function create(Request $request){
        if ($request->isMethod('get')) return redirect()->back()->withErrors(["message" => "Không thể đăng kí ứng dụng"]);
        $result = (new ApplicationAPI())->create($request);
        return $this->returnBack($result);
    }
    public function delete(Request $request){
        if ($request->isMethod('get')) return redirect()->back()->withErrors(["message" => "Không thể xoá ứng dụng"]);
        $result = (new ApplicationAPI())->delete($request);
        return $this->returnBack($result);
    }
    public function manager(Request $request, $id){
        $config = new Config();
        return $config->resource(TRUE);
    }
}
