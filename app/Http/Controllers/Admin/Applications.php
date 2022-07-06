<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Application as ApplicationModel;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Admin\Config;
class Applications extends Controller
{
    //
    private $title = "applications";
    public function index()
    {
        $applications = ApplicationModel::get();
        $config = new Config();
        $config->title= $this->title;
        $config->data = $applications;
        $config->layouts = [
            "application"
        ];
        return $config->resource();
    }
}
