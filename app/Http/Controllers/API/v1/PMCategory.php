<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PMCategory as PMCategoryModel;
use App\Http\Controllers\API\v1\Response as APIResponse;
use Validator;
class PMCategory extends Controller
{
     //
    public function get(Reqeust $request){
         $rule = ['application_id' => 'required'];
         $messages = [
            'application_id.required' => 'Application ID is required'
         ];
         $validator = Validator::make($request->all(), $rule, $messages);
         if($validator->fails()) return APIResponse::FAIL($validator->errors());
         return PMCategoryModel::select('id', 'name', 'description')->where('applications', 'LIKE', '%'.$request->application_id.'%')->get();
     }
    public function create(Request $request){
        $rule = ['name' => 'required|max:255'];
        $messages = [
            'name.required' => 'Name is required',
            'name.max:255' => "Name's length is 255"
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        PMCategoryModel::insert(['name' => $request->name, 'description' => $request->has("description")? $request->description : ""]);
        return APIResponse::SUCCESS("Category is created");
    }
    public function joinApplication(Request $request){
        $rule = ['id' => 'required', 'application_id' => 'required'];
        $messages = [
            'id.required' => 'Category ID is required',
            'application_id.required' => 'Application ID is required'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $category = PMcategoryModel::find($request->id);
        if(!isset($category)) return APIResponse::FAIL("Category not found");
        $listArray = explode($category['applications'], ',');
        if(array_search($request->application_id, $listArray) >= 0) return APIResponse::FAIL("Category is exists");
        $category->applications = $category->applications.",".$request->application_id;
        $category->save();
        return APIResponse::SUCCESS("Category is inserted");
    }
}
