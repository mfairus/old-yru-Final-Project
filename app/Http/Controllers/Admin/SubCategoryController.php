<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CategoryModel;
use App\Models\SubCategoryModel;
use Illuminate\Support\Facades\Auth;

class SubCategoryController extends Controller
{
    public function list()
    {
        $data['getRecord'] = SubCategoryModel::getRecord();
        $data['header_title'] = "Sub Category";
        return view('admin.subcategory.list', $data);
    }
    public function add()
    {
        $data['getCategory'] = CategoryModel::getRecord();
        $data['header_title'] = "Add New Sub Category";
        return view('admin.subcategory.add', $data);
    }

    public function insert(Request $request)
    {
        request()->validate([
            'slug' => 'required|unique:sub_category'
        ]);

        $subcategory = new SubCategoryModel;
        $subcategory->category_id = trim($request->category_id);
        $subcategory->name = trim($request->name);
        $subcategory->slug = trim($request->slug);
        $subcategory->status = trim($request->status);
        $subcategory->meta_title = trim($request->meta_title);
        $subcategory->meta_description = trim($request->meta_description);
        $subcategory->meta_keywords = trim($request->meta_keywords);
        $subcategory->created_by = Auth::user()->id;
        $subcategory->save();

        return redirect('admin/sub_category/list')->with('success', "Sub Category Successfully Create");
    }

    public function edit($id)
    {
        $data['getCategory'] = CategoryModel::getRecord();
        $data['getRecord'] = SubCategoryModel::getSingle($id);
        $data['header_title'] = "Edit Sub Category";
        return view('admin.subcategory.edit', $data);
    }
    public function update($id, Request $request)
    {
        request()->validate([
            'slug' => 'required|unique:sub_category,slug,' . $id
        ]);

        $subcategory = SubCategoryModel::getSingle($id);
        $subcategory->category_id = trim($request->category_id);
        $subcategory->name = trim($request->name);
        $subcategory->slug = trim($request->slug);
        $subcategory->status = trim($request->status);
        $subcategory->meta_title = trim($request->meta_title);
        $subcategory->meta_description = trim($request->meta_description);
        $subcategory->meta_keywords = trim($request->meta_keywords);
        $subcategory->save();

        return redirect('admin/sub_category/list')->with('success', "Sub Category Successfully Update");
    }
    public function delete($id)
    {
        $subcategory = SubCategoryModel::getSingle($id);
        $subcategory->is_delete = 1;
        $subcategory->save();

        return redirect()->back()->with('success', "Sub Category Successfuly Delete");
    }
    public function get_sub_category(Request $request)
    {
        // dd($request->all());
        $category_id = $request->id; // รับค่า category_id ที่ส่งมาจาก AJAX request
        $get_sub_category = SubCategoryModel::getRecordSubCategory($category_id); // ดึงข้อมูลหมวดหมู่ย่อยจากฐานข้อมูล
        $html = '';
        $html .= '<option value="">Select</option>'; // เพิ่มตัวเลือกเริ่มต้น
        foreach ($get_sub_category as $value) {
            // เพิ่มตัวเลือกสำหรับแต่ละหมวดหมู่ย่อย
            $html .= '<option value="' . $value->id . '">' . $value->name . '</option>';
        }
        $json['html'] = $html; // สร้าง JSON ที่มีค่าเป็น HTML สำหรับตัวเลือกในแบบฟอร์ม
        echo json_encode($json); // ส่ง JSON กลับไปยัง AJAX request

    }
}
