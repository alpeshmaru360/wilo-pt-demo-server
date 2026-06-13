<?php

namespace App\Http\Controllers\Admin;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\ScpCart;
use App\ScpvCart; // A Code: 06-11-2025
use App\ArticleFile;
use App\ManualFile;
use App\AtmosCart;
use App\ControlPanelCart;
use App\Models\BoosterCart;
use Session;

class DocumentController
{    
    public function index(){         
        return view('admin.document.update');
    }

    public function getArticleByModule(){
        $module=$_REQUEST["module"];
        // if($module==""){

        // }

        if($module=="booster_set"){
            $data=BoosterCart::whereNotNull('full_article_number')->groupBy('full_article_number')->get();
        }elseif($module=="control_panel"){
            $data=ControlPanelCart::whereNotNull('full_article_number')->groupBy('full_article_number')->get();
        }elseif($module=="scp_pump_assembly"){
            $data=ScpCart::whereNotNull('full_article_number')->groupBy('full_article_number')->get();

        // A Code: 06-11-2025 Start
        }elseif($module=="scpv_pump_assembly"){
            $data=ScpvCart::whereNotNull('full_article_number')->groupBy('full_article_number')->get();
        // A Code: 06-11-2025 End

        }elseif($module=="atmos_giga"){
            $data=AtmosCart::whereNotNull('full_article_number')->groupBy('full_article_number')->get();
        }


        // echo "<option value=''>Select</option>";
        if(count($data)>0){
            foreach($data as $rs){
                echo "<option value='".$rs->full_article_number."'>".$rs->full_article_number." </option>";
            }
        }
    }

    public function getArticleDetail(){
        $module=$_REQUEST["module"];
        $article=$_REQUEST["article"];
       
        $moduleName="";
        if($module=="booster_set"){
            $moduleName="Booster Set";
        }elseif($module=="control_panel"){
            $moduleName="Control Panel";
        }elseif($module=="scp_pump_assembly"){
            $moduleName="SCP Pump Assembly";

        // A Code: 06-11-2025 Start
        }elseif($module=="scpv_pump_assembly"){
            $moduleName="SCPV Pump Assembly";
        // A Code: 06-11-2025 End

        }elseif($module=="atmos_giga"){
            $moduleName="Atmos GIGA";
        }
      
        //$data = ScpCart::where('article_number', $article)->get();
        $dataExisting = ArticleFile::where('article_number', $article)->get();
        $dataExistingCount = ArticleFile::where('article_number', $article)->count();
        //die( $dataExistingCount."===========".$module."BBBBBBBBBBBBBBB".$article."DDDDDDDDD".count($dataExisting));
        echo "<tr>
        <td>".$moduleName."</td>
        <td>". $article."</td>
        <td> <input type='file' id='file' name='file' class='form-control' accept='image/x-png,image/gif,image/jpeg, .pdf, .xls, .xlsx, .doc, .docx' required > ";
        
        if($dataExistingCount>0){
           
            foreach($dataExisting as $rs){
                echo " <a href='". asset('public/articles/'. $dataExisting[0]->file_name) ."' target='_blank'><i class='fas fa-file'></i></a>  ";
                echo " <a href='document/delete/".$dataExisting[0]->file_name."'  ><i class='fas fa-trash'></i></a>   ";
            }
            
        }
        
        

        echo "</td></tr>";
    }
 

    public function upload(Request $request) {
        $file = $request->file;
        if (!empty($file)) {
            $path = 'public/articles/';
            $fileName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $fileNameComplete=$request->article."_". uniqid() .".".  $ext;
            if ($file->move($path, $fileNameComplete)) {
                $is_uploaded = $fileNameComplete;
            }

            $insertData = [];

            $insertData[] = array(  
            'article_number' => $request->article ,
            'file_name' => $fileNameComplete);
             
            ArticleFile::insert($insertData);
            Session::flash('message', "Success! Your file has been uploaded ");
            return redirect()->back();    
        }else{
            Session::flash('error', "File is empty");
            return redirect()->back();
    
        }  
    }
   
    public function deleteArticle(Request $request){
        ArticleFile::where("file_name",$request->article)->delete();
        unlink("public/articles/".$request->article);        
        Session::flash('message', "Article file has been deleted");
        return redirect()->back();
    }
 


    public function manual(){
         
        return view('admin.manual.update');
    }


    public function getManualByModule(){
        $module=$_REQUEST["module"];
        $moduleName="";
        if($module=="booster_set"){
            $moduleName="Booster Set";
        }elseif($module=="control_panel"){
            $moduleName="Control Panel";
        }elseif($module=="scp_pump_assembly"){
            $moduleName="SCP Pump Assembly";

        // A Code: 06-11-2025 Start
        }elseif($module=="scpv_pump_assembly"){
            $moduleName="SCPV Pump Assembly";
        // A Code: 06-11-2025 End

        }elseif($module=="atmos_giga"){
            $moduleName="Atmos GIGA";
        }
      
        //$data = ScpCart::where('article_number', $article)->get();
        $dataExisting = ManualFile::where('module_name', $module)->get();
        $dataExistingCount = ManualFile::where('module_name', $module)->count();
        //die( $dataExistingCount."===========".$module."DDDDDDDDD".count($dataExisting));
        echo "<tr>
        <td>".$moduleName."</td>
     
        <td> <input type='file' id='file' name='file' class='form-control h-100' accept='image/x-png,image/gif,image/jpeg, .pdf, .xls, .xlsx, .doc, .docx' required > ";
        
        if($dataExistingCount>0){
           
            foreach($dataExisting as $rs){
                // A Code: 06-11-2025 Start
                echo " <a href='". asset('manuals/'. $rs->file_name) ."' target='_blank'><i class='fas fa-file'></i></a>  ";                
                echo " <a href='manuals/delete/".$rs->file_name."'  ><i class='fas fa-trash'></i></a>   ";
                // A Code: 06-11-2025 End
            }
            
        }      
        echo "</td></tr>";         
    }
    

    public function manualUpload(Request $request) {
        $file = $request->file;
        // die($request->module."===".uniqid());
        if (!empty($file)) {
            $path = 'public/manuals/';
            $fileName = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $fileNameComplete=$request->module."_". uniqid() .".".  $ext;
            if ($file->move($path, $fileNameComplete)) {
                $is_uploaded = $fileNameComplete;
            }

            $insertData = [];

            $insertData[] = array(  
            'module_name' => $request->module ,
            'file_name' => $fileNameComplete);
             
            ManualFile::insert($insertData);
            Session::flash('message', "Success! Your file has been uploaded ");
            return redirect()->back();
    
        }else{
            Session::flash('error', "File is empty");
            return redirect()->back();
    
        }

  
    }


    public function deleteManual(Request $request){
        ManualFile::where("file_name",$request->file)->delete();
        unlink("public/manuals/".$request->file);        
        Session::flash('message', "File has been deleted");
        return redirect()->back();
    }
 
}
