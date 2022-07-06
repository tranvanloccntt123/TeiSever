<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Config extends Controller
{
    public $title = "";
    public $subTitle = "";
    public $data = [];
    public $layouts = [];
    public function resource(){
        return view("admin.index", [
            "title" => $this->title,
            "subTitle" => $this->subTitle,
            "layouts" => $this->layouts,
            "data" => $this->data
        ]);
    }
}
