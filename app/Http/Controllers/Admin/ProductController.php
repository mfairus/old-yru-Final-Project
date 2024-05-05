<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use App\Models\SubCategoryModel;
use App\Models\ProductModel;
use App\Models\BrandModel;
use App\Models\ColorModel;
use App\Models\ProductColorModel;
use App\Models\ProductSizeModel;
use App\Models\ProductImageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\CssSelector\XPath\Extension\FunctionExtension;

class ProductController extends Controller
{
    public function list()
    {
        $data['getRecord'] = ProductModel::getRecord();
        $data['header_title'] = "Product";
        return view('admin.product.list', $data);
    }
    public function add()
    {
        $data['getCategory'] = CategoryModel::getRecordActive();
        $data['getBrand'] = BrandModel::getRecordActive();
        $data['getColor'] = ColorModel::getRecordActive();
        // $data['getRecord'] = SubCategoryModel::getRecord();
        $data['header_title'] = "Add New Product";
        return view('admin.product.add', $data);
    }
    public function insert(Request $request)
    {
        $product = new ProductModel;
        $product->title = trim($request->title);
        $product->sku = trim($request->sku);
        $product->category_id = trim($request->category_id);
        $product->sub_category_id = trim($request->sub_category_id);
        $product->brand_id = trim($request->brand_id);
        $product->price = trim($request->price);
        $product->old_price = trim($request->old_price);
        $product->short_description = trim($request->short_description);
        $product->description = trim($request->description);
        $product->additional_information = trim($request->additional_information);
        $product->shipping_returns = trim($request->shipping_returns);
        $product->status = trim($request->status);
        $product->created_by = Auth::user()->id;
        $product->save();

        if(!empty($request->color_id))
        {
            foreach ($request->color_id as $color_id) {
            $color = new ProductColorModel;
            $color->color_id = $color_id;
            $color->product_id = $product->id;
            $color->save();
        }
        }
        
        if (!empty($request->size)) {
            foreach ($request->size as $size) {
                if (!empty($size['name'])) {
                    $saveSize = new ProductSizeModel;
                    $saveSize->name = $size['name'];
                    $saveSize->price = !empty($size['price']) ? $size['price'] : 0;
                    $saveSize->product_id = $product->id;
                    $saveSize->save();
                }
            }
        }

        $slug = Str::slug($product->title, "-");

        $checkSlug = ProductModel::checkSlug($slug);
        if (empty($checkSlug)) {
            $product->slug = $slug;
            $product->save();
        } else {
            $new_slug = $slug . '-' . $product->id;
            $product->slug =  $new_slug;
            $product->save();
        }

        if (!empty($request->file('image'))) {
            foreach ($request->file('image') as $value) {
                if ($value->isValid()) {
                    $ext = $value->getClientOriginalExtension();
                    $randomStr = $product->id . Str::random(20);
                    $filename = strtolower($randomStr) . '.' . $ext;
                    $value->move('upload/product/', $filename);

                    $imageupload = new ProductImageModel;
                    $imageupload->image_name = $filename;
                    $imageupload->image_extension = $ext;
                    $imageupload->product_id = $product->id;
                    $imageupload->save();
                }
            }
        }
        
        return redirect('admin/product/list');
        //  return redirect('admin/product/edit/'.$product->id);
    }
    public function edit($product_id)
    {
        $product = ProductModel::getSingle($product_id);
        if (!empty($product)) {
            $data['getCategory'] = CategoryModel::getRecordActive();
            $data['getBrand'] = BrandModel::getRecordActive();
            $data['getColor'] = ColorModel::getRecordActive();
            $data['product'] = $product;

            $data['getsubcategory'] = SubCategoryModel::getRecordSubCategory($product->category_id); // ดึงข้อมูลหมวดหมู่ย่อยจากฐานข้อมูล

            $data['header_title'] = "Edit Product";
            return view('admin.product.edit', $data);
        }
    }
    public function update($product_id, Request $request)
    {
        $product = ProductModel::getSingle($product_id);

        if (!empty($product)) {
            $product->title = trim($request->title);
            $product->sku = trim($request->sku);
            $product->category_id = trim($request->category_id);
            $product->sub_category_id = trim($request->sub_category_id);
            $product->brand_id = trim($request->brand_id);
            $product->price = trim($request->price);
            $product->old_price = trim($request->old_price);
            $product->short_description = trim($request->short_description);
            $product->description = trim($request->description);
            $product->additional_information = trim($request->additional_information);
            $product->shipping_returns = trim($request->shipping_returns);
            $product->status = trim($request->status);
            $product->save();

            ProductColorModel::DeleteRecord($product->id);
            if (!empty($request->color_id)) {
                foreach ($request->color_id as $color_id) {
                    $color = new ProductColorModel;
                    $color->color_id = $color_id;
                    $color->product_id = $product->id;
                    $color->save();
                }
            }

            ProductSizeModel::DeleteRecord($product->id);
            if (!empty($request->size)) {
                foreach ($request->size as $size) {
                    if (!empty($size['name'])) {
                        $saveSize = new ProductSizeModel;
                        $saveSize->name = $size['name'];
                        $saveSize->price = !empty($size['price']) ? $size['price'] : 0;
                        $saveSize->product_id = $product->id;
                        $saveSize->save();
                    }
                }
            }

            if (!empty($product)) {
                if (!empty($request->file('image'))) {
                    foreach ($request->file('image') as $value) {
                        if ($value->isValid()) {
                            $ext = $value->getClientOriginalExtension();
                            $randomStr = $product->id . Str::random(20);
                            $filename = strtolower($randomStr) . '.' . $ext;
                            $value->move('upload/product/', $filename);

                            $imageupload = new ProductImageModel;
                            $imageupload->image_name = $filename;
                            $imageupload->image_extension = $ext;
                            $imageupload->product_id = $product->id;
                            $imageupload->save();
                        }
                    }
                }
            }

            return redirect()->back()->with('success', "Product Successfully Update");
        } else {
            abort(404);
        }
    }
    public function image_delete($id)
    {
        $image = ProductImageModel::getSingle($id);
        if (!empty($image->getLogo())) {
            unlink('upload/product/' . $image->image_name);
        }
        $image->delete();
        return redirect()->back()->with('success', "Product Image Successfully Delete");
    }
    public function product_image_sortable(Request $request)
    {
        if (!empty($request->photo_id)) {
            $i = 1;
            foreach ($request->photo_id as $photo_id) {
                $image = ProductImageModel::getSingle($photo_id);
                $image->order_by = $i;
                $image->save();

                $i++;
            }
        }
        $json['success'] = true;
        echo json_encode($json);
    }
    public function delete($id)
    {
        $product = ProductModel::getSingle($id);
        $product->is_delete = 1;
        $product->save();

        return redirect()->back()->with('success', "Product Successfuly Delete");
    }
}
