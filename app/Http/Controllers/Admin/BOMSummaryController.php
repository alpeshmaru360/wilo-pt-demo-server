<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MassDestroyUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Role;
use App\ControlPanel;
use DB;
use App\Models\FireFighting\FireFightingCarts;
use App\Models\FireFighting\ElectricalPump;
use App\Models\FireFighting\DieselPump;
use App\Models\FireFighting\JockeyPump;
use App\Models\BoosterCart;
use App\Models\Quotation;
use App\Models\User;
use App\Models\Customer;
use App\Models\Country;
use Illuminate\Support\Arr;
use App\Models\AtmosCart;
use App\Models\ScpCart;
use App\Models\ScpvCart; // A Code: 23-02-2026
use App\Models\ControlPanelCart;
use App\Models\FirefightingCart;
use App\AtmosPump;
use App\ScpPumpType;
use App\ScpvPumpType; // A Code: 23-02-2026
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Frontend\AtmosGigaController;
use App\Http\Controllers\Frontend\ScpController;
use App\Http\Controllers\Frontend\ScpvController; // A Code: 23-02-2026
use App\Http\Controllers\Frontend\BoosterSetController;
use App\Http\Controllers\Frontend\ControlpanelController;
use App\Http\Controllers\Frontend\CPCartController;
use App\Http\Controllers\Frontend\FireFighting\FireFightingPumpController;
use Illuminate\Support\Str;

class BOMSummaryController extends Controller
{
    public function index(Request $request)
    {
        $query = Quotation::query()
            ->select([
                'quotations.quotation_number as QuotationId',
                'quotations.customer_id',
                'quotations.user_id',
                'quotations.cp_cart_id',
                'quotations.cart_model_name',
                'quotations.created_at',
            ])
            ->with([
                'user:id,name,country_id',
                'user.country:id,country',
                'customer:id,name,project_name,project_location,country',
                'boosterCart:id,full_article_number,pump_type,supply_voltage,total_price,qty,price,inter_company_margin,model_no,booster_overhead',
                'atmosCart:id,full_article_number,pump_name,brand,power,qty,price,total_price,inter_company_margin_price,overhead_price,assembly_charge,painting_charge,packing_charge',
                'scpCart:id,full_article_number,pump_name,brand,power,qty,price,total_price,inter_company_margin_price,overhead_price,assembly_charge,painting_charge,packing_charge',
                // A Code: 23-02-2026
                'scpvCart:id,full_article_number,pump_name,brand,power,qty,price,total_price,inter_company_margin_price,overhead_price,assembly_charge,painting_charge,packing_charge',
                'controlPanelCart:id,full_article_number,article_number,qty,price,total_price,intercompany_margin,overhead',
                'firefightingCart:id,full_article_number,qty,price,total_price,inter_company_margin_price,pump_models,overhead_price',
            ]);

        // Apply filters 
        // if ($request->filled('month')) {
        //     $month = $request->month;
        //     $query->whereMonth('created_at', date('m', strtotime($month . ' 1')));
        // }

        // Apply filters
        if ($request->filled('month')) {
            // The input is like "2025-08"
            $monthYear = explode('-', $request->month);
            if (count($monthYear) === 2) {
                $year = $monthYear[0];
                $month = $monthYear[1];
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            }
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('full_article_number')) {
            $articleNumber = $request->full_article_number;

            $query->where(function ($q) use ($articleNumber) {
                $q->where(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'booster')
                         ->whereHas('boosterCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'atmos')
                         ->whereHas('atmosCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'scp')
                         ->whereHas('scpCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })
                // A Code: 23-02-2026 Start
                ->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'scpv')
                         ->whereHas('scpvCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })
                // A Code: 23-02-2026 End
                ->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'controlPanel')
                         ->whereHas('controlPanelCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'firefighting')
                         ->whereHas('firefightingCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                });
            });
        }

        // Pagination with order by
        $quotations = $query->orderBy('quotations.id', 'desc')
            ->paginate(20)
            ->appends([
                'month' => $request->month,
                'date' => $request->date,
                'full_article_number' => $request->full_article_number,
            ])
            ->withPath(url('admin/bom_summary'));


        // Map each quotation to response array
        $response = $quotations->map(function ($quotation) {
            $date = $quotation->created_at ? new \DateTime($quotation->created_at) : null;
            
            $articleNo = '-';
            $description = '-';
            $qty = 0;            
            $unitPrice = 0;
            $totalPrice = 0;
            $interMargin = 0;
            $overhead = 0;

            $bomLabourCostAndCharges= 0;

            $cart_id = 0;

            if($quotation->cart_model_name == 'booster'){
                $articleNo = $quotation->boosterCart->full_article_number ?? '-';     
                $description = $quotation->boosterCart->model_no ?? '-';
                $qty = $quotation->boosterCart->qty ?? 0;   
                $unitPrice = $quotation->boosterCart->price ?? 0;   
                $totalPrice = $quotation->boosterCart->total_price ?? 0;   
                $interMargin = $quotation->boosterCart->inter_company_margin ?? 0;   
                $overhead = $quotation->boosterCart->booster_overhead ?? 0;   

                $cart_id = $quotation->boosterCart->id ?? '-'; 

                $boosterSetController = new BoosterSetController();
                $cartData = $boosterSetController->cartItems($cart_id, true);
                $bom = Arr::only($cartData, [
                    'boosterCartData',
                    'bomSummaryItems',
                    'bomSummarycpBoosterItems'
                ]);
                $bom_total_cost = $this->extractBoosterBOMChargesAndCosts($bom);
                $bomLabourCostAndCharges = $bom_total_cost['totalCharges'];
                // $bomLabourCostAndCharges += ($quotation->atmosCart->assembly_charge ?? 0) +
                //                             ($quotation->atmosCart->painting_charge ?? 0) +
                //                             ($quotation->atmosCart->packing_charge ?? 0);
                                            
            }else if($quotation->cart_model_name == 'atmos'){
                $articleNo = $quotation->atmosCart->full_article_number ?? '-';         
                $description = $quotation->atmosCart->pump_name ?? '-';
                $qty = $quotation->atmosCart->qty ?? 0;   
                $unitPrice = $quotation->atmosCart->price ?? 0;   
                $totalPrice = $quotation->atmosCart->total_price ?? 0;   
                $interMargin = $quotation->atmosCart->inter_company_margin_price ?? 0;   
                $overhead = $quotation->atmosCart->overhead_price ?? 0;   

                $cart_id = $quotation->atmosCart->id ?? '-'; 

                $atmosGigaController = new AtmosGigaController();
                $cartData = $atmosGigaController->cartItems($cart_id, true);

                $bom = Arr::only($cartData, [
                    'items',
                    'adderData',
                    'atmosBOMitems',
                    'atmosCart'
                ]);
                $bom_total_cost = $this->extractAtmosBOMChargesAndCosts($bom);
                $bomLabourCostAndCharges = $bom_total_cost['totalCharges'];
                $bomLabourCostAndCharges += ($quotation->atmosCart->assembly_charge ?? 0) +
                                            ($quotation->atmosCart->painting_charge ?? 0) +
                                            ($quotation->atmosCart->packing_charge ?? 0);

            }            
            else if($quotation->cart_model_name == 'scp'){
                $articleNo = $quotation->scpCart->full_article_number ?? '-';           
                $description = $quotation->scpCart->pump_name ?? '-';
                $qty = $quotation->scpCart->qty ?? 0;
                $unitPrice = $quotation->scpCart->price ?? 0;
                $totalPrice = $quotation->scpCart->total_price ?? 0;
                $interMargin = $quotation->scpCart->inter_company_margin_price ?? 0;
                $overhead = $quotation->scpCart->overhead_price ?? 0;
                $cart_id = $quotation->scpCart->id ?? '-';
                $bomLabourCostAndCharges += ($quotation->scpCart->assembly_charge ?? 0) +
                                            ($quotation->scpCart->painting_charge ?? 0) +
                                            ($quotation->scpCart->packing_charge ?? 0);

            }
            // A Code: 23-02-2026 Start
            else if($quotation->cart_model_name == 'scpv'){
                $articleNo = $quotation->scpvCart->full_article_number ?? '-';           
                $description = $quotation->scpvCart->pump_name ?? '-';
                $qty = $quotation->scpvCart->qty ?? 0;
                $unitPrice = $quotation->scpvCart->price ?? 0;
                $totalPrice = $quotation->scpvCart->total_price ?? 0;
                $interMargin = $quotation->scpvCart->inter_company_margin_price ?? 0;
                $overhead = $quotation->scpvCart->overhead_price ?? 0;
                $cart_id = $quotation->scpvCart->id ?? '-';
                $bomLabourCostAndCharges += ($quotation->scpvCart->assembly_charge ?? 0) +
                                            ($quotation->scpvCart->painting_charge ?? 0) +
                                            ($quotation->scpvCart->packing_charge ?? 0);

            }
            // A Code: 23-02-2026 End            
            else if($quotation->cart_model_name == 'controlpanel'){
                $articleNo = $quotation->controlPanelCart->full_article_number ?? '-';              
                $description = $quotation->controlPanelCart->article_number ?? '-';
                $qty = $quotation->controlPanelCart->qty ?? 0;
                $unitPrice = $quotation->controlPanelCart->price ?? 0;
                $totalPrice = $quotation->controlPanelCart->total_price ?? 0;
                $interMargin = $quotation->controlPanelCart->intercompany_margin ?? 0;
                $overhead = $quotation->controlPanelCart->overhead ?? 0;

                $cart_id = $quotation->controlPanelCart->id ?? '-'; 

                $controlPanelController = new CPCartController();
                $cartData = $controlPanelController->cartItems($cart_id, true);
                $bom = Arr::only($cartData, [
                    'bomSummaryItems'
                ]);

                $bom_total_cost = $this->extractControlPanelBOMChargesAndCosts($bom);
                $bomLabourCostAndCharges = $bom_total_cost['totalCharges'];
                // $bomLabourCostAndCharges += ($quotation->controlPanelCart->assembly_charge ?? 0) +
                //                             ($quotation->controlPanelCart->painting_charge ?? 0) +
                //                             ($quotation->controlPanelCart->packing_charge ?? 0);

            }else if($quotation->cart_model_name == 'firefighting'){
                
                $articleNo = $quotation->firefightingCart->full_article_number ?? '-';                
                $description = $quotation->firefightingCart->pump_models ?? '-';
                $qty = $quotation->firefightingCart->qty ?? 0;
                $unitPrice = $quotation->firefightingCart->price ?? 0;
                $totalPrice = $quotation->firefightingCart->total_price ?? 0;
                $interMargin = $quotation->firefightingCart->inter_company_margin_price ?? 0;
                $overhead = $quotation->firefightingCart->overhead_price ?? 0;
                $cart_id = $quotation->firefightingCart->id ?? '-'; 

            }

            $mfcPerUnit = $unitPrice * $interMargin;

            if (!empty($overhead) && $overhead != 0) {
                $overheadPerUnit = $mfcPerUnit * ($overhead - 1) / $overhead;
            } else {
                $overheadPerUnit = 0; // fallback if $overhead is 0 or null
            }
            $bomDirectMaterialCost = $mfcPerUnit - $overheadPerUnit - $bomLabourCostAndCharges;
            return [
                'ItemWiseId' => $cart_id ,
                'date' => $date ? $date->format('d-m-Y') : '-',
                'country' => $quotation->user->country->country ?? '-',
                'customer_name' => $quotation->customer->name ?? '-',
                'quotation_no' => $quotation->QuotationId,
                'project_name' => $quotation->customer->project_name ?? '-',
                'project_country' => $quotation->customer->country ?? '-',
                'article_no' => $articleNo,
                'description' => $description,
                'Module' => $quotation->cart_model_name,
                'qty' => $qty,
                'unit_price' => round($unitPrice, 2),
                'total_price' => round($totalPrice, 2),
                'inter_company_margin_price' => round($interMargin, 2),
                'MFC_per_unit' => round($mfcPerUnit, 2),
                'overhead' => round($overhead, 2),                
                'Overhead_per_unit' => round($overheadPerUnit, 2),
                'BOM_labour_charges_and_costs' => round($bomLabourCostAndCharges, 2),
                'BOM_direct_material_cost' => round($bomDirectMaterialCost, 2)
            ];
        });
        return view('admin.bom_summary.index', [
            'response' => $response,
            'quotations' => $quotations // Needed for pagination links
        ]);
    }

    // A Code: 22-09-2025 Start
    public function exportCSV(Request $request)
    {
        // Start base query
        $query = Quotation::query()
            ->select([
                'quotations.quotation_number as QuotationId',
                'quotations.customer_id',
                'quotations.user_id',
                'quotations.cp_cart_id',
                'quotations.cart_model_name',
                'quotations.created_at',
            ])
            ->with([
                'user:id,name,country_id',
                'user.country:id,country',
                'customer:id,name,project_name,project_location,country',
                'boosterCart:id,full_article_number,pump_type,supply_voltage,total_price,qty,price,inter_company_margin,model_no,booster_overhead',
                'atmosCart:id,full_article_number,pump_name,brand,power,qty,price,total_price,inter_company_margin_price,overhead_price,assembly_charge,painting_charge,packing_charge',
                'scpCart:id,full_article_number,pump_name,brand,power,qty,price,total_price,inter_company_margin_price,overhead_price,assembly_charge,painting_charge,packing_charge',
                // A Code: 24-02-2026
                'scpvCart:id,full_article_number,pump_name,brand,power,qty,price,total_price,inter_company_margin_price,overhead_price,assembly_charge,painting_charge,packing_charge',
                'controlPanelCart:id,full_article_number,article_number,qty,price,total_price,intercompany_margin,overhead',
                'firefightingCart:id,full_article_number,qty,price,total_price,inter_company_margin_price,pump_models,overhead_price',
            ]);

        // Apply filters
        if ($request->filled('month')) {
            // The input is like "2025-08"
            $monthYear = explode('-', $request->month);
            if (count($monthYear) === 2) {
                $year = $monthYear[0];
                $month = $monthYear[1];
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
            }
        }       

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->filled('full_article_number')) {
            $articleNumber = $request->full_article_number;

            $query->where(function ($q) use ($articleNumber) {
                $q->where(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'booster')
                         ->whereHas('boosterCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'atmos')
                         ->whereHas('atmosCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })
                ->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'scp')
                         ->whereHas('scpCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })
                // A Code: 23-02-2026 Start
                ->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'scpv')
                         ->whereHas('scpvCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })
                // A Code: 23-02-2026 End
                ->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'controlPanel')
                         ->whereHas('controlPanelCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                })->orWhere(function ($subQ) use ($articleNumber) {
                    $subQ->where('cart_model_name', 'firefighting')
                         ->whereHas('firefightingCart', fn($sub) => $sub->where('full_article_number', $articleNumber));
                });
            });
        }

        // Fetch all quotations (no pagination for export, ordered by id desc)
        $quotations = $query->orderBy('quotations.id', 'desc')->get();

        // CSV headers
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bom_summary_export_' . $timestamp . '.csv"',
        ];

        $columns = [
            'SR No', 'Date', 'Month', 'Country', 'Customer Name', 'Quotation Number', 
            'Project Name', 'Project Country', 'Full Article Number', 'Product Description', 'Product Module', 
            'Quantity', 'Unit Price', 'Total Price', 'Intercompany Margin', 'MFC/Unit', 'Overhead',
            'Overhead / Unit', 'BOM Labour Charges', 'BOM Direct Material Cost'
        ];

        $callback = function () use ($quotations, $columns) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, $columns);

            $sr_no = 1;

            foreach ($quotations as $quotation) {
                $date = $quotation->created_at ? Carbon::parse($quotation->created_at)->format('d M Y') : 'N/A';
                $month = $quotation->created_at ? Carbon::parse($quotation->created_at)->format('F') : 'N/A';               
                
                $articleNo = '-';                
                $description = '-';
                $module = $quotation->cart_model_name;
                $qty = 0;
                $unitPrice = 0;
                $totalPrice = 0;
                $interMargin = 0;
                $overhead = 0;

                $bomLabourCostAndCharges= 0;

                $cart_id = 0;
                
                $bomLabourCost = 0;
                $bomLabourCharges = 0;

                if($quotation->cart_model_name == 'booster'){

                    //$cart = $quotation->boosterCart;
                    $cart = optional($quotation->boosterCart); // A Code: 27-06-2026

                    $articleNo   = $cart->full_article_number ?? null;
                    $description = $cart->model_no ?? null;
                    $qty         = $cart->qty ?? 0;
                    $unitPrice   = $cart->price ?? 0;
                    $totalPrice  = $cart->total_price ?? 0;
                    $interMargin = $cart->inter_company_margin ?? 0;
                    $overhead    = $cart->booster_overhead ?? 0;

                    //$cart_id = $quotation->boosterCart->id ?? '-'; 
                    $cart_id = $cart->id ?? '-'; // A Code: 27-06-2026

                    $boosterSetController = new BoosterSetController();
                    $cartData = $boosterSetController->cartItems($cart_id, true);
                    $bom = Arr::only($cartData, [
                        'boosterCartData',
                        'bomSummaryItems',
                        'bomSummarycpBoosterItems'
                    ]);
                    $bom_total_cost = $this->extractBoosterBOMChargesAndCosts($bom);
                    $bomLabourCostAndCharges = $bom_total_cost['totalCharges'];

                }else if($quotation->cart_model_name == 'atmos'){

                    //$cart = $quotation->atmosCart;
                    $cart = optional($quotation->atmosCart); // A Code: 27-06-2026

                    $articleNo   = $cart->full_article_number ?? null;
                    $description = $cart->pump_name ?? null;
                    $qty         = $cart->qty ?? 0;
                    $unitPrice   = $cart->price ?? 0;
                    $totalPrice  = $cart->total_price ?? 0;
                    $interMargin = $cart->inter_company_margin_price ?? 0;
                    $overhead    = $cart->overhead_price ?? 0;

                    $cart_id = $cart->id ?? '-'; 

                    $atmosGigaController = new AtmosGigaController();
                    $cartData = $atmosGigaController->cartItems($cart_id, true);

                    $bom = Arr::only($cartData, [
                        'items',
                        'adderData',
                        'atmosBOMitems',
                        'atmosCart'
                    ]);
                    $bom_total_cost = $this->extractAtmosBOMChargesAndCosts($bom);
                    $bomLabourCostAndCharges = $bom_total_cost['totalCharges'];
                    $bomLabourCostAndCharges += ($quotation->atmosCart->assembly_charge ?? 0) +
                                            ($quotation->atmosCart->painting_charge ?? 0) +
                                            ($quotation->atmosCart->packing_charge ?? 0);

                }
                else if($quotation->cart_model_name == 'scp'){

                    //$cart = $quotation->scpCart;
                    $cart = optional($quotation->scpCart); // A Code: 27-06-2026

                    $articleNo   = $cart->full_article_number ?? null;
                    $description = $cart->pump_name ?? null;
                    $qty         = $cart->qty ?? 0;
                    $unitPrice   = $cart->price ?? 0;
                    $totalPrice  = $cart->total_price ?? 0;
                    $interMargin = $cart->inter_company_margin_price ?? 0;
                    $overhead    = $cart->overhead_price ?? 0;

                    $bomLabourCostAndCharges += ($quotation->scpCart->assembly_charge ?? 0) +
                                            ($quotation->scpCart->painting_charge ?? 0) +
                                            ($quotation->scpCart->packing_charge ?? 0);

                }
                // A Code: 23-02-2026 Start
                else if($quotation->cart_model_name == 'scpv'){

                    //$cart = $quotation->scpvCart;
                    $cart = optional($quotation->scpvCart); // A Code: 27-06-2026

                    $articleNo   = $cart->full_article_number ?? null;
                    $description = $cart->pump_name ?? null;
                    $qty         = $cart->qty ?? 0;
                    $unitPrice   = $cart->price ?? 0;
                    $totalPrice  = $cart->total_price ?? 0;
                    $interMargin = $cart->inter_company_margin_price ?? 0;
                    $overhead    = $cart->overhead_price ?? 0;

                    $bomLabourCostAndCharges += ($quotation->scpvCart->assembly_charge ?? 0) +
                                            ($quotation->scpvCart->painting_charge ?? 0) +
                                            ($quotation->scpvCart->packing_charge ?? 0);

                }
                // A Code: 23-02-2026 End              
                else if($quotation->cart_model_name == 'controlpanel'){  

                    //$cart = $quotation->controlPanelCart;
                    $cart = optional($quotation->controlPanelCart); // A Code: 27-06-2026

                    $articleNo   = $cart->full_article_number ?? null;
                    $description = $cart->article_number ?? null;
                    $qty         = $cart->qty ?? 0;
                    $unitPrice   = $cart->price ?? 0;
                    $totalPrice  = $cart->total_price ?? 0;
                    $interMargin = $cart->intercompany_margin ?? 0;
                    $overhead    = $cart->overhead ?? 0;

                    //$cart_id = $quotation->controlPanelCart->id ?? '-'; 
                    $cart_id = $cart->id ?? '-'; // A Code: 27-06-2026

                    $controlPanelController = new CPCartController();
                    $cartData = $controlPanelController->cartItems($cart_id, true);
                    $bom = Arr::only($cartData, [
                        'bomSummaryItems'
                    ]);

                    $bom_total_cost = $this->extractControlPanelBOMChargesAndCosts($bom);
                    $bomLabourCostAndCharges = $bom_total_cost['totalCharges'];
                    // $bomLabourCostAndCharges += ($quotation->controlPanelCart->assembly_charge ?? 0) +
                    //                             ($quotation->controlPanelCart->painting_charge ?? 0) +
                    //                             ($quotation->controlPanelCart->packing_charge ?? 0);

                }else if($quotation->cart_model_name == 'firefighting'){
                    
                    //$cart = $quotation->firefightingCart;
                    $cart = optional($quotation->firefightingCart); // A Code: 27-06-2026

                    $articleNo   = $cart->full_article_number ?? null;
                    $description = $cart->pump_models ?? null;
                    $qty         = $cart->qty ?? 0;
                    $unitPrice   = $cart->price ?? 0;
                    $totalPrice  = $cart->total_price ?? 0;
                    $interMargin = $cart->inter_company_margin_price ?? 0;
                    $overhead    = $cart->overhead_price ?? 0;

                    //$cart_id = $quotation->firefightingCart->id ?? '-'; 
                    $cart_id = $cart->id ?? '-'; // A Code: 27-06-2026
                }

                $mfcPerUnit = $unitPrice * $interMargin;

                if (!empty($overhead) && $overhead != 0) {
                    $overheadPerUnit = $mfcPerUnit * ($overhead - 1) / $overhead;
                } else {
                    $overheadPerUnit = 0; // fallback if $overhead is 0 or null
                }
                $bomDirectMaterialCost = $mfcPerUnit - $overheadPerUnit - $bomLabourCostAndCharges;


                // Excel-safe + UTF-8
                fputcsv($file, [
                    $sr_no++,
                    $date,
                    $month,
                    $quotation->user->country->country ?? 'N/A',
                    $quotation->customer->name ?? 'N/A',
                    '="' . ($quotation->QuotationId ?? 'N/A') . '"',           
                    '="' . ($quotation->customer->project_name ?? 'N/A') . '"', 
                    $quotation->customer->country ?? 'N/A',
                    $articleNo,
                    $description,
                    $module,
                    $qty,
                    round($unitPrice, 2),
                    round($totalPrice, 2),
                    round($interMargin, 2),
                    round($mfcPerUnit, 2), // MFC/Unit
                    round($overhead, 2),
                    round($overheadPerUnit, 2), // Overhead / Unit
                    round($bomLabourCostAndCharges, 2), // BOM Labour Charges
                    round($bomDirectMaterialCost, 2), // BOM Direct Material Cost
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
    // A Code: 22-09-2025 End

    private function extractControlPanelBOMChargesAndCosts($bom)
    {
        $totalCharges = 0;
        $allItems = [];

        foreach ($bom as $key => $value) {
            if ($value instanceof \Illuminate\Support\Collection) {
                foreach ($value as $item) {
                    $desc = $item->item_description ?? $item->description ?? $item->desc ?? '';
                    $total_price = $item->total_price ?? $item->price ?? 0;
                    $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                    if (Str::contains(strtolower($desc), ['charge','pallect and packing'])) {
                        $totalCharges += $total_price;
                    }
                }
            } elseif (is_array($value)) {
                foreach ($value as $item) {
                    $desc = $item['item_description'] ?? $item['description'] ?? $item['desc'] ?? '';
                    $total_price = $item['total_price'] ?? $item['price'] ?? 0;
                    $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                    if (Str::contains(strtolower($desc), ['charge','pallect and packing'])) {
                        $totalCharges += $total_price;
                    }
                }
            } elseif (is_object($value)) {
                $desc = $value->item_description ?? $value->description ?? $value->desc ?? '';
                $total_price = $value->total_price ?? $value->price ?? 0;
                $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                if (Str::contains(strtolower($desc), ['charge','pallect and packing'])) {
                    $totalCharges += $total_price;
                }
            }
            else{
            }
        }
        
        return [
            'allItems' => $allItems,
            'totalCharges' => $totalCharges,
        ];
    }

    private function extractBoosterBOMChargesAndCosts($bom)
    {
        $totalCharges = 0;
        $allItems = [];

        foreach ($bom as $key => $value) {
            if ($value instanceof \Illuminate\Support\Collection) {
                foreach ($value as $item) {
                    $desc = $item->item_description ?? $item->description ?? $item->desc ?? '';
                    $total_price = $item->total_price ?? $item->price ?? 0;
                    $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                    if (Str::contains(strtolower($desc), ['charge', 'cost','pallect and packing'])) {
                        $totalCharges += $total_price;
                    }
                }
            } elseif (is_array($value)) {
                foreach ($value as $item) {
                    $desc = $item['item_description'] ?? $item['description'] ?? $item['desc'] ?? '';
                    $total_price = $item['total_price'] ?? $item['price'] ?? 0;
                    $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                    if (Str::contains(strtolower($desc), ['charge', 'cost','pallect and packing'])) {
                        $totalCharges += $total_price;
                    }
                }
            } elseif (is_object($value)) {
                $desc = $value->item_description ?? $value->description ?? $value->desc ?? '';
                $total_price = $value->total_price ?? $value->price ?? 0;
                $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                if (Str::contains(strtolower($desc), ['charge', 'cost','pallect and packing'])) {
                    $totalCharges += $total_price;
                }
            }
        }
        
        return [
            'allItems' => $allItems,
            'totalCharges' => $totalCharges,
        ];
    }

    private function extractAtmosBOMChargesAndCosts($bom)
    {
        $totalCharges = 0;
        $allItems = [];

        foreach ($bom as $key => $value) {
            if ($value instanceof \Illuminate\Support\Collection) {
                foreach ($value as $item) {
                    $desc = $item->item_description ?? $item->description ?? $item->desc ?? '';
                    $total_price = $item->total_price ?? $item->price ?? 0;
                    $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                    if (Str::contains(strtolower($desc), ['charge', 'cost'])) {
                        $totalCharges += $total_price;
                    }
                }
            } elseif (is_array($value)) {
                foreach ($value as $item) {
                    $desc = $item['item_description'] ?? $item['description'] ?? $item['desc'] ?? '';
                    $total_price = $item['total_price'] ?? $item['price'] ?? 0;
                    $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                    if (Str::contains(strtolower($desc), ['charge', 'cost'])) {
                        $totalCharges += $total_price;
                    }
                }
            } elseif (is_object($value)) {
                $desc = $value->item_description ?? $value->description ?? $value->desc ?? '';
                $total_price = $value->total_price ?? $value->price ?? 0;
                $allItems[] = ['desc' => $desc, 'total_price' => $total_price];
                if (Str::contains(strtolower($desc), ['charge', 'cost'])) {
                    $totalCharges += $total_price;
                }
            }
        }
        
        return [
            'allItems' => $allItems,
            'totalCharges' => $totalCharges,
        ];
    }

    public static function atmos_short_code($matirial_id)
    {
        $short_code = DB::table('atmos_materials')->where('id', '=', 'matirial_id')->pluck("short_code")->first();
        if ($short_code) {
            $short_code = $short_code;
        } else {
            $short_code = '-';
        }
        return $short_code;
    }

    public static function atmos_pump_article_number($pump_id, $material_id)
    {
        $article = AtmosPump::select('bare_pump_article_no')
            ->where('pump_id', '=', $pump_id)
            ->where('material_id', '=', $material_id)
            ->first();
        if ($article) {
            $article = $article->bare_pump_article_no;
        } else {
            $article = '-';
        }
        return $article;
    }

    public static function scp_pump_article_number($pump_name)
    {
        $article = ScpPumpType::select('bare_shaft_article_number')
            ->where('name', '=', $pump_name)
            ->first();
        if ($article) {
            $article = $article->bare_shaft_article_number;
        } else {
            $article = '-';
        }
        return $article;
    }

    // A Code: 23-02-2026 Start
    public static function scpv_pump_article_number($pump_name)
    {
        $article = ScpvPumpType::select('bare_shaft_article_number')
            ->where('name', '=', $pump_name)
            ->first();
        if ($article) {
            $article = $article->bare_shaft_article_number;
        } else {
            $article = '-';
        }
        return $article;
    }
    // A Code: 23-02-2026 End

    public static function cp_table_name($cp_id)
    {
        $table_name = ControlPanel::select('table_name')->where('id', '=', $cp_id)->first();
        if ($table_name) {
            if (str_starts_with($table_name->table_name, "basic_") == true) {
                $table_name = "COE";
            } else {
                $table_name = "CO";
            }
        } else {
            $table_name = "-";
        }
        return $table_name;
    }

    public static function cp_no_of_pump($cp_id)
    {
        $cp_no_of_pump = ControlPanel::select('no_of_pump_id')->where('id', '=', $cp_id)->first();
        if ($cp_no_of_pump) {
            $no_of_pump = $cp_no_of_pump->no_of_pump_id;
        } else {
            $no_of_pump = '-';
        }
        return $no_of_pump;
    }

    public static function cp_enclosure($cp_id)
    {
        $enclosure = ControlPanel::select('enclosure_id')->where('id', '=', $cp_id)->first();
        if ($enclosure) {
            if ($enclosure->enclosure_id == '1') {
                $enclosure = "PVC";
            } elseif ($enclosure->enclosure_id == '2') {
                $enclosure = "Metal";
            } elseif ($enclosure->enclosure_id == '3') {
                $enclosure = "GRP";
            } elseif ($enclosure->enclosure_id == '4') {
                $enclosure = "Stainless steel";
            } else {
                $enclosure = "-";
            }
        } else {
            $enclosure = "-";
        }
        return $enclosure;
    }

    public static function cp_component($cp_id)
    {
        $component = ControlPanel::select('components_id')->where('id', '=', $cp_id)->first();
        if ($component) {
            if ($component->components_id == "1") {
                $component = "Standard";
            } elseif ($component->components_id == "2") {
                $component = "Economic";
            } else {
                $component = "-";
            }
        } else {
            $component = "-";
        }
        return $component;
    }

    public static function cp_ip_rating($cp_id)
    {
        $ip_rating = ControlPanel::select('ip_rating_id')->where('id', '=', $cp_id)->first();
        if ($ip_rating) {
            if ($ip_rating->ip_rating_id == "1") {
                $ip_rating = "IP54";
            } elseif ($ip_rating->ip_rating_id == "2") {
                $ip_rating = "IP55";
            } elseif ($ip_rating->ip_rating_id == "3") {
                $ip_rating = "IP56";
            } else {
                $ip_rating = "-";
            }
        } else {
            $ip_rating = "-";
        }
        return $ip_rating;
    }

    public static function cp_communication_protocol($cp_id)
    {
        $communication_protocol = ControlPanel::select('communication_protocol_id')->where('id', '=', $cp_id)->first(); {
            if ($communication_protocol) {
                if ($communication_protocol->communication_protocol_id == "1") {
                    $communication_protocol = "VFC";
                } elseif ($communication_protocol->communication_protocol_id == "2") {
                    $communication_protocol = "Modbus RTU";
                } elseif ($communication_protocol->communication_protocol_id == "3") {
                    $communication_protocol = "Modbus TCP";
                } elseif ($communication_protocol->communication_protocol_id == "4") {
                    $communication_protocol = "Bacnet";
                } else {
                    $communication_protocol = "else1";
                }
            } else {
                $communication_protocol = "ele1";
            }
            return $communication_protocol;
        }
    }

    public static function cp_stater_type_id($cp_id)
    {
        $cp_starter_type_id = ControlPanel::select('stater_type_id')->where('id', '=', $cp_id)->first();
        if ($cp_starter_type_id) {
            if ($cp_starter_type_id->stater_type_id == "1") {
                $cp_starter_type_id = "Xtreme";
            } elseif ($cp_starter_type_id->stater_type_id == "2") {
                $cp_starter_type_id = "Constant speed- DOL";
            } elseif ($cp_starter_type_id->stater_type_id == "3") {
                $cp_starter_type_id = "Multi VFD";
            } elseif ($cp_starter_type_id->stater_type_id == "4") {
                $cp_starter_type_id = "Multi VFD + Bypass";
            } elseif ($cp_starter_type_id->stater_type_id == "5") {
                $cp_starter_type_id = "Softstarter";
            } elseif ($cp_starter_type_id->stater_type_id == "6") {
                $cp_starter_type_id = "Constant speed- SD";
            } elseif ($cp_starter_type_id->stater_type_id == "7") {
                $cp_starter_type_id = "Single VFD";
            } else {
                $cp_starter_type_id = "-";
            }
        } else {
            $cp_starter_type_id = "-";
        }
        return $cp_starter_type_id;
    }

    public static function cp_ambient_temps($cp_id)
    {
        $cp_ambient_temps = ControlPanel::select('ambient_temp_id')->where('id', '=', $cp_id)->first();
        if ($cp_ambient_temps) {
            if ($cp_ambient_temps->ambient_temp_id == "1") {
                $cp_ambient_temps = "40";
            } elseif ($cp_ambient_temps->ambient_temp_id == "2") {
                $cp_ambient_temps = "50";
            } else {
                $cp_ambient_temps = "-";
            }
        } else {
            $cp_ambient_temps = "-";
        }
        return $cp_ambient_temps;
    }

    public static function fireFightingPumpData($firefightingCart)
    {
        $electricalchange = [
            'id' => 'id',
            'electrical_pumpmodels' => 'wilo_pump_models',
            'electrical_pumptype' => 'pump_type',
            'electrical_frequency' => 'frequency',
            'electrical_pump_approval' => 'pump_approval',
            'electrical_flow' => 'flow',
            'electrical_head' => 'head',
            'electrical_speed' => 'speed_rpm'
        ];
        $dieselchange = [
            'id' => 'id',
            'diesel_pumpmodels' => 'pump_models',
            'diesel_pumptype' => 'pump_type',
            'diesel_frequency' => 'frequency',
            'diesel_pump_approval' => 'pump_approval',
            'diesel_engine_approval' => 'engine_approval',
            'diesel_flow' => 'flow',
            'diesel_head' => 'head',
            'diesel_speed' => 'speed_rpm'
        ];
        switch ($firefightingCart->category) {
            case 'electrical':
                $data = ElectricalPump::select('*');
                $id = null;
                foreach ($firefightingCart->field_val as $key => $value) {
                    if (array_key_exists(array_key_first($value), $electricalchange) && array_key_exists(array_key_first($value), $value)) {
                        if ($electricalchange[array_key_first($value)] != 'id') {
                            $data = $data->where($electricalchange[array_key_first($value)], $value[array_key_first($value)]);
                        } else {
                            $id = $value[array_key_first($value)];
                        }
                    }
                }
                $data = $data->get();

                if (count($data) > 0) {
                    if (count($data) > 1) {
                        $data = $data->where('id', $id)->first();
                    } else {
                        $data = $data->first();
                    }
                } else {
                    $data = null;
                }

                $datapass['system_pressure'] = '-';
                $datapass['impeller_material'] = $data->moc_impeller ?? '-';
                $datapass['no_of_pumps'] = '1';
                $datapass['motor_power'] = $data->motor_power ?? '-';
                $datapass['voltage'] = $data->voltage ?? '-';
                $datapass['frequency'] = $data->frequency ?? '-';
                $datapass['no_of_poles'] = $data->speed_rpm >= 2900 ? 2 : 4;
                $datapass['efficicency'] = '';
                $datapass['motor_brand'] = $data->motor_brand ?? '-';
                $datapass['pump_article_number'] = $data->electrical_pump_ordering_code ?? '-';
                // dd($datapass);
                return $datapass;
                break;

            case 'diesel':
                $data = DieselPump::select('*');
                $id = null;
                foreach ($firefightingCart->field_val as $key => $value) {
                    if ($dieselchange[array_key_first($value)] != 'id') {
                        $data = $data->where($dieselchange[array_key_first($value)], $value[array_key_first($value)]);
                    } else {
                        $id = $value[array_key_first($value)];
                    }
                }
                $data = $data->get();

                if (count($data) > 0) {
                    if (count($data) > 1) {
                        $data = $data->where('id', $id)->first();
                    } else {
                        $data = $data->first();
                    }
                } else {
                    $data = null;
                }

                $datapass['system_pressure'] = '-';
                $datapass['impeller_material'] = $data->moc_impeller ?? '-';
                $datapass['no_of_pumps'] = '1';
                $datapass['motor_power'] = $data->engine_power ?? '-';
                $datapass['voltage'] = $data->voltage ?? '-';
                $datapass['frequency'] = $data->frequency ?? '-';
                // $datapass['no_of_poles'] = $data->speed_rpm >= 2900 ? 2 : 4;
                $datapass['no_of_poles'] = ($data && $data->speed_rpm >= 2900) ? 2 : 4;
                $datapass['efficicency'] = '';
                $datapass['motor_brand'] = $data->engine_brand ?? '-';
                $datapass['pump_article_number'] = $data->diesel_pump_code ?? '-';

                return $datapass;
                break;

            case 'jockey-pump':
                $data = JockeyPump::where('pump_article_no', $firefightingCart->jockey_article_number)->where('power', $firefightingCart->power)->where('frequency', $firefightingCart->frequency)->first();

                $datapass['system_pressure'] = '-';
                $datapass['impeller_material'] = $data->moc_impeller ?? '-';
                $datapass['no_of_pumps'] = '1';
                $datapass['motor_power'] = $data->power ?? '-';
                $datapass['voltage'] = $data->voltage ?? '-';
                $datapass['frequency'] = $data->frequency ?? '-';
                $datapass['no_of_poles'] = '-';
                $datapass['efficicency'] = '';
                $datapass['motor_brand'] = $data->motor_brand ?? '-';
                $datapass['pump_article_number'] = '-';

                return $datapass;
                break;
        }
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
