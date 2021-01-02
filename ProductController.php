<?php

namespace App\Http\Controllers;

use Carbon\carbon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin_panel.products.index',[
            'products' => Product::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin_panel.products.create',[
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $product_id = Product::insertGetId($request->except('_token','product_thumbnail_picture','product_picture') + [
            'create_by' => Auth::id(),
            'created_at' => Carbon::now(),
            'slug' => $request->product_name .'-'. Str::random(5). rand(2,5),
        ]);
        if($request->hasfile('product_thumbnail_picture')){
            $upload_image = $request->product_thumbnail_picture;
            $upload_image_name = $product_id . '.' . $upload_image->getClientOriginalExtension();
            $upload_image_location = 'public/uploads/products/'. $upload_image_name;

            Image::make($upload_image)->resize(600,622)->save(base_path($upload_image_location));
            Product::find($product_id)->update([
                'product_thumbnail_picture' => $upload_image_name,
            ]);
        }
        if ($request->hasfile('product_picture')) {
            
            $flag = 1;
            foreach ($request->product_picture as $product_picture) {
                $ProductImage = ProductImage::insertGetId([
                    'product_id' => $product_id,
                    'product_picture' => $product_picture,
                    'created_by' => Auth::id(),
                    'created_at' => Carbon::now(),
                ]);
                $upload_images = $product_picture;
                $upload_images_name = $product_id . '-' . $flag . '.' . $upload_images->getClientOriginalExtension();
                $upload_images_location = 'public/uploads/product_images/' . $upload_images_name;

                Image::make($upload_images)->resize(600, 622)->save(base_path($upload_images_location));
                ProductImage::find($ProductImage)->update([
                    'product_picture' => $upload_images_name,
                ]);
                $flag++;
            }
        }
        return redirect(route('products.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('admin_panel.products.edit',[
            'product' => $product,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }
}
