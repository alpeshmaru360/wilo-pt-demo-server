<?php

Route::get('admin/login', 'Admin\AuthController@loginForm')->name('loginform');
Route::post('admin/login', 'Admin\AuthController@login')->name('adminlogin');

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('/dashboard', 'HomeController@index')->name('home');

    Route::resource('bom_summary', 'BOMSummaryController');
    Route::post('bom_summary/filter', 'BOMSummaryController@index')->name('BOMSummaryFilter');
    Route::post('bom_summary/export-csv', 'BOMSummaryController@exportCSV')->name('BOMSummaryExportCsv');
  
    // For add and show the country
	Route::get('country/show', 'CountryController@show_country');
    Route::post('country/add_country', 'CountryController@add_country');

    Route::post('country/edit_country', 'CountryController@edit_country');
    Route::delete('country/delete_country', 'CountryController@delete_country');

    // A Code: 11-04-2026 Start
    Route::get('warehouse-pump-details-import', 'CPBasicController@import_warehouse_pump');
    Route::post('warehouse-pump-details-upload', 'CPBasicController@upload_warehouse_pump')->name('warehouse_pump.upload');
    // A Code: 11-04-2026 End

    Route::get('change-password', 'ChangePasswordController@index');
    Route::post('change-password', 'ChangePasswordController@store')->name('change.password');
    
	//route for dashboard Quotation Excel download.
	Route::get('dashboard/export_quotation','HomeController@export_quotation');
	Route::post('dashboard/new-export-quotation','HomeController@newExportQuotation');
    Route::get('dashboard/new-export-quotation-test/{actiton_id}','HomeController@newExportQuotationTest');
	Route::get('all-quotation-list','HomeController@allQuotationList');
    Route::get('manage_tooltips', 'HomeController@tool_tip_page')->name('manage_tooltips');
    Route::get('document', 'DocumentController@index')->name('document');
    Route::get('get_artical_by_module', 'DocumentController@getArticleByModule')->name('get_artical_by_module');
    Route::get('get_artical_detail', 'DocumentController@getArticleDetail')->name('get_artical_detail');
    Route::post('document/upload', 'DocumentController@upload')->name('document_upload');
    Route::get('document/delete/{article}', 'DocumentController@deleteArticle')->name('delete_article');
    Route::get('manual', 'DocumentController@manual')->name('manual');
    Route::get('get_manual_by_module', 'DocumentController@getManualByModule')->name('get_manual_by_module');
    Route::post('manual/upload', 'DocumentController@manualUpload')->name('manual_upload');
    Route::get('manuals/delete/{file}', 'DocumentController@deleteManual')->name('delete_manual');
    Route::get('booster_set', 'HomeController@booster_set')->name('booster_set');
    Route::get('atmos_giga', 'HomeController@atmos_giga')->name('atmos_giga');
    Route::get('control_panel', 'HomeController@control_panel')->name('control_panel');
    Route::get('scp_pumps', 'HomeController@scp_pumps')->name('scp_pumps');

    // A Code: 06-11-2025 Start
    Route::get('scpv_pumps', 'HomeController@scpv_pumps')->name('scpv_pumps');
    Route::post('scpv_t_tip', 'HomeController@scpv_t_tip')->name('scpv_t_tip');
    // A Code: 06-11-2025 End

    Route::post('save_control_panel_tool_tip', 'HomeController@save_control_panel_tool_tip')->name('save_control_panel_tool_tip');
    Route::post('scp_t_tip', 'HomeController@scp_t_tip')->name('scp_t_tip');
    Route::post('giga', 'HomeController@giga')->name('giga');
    Route::get('setup', 'HomeController@setup')->name('setup');
    Route::post('setup_post', 'HomeController@setup_post')->name('setup_post');
    Route::get('ic_margin', 'HomeController@ic_margin')->name('ic_margin');
    Route::post('ic_margin_post', 'HomeController@ic_margin_post')->name('ic_margin_post');

    Route::get('otp_margin', 'HomeController@otp_margin')->name('otp_margin');
    Route::post('otp_margin_post', 'HomeController@otp_margin_post')->name('otp_margin_post');

    Route::get('maintance_mode', 'HomeController@maintance_mode')->name('maintance_mode');
    Route::post('maintance_mode_post', 'HomeController@maintance_mode_post')->name('maintance_mode_post');

    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::post('booster_tool_tip', 'HomeController@save_booster_tool_tip')->name('booster_tool_tip');
    Route::resource('permissions', 'PermissionsController');

    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');

    Route::resource('roles', 'RolesController');

    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');

    Route::resource('users', 'UsersController');

    Route::delete('products/destroy', 'ProductsController@massDestroy')->name('products.massDestroy');

    Route::resource('products', 'ProductsController');

    Route::get('file-import', 'FileImportController@import');
    Route::get('ajax-fileName', 'FileImportController@ajaxFileName');
    Route::get('ajax-rangeName', 'FileImportController@ajaxRangeName'); // A Code: 26-06-2026
    Route::post('upload', 'FileImportController@upload')->name('upload');

    // Profile
    Route::get('profile', 'AccountController@show')->name('profile');
    Route::post('profile/update/{user}', 'AccountController@update')->name('profile.update');

    //Master Price Sheet Import
    Route::get('master-price-file-import', 'MasterPriceFileImportController@import');
    Route::post('upload/master', 'MasterPriceFileImportController@upload')->name('master.price.upload');
    
    //Control Panel main file import
    Route::get('cp/control-panel-import', 'CPBasicController@import');
    Route::post('cp/upload/main-file-upload', 'CPBasicController@upload')->name('cp.main.upload');

    // A Code: 05-03-2026 Start
    Route::get('cp/short-control-panel-import', 'CPBasicController@import_short');
    Route::post('cp/upload/short-file-upload', 'CPBasicController@upload_short')->name('cp.short.upload');
    // A Code: 05-03-2026 End

    //AdderOptionalImportControllerImport
    Route::get('adder-optional-file-import', 'AdderOptionalImportController@import');
    Route::post('upload/adder-optional-upload', 'AdderOptionalImportController@upload')->name('adder.optional.upload');
    Route::get('/create-table-csv-upload', 'CreateTableCsvFileController@index');

    Route::get('adder-optional-list-file-import', 'AdderOptionalListImportController@import');
    Route::post('upload/adder-optional-list-upload', 'AdderOptionalListImportController@upload')->name('adder.optional.upload');

    /* Start Atmos GIGA Import */
    // new 20-5-2024 pump_assmebly_cost
    //1
    Route::get('atmos-giga/pump-assmebly_cost-import', 'AtmosGigaFileImportController@pumpAssmeblyCostImport')->name('atmos.pump_assmebly_cost.import.view');
    Route::post('atmos-giga/pump-assmebly_cost/upload', 'AtmosGigaFileImportController@pumpAssmeblyCostImportUpload')->name('atmos.pump_assmebly_cost.import.upload');
    //2
    Route::get('atmos-giga/pump-bom-import', 'AtmosGigaFileImportController@pumpBOMImport')->name('atmos.pump_bom.import.view');
    Route::post('atmos-giga/pump-bom/upload', 'AtmosGigaFileImportController@pumpBOMImportUpload')->name('atmos.pump_bom.import.upload');
    //3 Atmos pump - Master sheet
    Route::get('atmos-giga/pump-master-sheet-import', 'AtmosGigaFileImportController@pumpMasterPumpPriceImport')->name('atmos.pump_master_sheet.import.view');
    Route::post('atmos-giga/pump-master-sheet/upload', 'AtmosGigaFileImportController@pumpMasterPumpPriceImportUpload')->name('atmos.pump_master_sheet.import.upload');

    Route::get('atmos-giga/pump-type-import', 'AtmosGigaFileImportController@pumpTypeImport')->name('atmos.pumptype.import.view');
    Route::post('atmos-giga/pump-type/upload', 'AtmosGigaFileImportController@pumpTypeImportUpload')->name('atmos.pumptype.import.upload');

    Route::get('atmos-giga/accessories-import', 'AtmosGigaFileImportController@importAccessories')->name('atmos.accessories.import.view');
    Route::post('atmos-giga/accessories/upload', 'AtmosGigaFileImportController@importAccessoriesUpload')->name('atmos.accessories.import.upload');

    Route::get('atmos-giga/master-import', 'AtmosGigaFileImportController@masterPriceImport')->name('atmos.master.import.view');
    Route::post('atmos-giga/master/upload', 'AtmosGigaFileImportController@masterPriceImportUpload')->name('atmos.master.import.upload');

    Route::get('atmos-giga/cost-paint-pack-import', 'AtmosGigaFileImportController@costPaintPackImport')->name('atmos.costpaint.import.view');
    Route::post('atmos-giga/cost-paint-pack-import/upload', 'AtmosGigaFileImportController@costPaintPackImportUpload')->name('atmos.master.costpaint.upload');

    Route::get('atmos-giga/adder-import', 'AtmosGigaFileImportController@adderImport')->name('atmos.adder.import.view');
    Route::post('atmos-giga/adder-import/upload', 'AtmosGigaFileImportController@adderImportUpload')->name('atmos.master.adder.upload');

    /* End Atmos GIGA Import */

    /* Start SCP Import */
    Route::get('scp/pump-type-import', 'ScpFileImportController@pumpTypeImport')->name('scp.pumptype.import.view');
    Route::post('scp/pump-type/upload', 'ScpFileImportController@pumpTypeImportUpload')->name('scp.pumptype.import.upload');

    Route::get('scp/accessories-import', 'ScpFileImportController@importAccessories')->name('scp.accessories.import.view');
    Route::post('scp/accessories/upload', 'ScpFileImportController@importAccessoriesUpload')->name('scp.accessories.import.upload');

    Route::get('scp/master-import', 'ScpFileImportController@masterPriceImport')->name('scp.master.import.view');
    Route::post('scp/master/upload', 'ScpFileImportController@masterPriceImportUpload')->name('scp.master.import.upload');

    Route::get('scp/cost-paint-pack-import', 'ScpFileImportController@costPaintPackImport')->name('scp.costpaint.import.view');
    Route::post('scp/cost-paint-pack-import/upload', 'ScpFileImportController@costPaintPackImportUpload')->name('scp.master.costpaint.upload');

    Route::get('scp/adder-import', 'ScpFileImportController@adderImport')->name('scp.adder.import.view');
    Route::post('scp/adder-import/upload', 'ScpFileImportController@adderImportUpload')->name('scp.master.adder.upload');
    /* End SCP Import */

    Route::get('booster/pump-price/full-pump-price-import', 'BoosterPumpPriceFileImportController@importFullPumpPrice');
    Route::post('booster/pump-price/full-pump-price-upload', 'BoosterPumpPriceFileImportController@uploadFullPumpPrice')->name('booster.full_pump_price.upload');

    /* Start SCPV Import */
    Route::get('scpv/pump-type-import', 'ScpvFileImportController@pumpTypeImport')->name('scpv.pumptype.import.view');
    Route::post('scpv/pump-type/upload', 'ScpvFileImportController@pumpTypeImportUpload')->name('scpv.pumptype.import.upload');

    Route::get('scpv/accessories-import', 'ScpvFileImportController@importAccessories')->name('scpv.accessories.import.view');
    Route::post('scpv/accessories/upload', 'ScpvFileImportController@importAccessoriesUpload')->name('scpv.accessories.import.upload');

    Route::get('scpv/master-import', 'ScpvFileImportController@masterPriceImport')->name('scpv.master.import.view');
    Route::post('scpv/master/upload', 'ScpvFileImportController@masterPriceImportUpload')->name('scpv.master.import.upload');

    Route::get('scpv/cost-paint-pack-import', 'ScpvFileImportController@costPaintPackImport')->name('scpv.costpaint.import.view');
    Route::post('scpv/cost-paint-pack-import/upload', 'ScpvFileImportController@costPaintPackImportUpload')->name('scpv.master.costpaint.upload');
    /* End SCPV Import */

    //Booster 

    Route::get('/booster/adder-optional-file-import', 'MechanicalAdderOptionalImportController@import');

    Route::post('/booster/upload/adder-optional-upload', 'MechanicalAdderOptionalImportController@upload')->name('mechanical.adder.optional.upload');

    Route::get('/booster/adder-optional-list-file-import', 'MechanicalAdderOptionalListImportController@import');

    Route::post('/booster/upload/adder-optional-list-upload', 'MechanicalAdderOptionalListImportController@upload')->name('mechanical.adder.optional.upload');

    Route::get('/booster/bom-file-import', 'BoosterBomFileController@import');

    Route::post('/booster/upload/bom-file-upload', 'BoosterBomFileController@upload')->name('booster.bom.upload');

    Route::get('booster/pump-price/bareshaft-pump-motor-price-import', 'BoosterPumpPriceFileImportController@importBareshaftPumpMotorPrice');
    Route::post('booster/pump-price/bareshaft-pump-motor-price-upload', 'BoosterPumpPriceFileImportController@uploadBareshaftPumpMotorPrice')->name('booster.bareshaft_pump_motor_price.upload');

    Route::get('booster/mechanical-component/master-sheet-price-import', 'BoosterMechanicalFileImportController@importMasterPriceSheet');
    
    Route::post('booster/mechanical-component/master-sheet-price-upload', 'BoosterMechanicalFileImportController@uploadMasterPriceSheet')->name('booster.mechanical_master_sheet_price.upload');

    //bom-pn16-import
    Route::get('booster/mechanical-component/bom-pn16-import', 'BoosterMechanicalFileImportController@importBOMPN16');
    Route::post('booster/mechanical-component/bom-pn16-upload', 'BoosterMechanicalFileImportController@uploadBOMPN16')->name('booster.mechanical_BOM_PN16.upload');
    //ptp distance
    Route::get('booster/mechanical-component/ptp-distance-import', 'BoosterMechanicalFileImportController@importPtpDistance');
    Route::post('booster/mechanical-component/ptp-distance-upload', 'BoosterMechanicalFileImportController@uploadPtpDistance')->name('booster.mechanical_ptpDistance.upload');
    //cable sleection
    Route::get('booster/mechanical-component/cable-selection-import', 'BoosterMechanicalFileImportController@importCableSelection');
    Route::post('booster/mechanical-component/cable-selection-upload', 'BoosterMechanicalFileImportController@uploadCableSelection')->name('booster.mechanical_cable_selection.upload');
    //base frame calculation
    Route::get('booster/mechanical-component/base-frame-calculation-import', 'BoosterMechanicalFileImportController@importBaseFrameCalculation');
    Route::post('booster/mechanical-component/base-frame-calculation-upload', 'BoosterMechanicalFileImportController@uploadBaseFrameCalculation')->name('booster.mechanical_base_frame_calculation.upload');
	
	Route::group(['prefix' => 'fire-fighting-documents', 'as' => 'fire-fighting-documents.', 'namespace' => 'FireFighting', 'middleware' => ['auth', 'admin']], function () {
        Route::resource('/', 'FireFightingDocumentController');
    });

    // Fire Fighting Pump
    Route::group(['prefix' => 'fire-fighting', 'as' => 'fire-fighting.', 'namespace' => 'FireFighting', 'middleware' => ['auth', 'admin']], function () {
        Route::get('diesel-pump-import', 'FireFightingPumpController@dieselPumpImport')->name('diesel-pump-import');
        Route::post('diesel-pump-import', 'FireFightingPumpController@dieselPumpImportStore')->name('diesel-pump-import.store');

        Route::get('electrical-pump-import', 'FireFightingPumpController@electricalPumpImport')->name('electrical-pump-import');
        Route::post('electrical-pump-import', 'FireFightingPumpController@electricalPumpImportStore')->name('electrical-pump-import.store');

        Route::get('jockey-pump-import', 'FireFightingPumpController@jockeyPumpImport')->name('jockey-pump-import');
        Route::post('jockey-pump-import', 'FireFightingPumpController@jockeyPumpImportStore')->name('jockey-pump-import.store');

        Route::get('battery-master-import', 'FireFightingPumpController@batteryMasterImport')->name('battery-master-import');
        Route::post('battery-master-import', 'FireFightingPumpController@batteryMasterImportStore')->name('battery-master-import.store');

        Route::get('diesel-tank-master-import', 'FireFightingPumpController@dieselTankMasterImport')->name('diesel-tank-master-import');
        Route::post('diesel-tank-master-import', 'FireFightingPumpController@dieselTankMasterImportStore')->name('diesel-tank-master-import.store');

        Route::get('optional-master-import', 'FireFightingPumpController@optionalMasterImport')->name('optional-master-import');
        Route::post('optional-master-import', 'FireFightingPumpController@optionalMasterImportStore')->name('optional-master-import.store');

        Route::get('adders', 'FireFightingPumpController@addersImport')->name('adders');
        Route::post('adders', 'FireFightingPumpController@addersImportStore')->name('adders.store');

        Route::get('control-panel-master-import', 'FireFightingPumpController@controlePanelMasterImport')->name('control-panel-master-import');
        Route::post('control-panel-master-import', 'FireFightingPumpController@controlePanelMasterImportStore')->name('control-panel-master-import.store');

        Route::get('motor-master-import', 'FireFightingPumpController@motorMasterImport')->name('motor-master-import');
        Route::post('motor-master-import', 'FireFightingPumpController@motorMasterImportStore')->name('motor-master-import.store');

        Route::get('flow-meter-import', 'FireFightingPumpController@flowMeterMasterImport')->name('flow-meter-import');
        Route::post('flow-meter-import', 'FireFightingPumpController@flowMeterMasterImportStore')->name('flow-meter-import.store');

        Route::get('pressure-relief-valve', 'FireFightingPumpController@pressureReliefValveMasterImport')->name('pressure-relief-valve');
        Route::post('pressure-relief-valve', 'FireFightingPumpController@pressureReliefValveMasterImportStore')->name('pressure-relief-valve.store');

        Route::get('waste-cone-import', 'FireFightingPumpController@wasteConeMasterImport')->name('waste-cone-import');
        Route::post('waste-cone-import', 'FireFightingPumpController@wasteConeMasterImportStore')->name('waste-cone-import.store');    
    });
});
