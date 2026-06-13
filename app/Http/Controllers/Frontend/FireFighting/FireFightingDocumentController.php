<?php

namespace App\Http\Controllers\Frontend\FireFighting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FireFightingDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            
            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if (isset($request->s) && base64_decode($request->s, true)) {
            $main_path = base64_decode($request->s);
        } else {
            $main_path = 'assets/fire-fighting/documents';
        }
        
        $path = public_path().'/'.$main_path;
        if (file_exists($path)) {
            $files = array_diff(scandir($path), array('.', '..'));
        } else {
            $files = [];
        }
        
        $data['files'] = $files;
        $data['path'] = $path;
        $data['main_path'] = $main_path;

        return view('frontend.fire-fighting.documents', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
