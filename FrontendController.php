<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(){
        return view('index',[
            'sliders' => Slider::all(),
        ]);
    }
}
