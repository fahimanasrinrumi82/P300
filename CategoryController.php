<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin_panel.categories.index', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin_panel.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category_id = Category::insertGetId([
            'category_name' => $request->category_name,
            'category_picture' => 'default_cat_pic.png',
            'created_by' => Auth::id(),
            'created_at' => Carbon::now(),
        ]);
        if ($request->hasFile('category_picture')) {
            $upload_file_name = $request->category_picture;
            $new_file_name = $category_id . '.' . $upload_file_name->getClientOriginalExtension();
            $upload_location = 'public/uploads/categories/' . $new_file_name;

            Image::make($upload_file_name)->resize(1920, 1000)->save(base_path($upload_location));
            Category::find($category_id)->update([
                'category_picture' => $new_file_name,
            ]);
        }
        return redirect(route('categories.index'));

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(Category $category)
    {
        return view('admin_panel.categories.edit',[
            'category' => $category
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $category->update([
            'category_name' => $request->category_name,
        ]);
        if ($request->hasFile('category_picture')) {
            if($category->category_picture != 'default_cat_pic.png'){
                unlink(base_path('public/uploads/categories/' . Category::find($request->category_id)->category_picture));
            }
            $upload_file_name = $request->category_picture;
            $new_file_name = $request->category_id . '.' . $upload_file_name->getClientOriginalExtension();
            $upload_location = 'public/uploads/categories/' . $new_file_name;

            Image::make($upload_file_name)->resize(1920, 1000)->save(base_path($upload_location));
            Category::find($request->category_id)->update([
                'category_picture' => $new_file_name,
            ]);
        }
        return redirect(route('categories.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function delete($category_id,Category $category)
    {
        $category->find($category_id)->delete();
        return back();
    }

    public function markdelete(Request $request ,Category $category)
    {
        foreach($request->mark_delete as $mark_delete){
            $category->find($mark_delete)->delete();
        }
        return back();
    }
    

    public function trash(Category $category)
    {
       return view('admin_panel.categories.trash',[
            'categories_trash' => Category::onlyTrashed()->get(),
       ]);
    }

    public function restore($category_id, Category $category)
    {
        $category->withTrashed()->find($category_id)->restore();
        return back();
    }

    public function markrestore(Request $request, Category $category)
    {
        foreach ($request->mark_restore as $mark_restore) {
            $category->withTrashed()->find($mark_restore)->restore();
        }
        return back();
    }


    public function forcedelete($category_id, Category $category)
    {
        $category->withTrashed()->find($category_id)->forceDelete();
        return back();
    }

    public function markforcedelete(Request $request, Category $category)
    {
        foreach ($request->mark_restore as $mark_restore) {
            $category->withTrashed()->find($mark_restore)->forcedelete();
        }
        return back();
    }
}
