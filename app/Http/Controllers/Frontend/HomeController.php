<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('frontend.index');
    }

    public function maintance_mode(Request $request){
        $label = $request->label;
        $title = DB::table("setup_fields")->where('label',$label)->pluck('name')[0];
        return view('frontend.maintance.maintance',compact('label','title'));
    }
}
