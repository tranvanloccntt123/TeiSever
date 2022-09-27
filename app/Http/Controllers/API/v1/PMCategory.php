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
         return PMCategoryModel::select('id', 'name', 'description')->where('applications', 'LIKE', '%'.$request->application_id.'%')->get();
     }
    public function create(Request $request){
        $rule = ['name' => 'required|max:255'];
        $messages = [
            'name.required' => 'Bạn cần điền tên danh mục',
            'name.max:255' => 'Tên danh mục tối đa 255'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        PMCategoryModel::insert(['name' => $request->name, 'description' => $request->has('description')? $request->description : '']);
        return APIResponse::SUCCESS('Danh mục đã được khởi tạo');
    }
    public function joinApplication(Request $request){
        $rule = ['id' => 'required', 'application_id' => 'required'];
        $messages = [
            'id.required' => 'Danh mục ID không được bỏ trống',
            'application_id.required' => 'Application ID không được bỏ trống'
        ];
        $validator = Validator::make($request->all(), $rule, $messages);
        if($validator->fails()) return APIResponse::FAIL($validator->errors());
        $category = PMcategoryModel::find($request->id);
        if(!isset($category)) return APIResponse::FAIL(['category' => ['Không tìm thấy danh mục']]);
        $listArray = explode($category['applications'], ',');
        if(array_search($request->application_id, $listArray) >= 0) return APIResponse::FAIL(['category' => ['Danh mục đã tồn tại']]);
        $category->applications = $category->applications.','.$request->application_id;
        $category->save();
        return APIResponse::SUCCESS('Danh mục đã được khởi tạo');
    }
}
