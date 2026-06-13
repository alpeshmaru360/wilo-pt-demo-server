<?php

namespace App\Http\Controllers\Admin\FireFighting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

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

        return view('admin.fire-fighting.document.index', $data);
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
        if (isset($request->delete_file)) {
            if (base64_decode($request->delete_file, true)) {
                $path = base64_decode($request->delete_file);
                if (file_exists(public_path($path))) {

                    if (pathinfo(public_path($path), PATHINFO_EXTENSION)) {
                        unlink(public_path($path));
                        Session::flash('message', "Success! files has been deleted");
                    } else {
                        $this->delTree(public_path($path));
                        Session::flash('message', "Success! folder has been deleted");
                    }
                } else {
                    Session::flash('error', "file path not found.");
                }
            } else {
                Session::flash('error', "file path not found.");
            }
        } else {
            $this->validate($request, [
                'type' => 'required',
                'path' => 'required',
                'data' => 'required'
            ]);
            $path = public_path().'/'.$request->path;
            \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
            
            if ($request->type == 'folder') {
                $path .= '/'.$request->data;
                \File::isDirectory($path) or \File::makeDirectory($path, 0777, true, true);
                Session::flash('message', "Success! folder has been created");
            } else {
                if($request->hasfile('data'))
                {
                    foreach($request->file('data') as $file)
                    {
                        $name = $file->getClientOriginalName();
                        $file->move(public_path($request->path), $name);  
                    }
                }
                Session::flash('message', "Success! files has been uploaded");
            }
        }
        return redirect()->back();
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
        dd($id);
    }

    public function delTree($dir)
    { 
        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file"); 
        }

        return rmdir($dir); 
    } 
}
