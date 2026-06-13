<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Country;
use App\ICMargin;

class CountryController extends Controller
{
  public function show_country()
  {
      $country = Country::select('*')->get();
      return view('admin.country.show_country',['country'=>$country]);
  }

  public function add_country(Request $request)
  {
   
      // Validate the country name for uniqueness
      $request->validate([
          'country_name' => 'required|unique:countries,country'
      ]);
     
    
      $country = new Country;
      $country->country = $request->country_name;
      $country->save();

      $booster_margin = new ICMargin;
      $booster_margin->country = $request->country_name;
      $booster_margin->value = 0.9;
      $booster_margin->part_id = 1;
      $booster_margin->country_id = $country->id;
      $booster_margin->save();
    
      $cp_margin = new ICMargin;
      $cp_margin->country = $request->country_name;
      $cp_margin->value = 0.9;
      $cp_margin->part_id = 2;
      $cp_margin->country_id = $country->id;
      $cp_margin->save();

      $scp_margin = new ICMargin;
      $scp_margin->country = $request->country_name;
      $scp_margin->value = 0.9;
      $scp_margin->part_id = 3;
      $scp_margin->country_id = $country->id;
      $scp_margin->save();

      // A Code: 02-03-2026 Start
      $scpv_margin = new ICMargin;
      $scpv_margin->country = $request->country_name;
      $scpv_margin->value = 0.9;
      $scpv_margin->part_id = 6;
      $scpv_margin->country_id = $country->id;
      $scpv_margin->save();
      // A Code: 02-03-2026 End

      $atmos_margin = new ICMargin;
      $atmos_margin->country = $request->country_name;
      $atmos_margin->value = 0.9;
      $atmos_margin->part_id = 4;
      $atmos_margin->country_id = $country->id;
      $atmos_margin->save();
	  
	  $fire_fighting_margin = new ICMargin;
      $fire_fighting_margin->country = $request->country_name;
      $fire_fighting_margin->value = 0.9;
      $fire_fighting_margin->part_id = 5;
      $fire_fighting_margin->country_id = $country->id;
      $fire_fighting_margin->save();

      return redirect()->back()->with('success', 'Country added successfully..!!');   
  }

  public function edit_country(Request $request)
  {
      $country = Country::find($request->country_id);      
      if ($country) {
          // Validate the country name for uniqueness, excluding the current country
          $request->validate([
              'country_name' => 'required|unique:countries,country,' . $country->id
          ]);
          
          // Update the country name
          $country->country = $request->country_name;
          $country->save();
          return redirect()->back()->with('success', 'Country updated successfully!');
      }
      return redirect()->back()->with('error', 'Country not found. Please try again.');
  }
  
  public function delete_country(Request $request)
  {
      try {
          $country = Country::findOrFail($request->cntry_id);
          $country->delete();
          return redirect()->back()->with('success', 'Country deleted successfully!');
      } catch (\Exception $e) {
          return redirect()->back()->with('error', 'Failed to delete country. Please try again.');
      }
  }
}
