<?php

namespace App\Http\Controllers\Admin;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\User;
use App\Country;
use App\Quotation;
use Excel;
use App\Exports\QuotationDetailsExcel;
use App\Exports\QuotationExportDownload;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Storage;
class HomeController
{

    public function newExportQuotationTest(Request $request)
    {
        $action_id = $request->actiton_id;
        if($action_id==1)
        {
            $data = array(array('id'=>1,'type'=>'electrical'));
            $filename = 'success_test_quotations.xlsx';
            $path =  'public/QuotationLog/'.$filename;
            $res = Excel::store(new QuotationExportDownload($data), $path);
            return [
                'success' => 2,
                'url' => $path
            ];
        }

        if($action_id == 2)
        {
            $data = array(array('id'=>1,'type'=>'electrical'));
            // $data = Quotation::get_excel_file($quotation);
            $filename = 'success_test_quotations.xlsx';
            $path =  'public/QuotationLog/'.$filename;
            $res = Excel::store(new QuotationExportDownload($data), $path);
            $fileUrl = Storage::url($path);
            return response()->download(storage_path("app/$path"));
        }
    }

    public function index()
    {
        // PIE CHART 1 STARTED..
        $unique_quotation = Quotation::select("users.id as userId","users.country_id","quotations.id as quotationId","quotations.user_id","quotations.quotation_number","countries.id","countries.country")
                        ->selectRaw('COUNT(distinct quotations.quotation_number) as count')
                        ->leftJoin("users","users.id","=","quotations.user_id")
                        ->leftJoin("countries","countries.id","=","users.country_id")
                        ->groupBy("users.country_id")
                        ->get();
        
        $country = Country::select('*')->get();
        $data = array();
        $data1 = array();

        foreach($unique_quotation as $val)
        {
            if($val->count)
            {
                $data[$val->country] = $val->count;
            }
        }
        foreach($country as $val_con)
        {
            if($val_con->country)
            {
                $data1[$val_con->country] = 0;
            }
        }
        
        $keys = array_fill_keys(array_keys($data + $data1), 0);
        $data2 = array_fill_keys(array_keys($data + $data1), 0);
        array_walk($data2, function (&$value, $key, $arrs) { $value = @($arrs[0][$key] + $arrs[1][$key]); }, array($data, $data1));
        $data3 =json_encode($data2);
        //PIE CHART 1 COMPLETED..

        //PIE CHART 2 STARTED..
        $country_quotation_value = Quotation::select('countries.id as CountryId','countries.country','users.id as UserId','users.country_id','quotations.user_id')
        ->selectRaw('SUM(total_quotation_value) as total')
        ->leftJoin('users','users.id','=','quotations.user_id')
        ->leftJoin('countries','countries.id','=','users.country_id')
        ->groupBy('users.country_id')
        ->get();
        
        $data4 = array();
        $data5 = array();

        foreach($country_quotation_value as $value)
        {
            $data4[$value->country] = round($value->total);
        }
        foreach($country as $val_country)
        {
            $data5[$val_country->country] = 0;
        }
        
        $sums = array_fill_keys(array_keys($data4 + $data5), 0);
        array_walk($sums, function (&$value, $key, $arrs) { $value = @($arrs[0][$key] + $arrs[1][$key]); }, array($data4, $data5));
        $array_merge = json_encode($sums);
        //PIE CHART 2 COMPLETED..
        $current_year = date('Y');
        $years = [];
        for($i = 0; $i<5; $i++){
            $years[] = $current_year - $i;
        } 
        return view('home',['data3'=>$data3,'array_merge'=>$array_merge, 'years' => $years]);
        // return view('home',['data3'=>$data3,'array_merge'=>$array_merge]);
    }

    public function document(){
         
        return view('admin.document.update');
    }

    public function tool_tip_page()
    {
        // $data = [
        //     [
        //     'name'=>'Booster set',
        //     'created_at'=>Carbon::now(),
        //     'updated_at'=>Carbon::now(),
        // ],

        // [
        //     'name'=>'Control Panel',
        //     'created_at'=>Carbon::now(),
        //     'updated_at'=>Carbon::now(),
        // ],

        // [
        //     'name'=>'Scp Pump Assembly',
        //     'created_at'=>Carbon::now(),
        //     'updated_at'=>Carbon::now(),
        // ],

        // [
        //     'name'=>'Atmos Giga',
        //     'created_at'=>Carbon::now(),
        //     'updated_at'=>Carbon::now(),
        // ]];
        // DB::table('parts')->insert($data);
        return view('admin.tool_tip.parts');
    }

    public function booster_set(){
        // $data = [
        //     [
        //         'component_name'=>'pump_info',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'control_panel',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'application',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],

        //     [
        //         'component_name'=>'ambient_type',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'stater_type',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'communication_protocol',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'ip_rating',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'components',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'enclosure',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'optional',
        //         'part_id' => 1,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        // ];
        //     DB::table('tool_tip')->insert($data);
        $current_data = DB::table('tool_tip')->where('part_id',1)->get();
        return view('admin.tool_tip.booster_set')->with('current_data',$current_data);
        
    }

    public function save_booster_tool_tip(Request $request){
        $data = $request->all();
        unset($data['_token']);
        foreach($data as $key => $val){
            DB::table('tool_tip')->where('component_name',$key)->where('part_id',1)->update([
                'tool_tip' => $val
            ]);
        }
        return view('admin.tool_tip.parts');
    }
    
    public function atmos_giga(){
        // $data = [
        //     [
        //         'component_name'=>'pump_model',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'impeller_material',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'motor_power',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'power_supply',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'frequency',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'no_of_poles',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'efficiency',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'motor_brand',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'application',
        //         'part_id' => 4,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
          
    
           
    
        // ];
        //     DB::table('tool_tip')->insert($data);
        $current_data = DB::table('tool_tip')->where('part_id',4)->get();
        return view('admin.tool_tip.atmos_giga')->with('current_data',$current_data);

    }

    public function control_panel(){
        // $data = [
        //     [
        //         'component_name'=>'article_number',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'no_of_pumps',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'motor_power',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'supply_voltage',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'application',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'ambient_temp',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'stater_type',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'communication_protocol',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'ip_rating',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'components',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'enclosure',
        //         'part_id' => 2,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
           
    
        // ];
        //     DB::table('tool_tip')->insert($data);


        $current_data = DB::table('tool_tip')->where('part_id',2)->get();
        return view('admin.tool_tip.control_panel')->with('current_data',$current_data);

    }

    public function scp_pumps(){
        // $data = [
        //     [
        //         'component_name'=>'pump_model',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'impeller_material',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'seal_gland_pack',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'motor_power',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'power_supply',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'frequency',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'no_of_poles',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'efficiency',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'motor_brand',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
        //     [
        //         'component_name'=>'application',
        //         'part_id' => 3,
        //         'created_at'=>Carbon::now(),
        //         'updated_at'=>Carbon::now(),
        //     ],
    
          
    
           
    
        // ];
        //     DB::table('tool_tip')->insert($data);
        $current_data = DB::table('tool_tip')->where('part_id',3)->get();
        return view('admin.tool_tip.scp_pump_assemby')->with('current_data',$current_data);

    }

    public function save_control_panel_tool_tip(Request $request){

        // dd($request);
        $data = $request->all();
        unset($data['_token']);
        
        foreach($data as $key => $val){
         
            DB::table('tool_tip')->where('component_name',$key)->where('part_id',2)->update([
                'tool_tip' => $val
            ]);
        
        }

        return view('admin.tool_tip.parts');
    }

    public function scp_t_tip(Request $request){

        // dd("here");
        
        $data = $request->all();
        unset($data['_token']);
        
        foreach($data as $key => $val){
         
            DB::table('tool_tip')->where('component_name',$key)->where('part_id',3)->update([
                'tool_tip' => $val
            ]);
        
        }
        

      

        return view('admin.tool_tip.parts');
    }

    public function giga(Request $request){

        // dd("here");

        $data = $request->all();
        unset($data['_token']);
        
        foreach($data as $key => $val){
         
            DB::table('tool_tip')->where('component_name',$key)->where('part_id',4)->update([
                'tool_tip' => $val
            ]);
        
        }
        
      

        return view('admin.tool_tip.parts');
    }

    public function setup(){
      
        return View('admin.setup.setup')->with('current_data',DB::table('setup_fields')->get());
    }

    public function setup_post(Request $request){


        $data = $request->all();
        unset($data['_token']);

        foreach($data as $key => $val){
         
            DB::table('setup_fields')->where('name',$key)->update([
                'value' => $val
            ]);
        
        }

       

        return View('admin.setup.setup')->with('current_data',DB::table('setup_fields')->get());
    }

    public function ic_margin(){
       //     for($i = 1 ; $i<=4 ; $i++){
        //     $country = [
        //         [
        //             "country" => "lebanon",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],

        //         [
        //             "country" => "syria",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],

        //         [
        //             "country" => "jordan",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],

        //         [
        //             "country" => "egypt",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],

        //         [
        //             "country" => "uae",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],

        //         [
        //             "country" => "ksa",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],

        //         [
        //             "country" => "qatar",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],

        //         [
        //             "country" => "pakistan",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],
        //         [
        //             "country" => "morocco",
        //             "value" => "9",
        //             "part_id" => $i
        //         ],
                
        //     ];
        //         DB::table('ic_margin')->insert($country);
        // }
        //     dd("herr");
        $part_id = $_GET['part_id'];
        return View('admin.ic_margin.margin',compact('part_id'))->with('current_data',DB::table('ic_margin')->where('part_id',$part_id)->get());
    }

    public function ic_margin_post(Request $request){

        $part_id = $request->part_id;

        $data = $request->all();
        unset($data['_token']);
        unset($data['part_id']);
        
        foreach($data as $key => $val){
         
            DB::table('ic_margin')->where('country',$key)->where('part_id',$part_id)->update([
                'value' => $val
            ]);
        
        }
        

        return View('admin.ic_margin.margin',compact('part_id'))->with('current_data',DB::table('ic_margin')->where('part_id',$part_id)->get());
    }

    //
    public function otp_margin(){
        $part_id = $_GET['part_id'];
        return View('admin.otp_margin.otp_margin',compact('part_id'))->with('current_data',DB::table('otp_margin')->where('part_id',$part_id)->get());
    }

    public function otp_margin_post(Request $request){
        $part_id = $request->part_id;
        $data = $request->all();
        unset($data['_token']);
        unset($data['part_id']);
        foreach($data as $key => $val){
            DB::table('otp_margin')->where('country',$key)->where('part_id',$part_id)->update([
                'value' => $val
            ]);
        }
        return View('admin.otp_margin.otp_margin',compact('part_id'))->with('current_data',DB::table('otp_margin')->where('part_id',$part_id)->get());
    }

    public function maintance_mode(Request $request){
        $lable = $request->lable;

        $maintance_mode = DB::table("setup_fields")->where('label',$lable)->pluck('value')[0];
        $title = DB::table("setup_fields")->where('label',$lable)->pluck('name')[0];
        return View('admin.maintance_mode.index',compact('maintance_mode','lable','title'));
    }

    public function maintance_mode_post(Request $request){
        $is_maintance_mode = $request->maintance_mode;
        $lable = $request->lable;
        $test = DB::table('setup_fields')
                ->where('label',$lable)
                ->update(['value'=>$is_maintance_mode]);
        $mode = DB::table('setup_fields')
                ->where('label',$lable)
                ->first();
        return back()->with('success', $mode->name.' maintance mode getting changed successfully..!!');
    }
    
    public function export_quotation()
    {
		set_time_limit(0);
        $excel_file = 'QuotationLog_' . Carbon::now()->format('m-d-Y h:i:s') . '.xlsx';
        return Excel::download(new QuotationDetailsExcel(),$excel_file);
    }

    public function newExportQuotation(Request $request)
    {
        $filename = '';
        $page = 1;
        $content = 'new';
        if (isset($request->filename)) {
            $filename = $request->filename;
        }
        if (isset($request->page)) {
            $page = $request->page;
        }
        if (isset($request->content)) {
            $content = $request->content;
        }

        $selected_year = $request->selected_year;
        $selected_month = $request->selected_month;

        // $start_date = $selected_year . '-01-01';
        // $end_date = $selected_year . '-12-31';
        $start_date = "$selected_year-$selected_month-01";
        $end_date = date('Y-m-t', strtotime($start_date));
        if ($content === 'new') {
            $filename = 'quotation_'.$selected_year.'_'.$selected_month. '_'.time().'.csv';
        }

        $perPage = 100;

        $quotation = Quotation::select('quotations.id as QuotationId','quotations.*','users.id as UserId','users.name as UserName','users.country_id','countries.id as CountryId','countries.country','customers.id as CustomerId','customers.name as CustomerName','customers.project_name','customers.country as ProjectCountry','customers.project_location as ProjectLocation','booster_carts.id as BoosterCartId','scp_carts.id as ScpCartId','atmos_carts.id as AtmosCartId','control_panel_carts.id as ControlPanelId','booster_carts.full_article_number as BFullArticleNumber','booster_carts.pump_type as BPumpType','booster_carts.supply_voltage as BSupplyVoltage','booster_carts.adder_ids as BElecticleAdderIds','booster_carts.total_price as BTotalPrice','booster_carts.qty as BQty','booster_carts.price as BUnitPrice','booster_carts.mechanical_adder_ids as BMechanicalAdderIds','booster_carts.system_pressure as BSystemPressure','booster_carts.model_no as BModelNo','booster_carts.manifold as BManifold','booster_carts.inter_company_margin as BInterCompanyMargin','booster_carts.booster_overhead as BOverHead','booster_carts.mechanical_total_adders_price as BMechanicalTotalAdderIdsPrice','booster_carts.total_adders_price as BTotalAddersPrice','booster_carts.cp_price as BCPPrice','booster_carts.cablePrice as BCablePrice','booster_carts.mechanical_system_price as BMechanicalSystemPrice','booster_carts.pump_price as BPumpPrice','booster_carts.cp_id as BCpId','booster_carts.booster_article_number as BBoosterArticleNumber','booster_carts.article_number as BArticleNumber','control_panel_carts.starter_code as CStarterCode','atmos_carts.full_article_number as AFullArticleNumber','atmos_carts.pump_id as APump_id','atmos_carts.pump_name as APumpName','atmos_carts.material_id as AMaterialId','atmos_carts.brand as ABrand','atmos_carts.power as APower','atmos_carts.no_of_pole as ANoOfPoles','atmos_carts.voltage as AVoltage','atmos_carts.frequency as AFrequency','atmos_carts.efficiency as AEfficiency','atmos_carts.adder_ids as AAdderIds','atmos_carts.qty as AQty','atmos_carts.article_number as AArticleNumber','atmos_carts.price as AUnitPrice','atmos_carts.total_price as ATotalPrice','atmos_carts.application as AApplication','atmos_carts.shipping_cost_price as AShippingCostPrice','atmos_carts.packing_charge as APackingCharge','atmos_carts.painting_charge as APaintingCharge','atmos_carts.assembly_charge as AAssemblyCharge','atmos_carts.insulate_bearing_price as AInsulateBearingPrice','atmos_carts.accesories_price as AAssesoriesPrice','atmos_carts.inter_company_margin_price as AInterCompanyMarginPrice','atmos_carts.overhead_price as AOverHead','atmos_carts.bare_pump_price as APumpPrice','control_panel_carts.full_article_number as CFatmos_cartsullArticleNumber','control_panel_carts.control_panel_id as CControlPanelId','control_panel_carts.no_of_pump_id as CNoOfPumpId','control_panel_carts.power_id as CPowerId','control_panel_carts.voltage_id as CVoltageId','control_panel_carts.application_id as CApplicationId','control_panel_carts.ambient_temp_id as CAmbientTempId','control_panel_carts.stater_type_id as CStaterTypeId','control_panel_carts.full_article_number as CFullArticleNumber','control_panel_carts.article_number as CArticleNumber','control_panel_carts.communication_protocol_id as CCommunicationProtocolId','control_panel_carts.ip_rating_id as CIpRatingId','control_panel_carts.components_id as CComponentId','control_panel_carts.enclosure_id as CEnclosureId','control_panel_carts.qty as CQty','control_panel_carts.price as CUnitPrice','control_panel_carts.total_price as CTotalPrice','control_panel_carts.adder_ids as CAdderIds','control_panel_carts.intercompany_margin as CInterCompanyMargin','control_panel_carts.overhead as COverHead','control_panel_carts.price as CPrice','scp_carts.full_article_number as SFullArticleNumber','scp_carts.pump_id as SPumpId','scp_carts.pump_name as SPumpName','scp_carts.material_id as SMaterialId','scp_carts.article_number as SArticleNumber','scp_carts.seal_gland_pack_id as SSealGlandPackId','scp_carts.master_id as SMasterId','scp_carts.brand as SBrand','scp_carts.power as SPower','scp_carts.power as SPower','scp_carts.no_of_pole as SNoOfPole','scp_carts.voltage as SVoltage','scp_carts.frequency as SFrequency','scp_carts.efficiency as SEfficiency','scp_carts.adder_ids as SAdderIds','scp_carts.qty as SQty','scp_carts.price as SUnitPrice','scp_carts.application as SApplication','scp_carts.total_price as STotalPrice','scp_carts.shipping_cost_price as SShippingCostPrice','scp_carts.packing_charge as SPackingCharge','scp_carts.painting_charge as SPaintingCharge','scp_carts.assembly_charge as SAssemblyCharge','scp_carts.insulate_bearing_price as SInsulateBearingPrice','scp_carts.accesories_price as SAssesoriesPrice','scp_carts.overhead_price as SOverHead','scp_carts.bare_pump_price as SPumpPrice','scp_carts.inter_company_margin_price as SInterCompanyMargin',DB::raw("(SELECT value from powers where id = booster_carts.motor_power) as BMotorPower"),DB::raw("(SELECT value from powers where id = control_panel_carts.power_id) as CPower"),DB::raw("(SELECT value from voltages where id = booster_carts.supply_voltage) as BSupplyVoltages"),DB::raw("(SELECT value from voltages where id = control_panel_carts.voltage_id) as CSupplyVoltages"),DB::raw("(SELECT value from applications where id = control_panel_carts.application_id) as CApplicationIdd"),DB::raw("(SELECT value from enclousres where id = control_panel_carts.enclosure_id) as CEnclosure"),DB::raw("(SELECT value from components where id = control_panel_carts.components_id) as CComponent"),DB::raw("(SELECT value from ip_ratings where id = control_panel_carts.ip_rating_id) as CIpRating"),DB::raw("(SELECT value from comunication_protocols where id = control_panel_carts.communication_protocol_id) as CCommunicationProtocol"),DB::raw("(SELECT value from starter_types where id = control_panel_carts.stater_type_id) as CStarterType"),DB::raw("(SELECT value from ambient_temps where id = control_panel_carts.ambient_temp_id) as CAmbientTemp"))
                    // ->whereYear('quotations.created_at', $selected_year)
                    ->whereBetween('quotations.created_at', [$start_date . ' 00:00:00', $end_date . ' 23:59:59'])
                    ->leftJoin('users','users.id','=','quotations.user_id')
                    ->leftJoin('customers','customers.id','=','quotations.customer_id')
                    ->leftJoin('countries','countries.id','=','users.country_id')
                    ->leftJoin('atmos_carts', function ($join) {
                        $join->on('atmos_carts.id', '=', 'quotations.cp_cart_id')
                             ->where('quotations.cart_model_name', '=', 'atmos');
                            })
                    ->leftJoin('booster_carts', function ($join) {
                                $join->on('booster_carts.id', '=', 'quotations.cp_cart_id')
                                ->where('quotations.cart_model_name', '=', 'booster');
                            })
                    ->leftJoin('control_panel_carts', function ($join) {
                        $join->on('control_panel_carts.id', '=', 'quotations.cp_cart_id')
                        ->where('quotations.cart_model_name', '=', 'controlpanel');
                            })
                    ->leftJoin('scp_carts', function ($join) {
                        $join->on('scp_carts.id', '=', 'quotations.cp_cart_id')
                             ->where('quotations.cart_model_name', '=', 'scp');
                               })
                    ->leftJoin('firefighting_carts', function ($join) {
                        $join->on('firefighting_carts.id', '=', 'quotations.cp_cart_id')
                             ->where('quotations.cart_model_name', '=', 'firefighting');
                               })
                               ->paginate($perPage, ['*'], 'page', $page);
                               // ->get();
        // if (count($quotation) > 0) {
        //     $data = Quotation::get_excel_file($quotation);
        //     $filename = $selected_year.'-'.'quotations.xlsx';
        //     $path = storage_path('app/public/QuotationLog').'/'.$filename;
        //     Excel::store(new QuotationExportDownload($data), $path);
        //     return [
        //         'success' => 2,
        //         'url' => url('storage/app/public/QuotationLog').'/'.$filename
        //     ];
        // }

        // else{
        //     return[
        //         'success' => 'no-data'
        //     ];
        // }

        if ($quotation->total() == 0) {
            return response()->json(['success' => 'no-data', 'msg' => 'No quotation data available for the selected Month & Year']);
        }

        if ($content !== 'movefile') {
            $percentage = ($page / $quotation->lastPage()) * 100;

            return response()->json([
                'success' => 1,
                'content' => $content,
                'filename' => $filename,
                'nextPage' => $page + 1,
                'msg' => 'Processing page ' . $page,
                'progress' => 'show',
                'percentage' => round($percentage),
                'currentPage' => $page,
                'lastPage' => $quotation->lastPage()
            ]);
        } else {
            $url = Storage::url($filename);
            return response()->json([
                'success' => 2,
                'url' => $url
            ]);
        }
    }

    public function allQuotationList(Request $request)
    {
        if (isset($request->quotation) && base64_decode($request->quotation, true)) {
            $path = base64_decode($request->quotation);
            unlink($path);
            return back()->with('success', 'Quotation Log Remove.');
        }
        $path = storage_path('app/public/QuotationLog').'/';
        if (file_exists($path)) {
            $files = array_diff(scandir($path), array('.', '..'));
        } else {
            $files = [];
        }
        $data['files'] = $files;
        $data['path'] = $path;
        $data['url'] = url('storage/QuotationLog');
        return view('admin.all-quotation-list', $data);
    }
}
