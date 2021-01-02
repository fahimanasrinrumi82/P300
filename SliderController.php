<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class SliderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin_panel.sliders.index', [
            'sliders' => Slider::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin_panel.sliders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $slider_id = Slider::insertGetId($request->except('_token')+[
            'created_at' => Carbon::now(),
            'created_by' => Auth::id(),
        ]);
        if($request->hasFile('bg_image')){
            $upload_file_name = $request->bg_image;
            $new_file_name = $slider_id .'.'. $upload_file_name->getClientOriginalExtension();
            $upload_location = 'public/uploads/sliders/' . $new_file_name;

            Image::make($upload_file_name)->resize(1920,1000)->save(base_path($upload_location));
            Slider::find($slider_id)->update([
                'bg_image'=>$new_file_name,
            ]);
        }
        return redirect(route('sliders.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider)
    {
        return view('admin_panel.sliders.edit',[
            'slider_info' => $slider
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Slider $slider)
    {
        $slider->update($request->except('_token', '_method', 'bg_image'));
        if($request->hasfile('bg_image')){
            unlink(base_path('public/uploads/sliders/'. Slider::find($request->slider_id)->bg_image));
            $upload_file_name = $request->bg_image;
            $new_file_name = $request->slider_id . '.' . $upload_file_name->getClientOriginalExtension();
            $upload_location = 'public/uploads/sliders/' . $new_file_name;

            Image::make($upload_file_name)->resize(1920, 1000)->save(base_path($upload_location));
            Slider::find($request->slider_id)->update([
                'bg_image' => $new_file_name,
            ]);
        }
        return redirect(route('sliders.index'));
    }

   
    public function delete($slider_id,Slider $slider)
    {
        $slider->find($slider_id)->delete();
        return redirect(route('sliders.index'));
    }

    public function markdelete(Request $request, Slider $slider)
    {
        foreach($request->mark_delete as $mark_delete){
            $slider->find($mark_delete)->delete();
        }
        return redirect(route('sliders.index'));
    }


    public function Trash(Slider $slider)
    {
        return view('admin_panel.sliders.Trash',[
            'slider_Trashes' => Slider::onlyTrashed()->get(),
        ]);
    }


    public function restore($slider_id, Slider $slider)
    {
        $slider->withTrashed()->find($slider_id)->restore();
        return redirect(route('sliders.index'));
    }


    public function forceDelete($slider_id, Slider $slider)
    {
        $slider->withTrashed()->find($slider_id)->forceDelete();
        return back();
    }


    public function markRestore(Request $request, Slider $slider)
    {
        foreach ($request->mark_restore as $mark_restore) {
            $slider->withTrashed()->find($mark_restore)->restore();
        }
        return redirect(route('sliders.index'));
    }


    public function markforceDelete(Request $request, Slider $slider)
    {
        foreach ($request->mark_restore as $mark_restore) {
            $slider->withTrashed()->find($mark_restore)->forceDelete();
        }
        return back();
    }


}
