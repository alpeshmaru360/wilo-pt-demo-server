<?php

Auth::routes(['register' => false]);

Route::group(['namespace' => 'Frontend'], function () {
    Route::get('/getCpbasic', 'CPBasicController@controlpanel_Basic')->name('cp.basicfile');
});
Route::get('/', 'Frontend\HomeController@index');

Route::get('cache_clear', function () {
    Artisan::call('optimize:clear');
    return '<h2>cache clear</h2>';
});
// Fire Fighting Pump
Route::group(['namespace' => 'Frontend\FireFighting', 'middleware' => ['auth', 'user']], function () {
    Route::get('firefighting-set/cart-item/{id}/{id1?}', 'FireFightingPumpController@cartItems');
    Route::resource('fire-fighting', 'FireFightingPumpController');
    Route::resource('fire-fighting-documents', 'FireFightingDocumentController');    
});

Route::group(['namespace' => 'Frontend', 'middleware' => ['auth', 'user']], function () {
    Route::get('country_name', 'QuotationController@country_name');
    Route::get('/controlpanel/manuals', 'DashboardController@getManuals');
    Route::get('/controlpanel/documents', 'DashboardController@getDocuments');

    Route::get('/home', 'DashboardController@index')->name('main.home');

    Route::get('/controlpanel', 'ControlpanelController@index')->name('cp.controlpanel');
    Route::get('/atmos_giga', 'AtmosGigaController@index')->name('ag.atmos_giga');
    Route::get('/maintance_mode', 'HomeController@maintance_mode')->name('is_maintance_mode');
    Route::post('/atmos_giga_price', 'AtmosGigaController@get_price')->name('ag.price');
    Route::post('/get_frame', 'AtmosGigaController@get_frame')->name('ag.get_frame');
    Route::post('/get_accessories', 'AtmosGigaController@get_accessories')->name('ag.get_accessories');
    Route::post('/get_motor_price', 'AtmosGigaController@get_motor_price')->name('ag.get_motor_price');
    Route::post('/atmos/ajax-optional-modal', 'AtmosGigaController@ajaxOptionalModal');
    Route::post('/atmos/ajax-optional-selected-adder', 'AtmosGigaController@ajaxOptionalSelectedAdderData');
    Route::get('/atmos/ajaxCalculate', 'AtmosGigaController@ajaxCalculate');
    Route::get('/atmos/ajaxCalculateBareshaft', 'AtmosGigaController@ajaxCalculateBareshaft');
    Route::post('/atmos/ajax-optional-selected-adder', 'AtmosGigaController@ajaxOptionalSelectedAdderData');
    Route::post('atmos/addtocart', 'AtmosGigaController@addToCart')->name('atmos.addtocart');
    Route::post('atmos/addToCartBareshaft', 'AtmosGigaController@addToCartBareshaft')->name('atmos.addToCartBareshaft');
    Route::get('atmos/remove-cart/{id}', 'AtmosGigaController@removeCart')->name('atmos.removecart');
    Route::get('/atmos/ajax-qty-update', 'AtmosGigaController@ajaxQtyUpdate');
    Route::get('/atmos/cart-item/{id}', 'AtmosGigaController@cartItems');
    Route::get('/atmos/search-article-number', 'AtmosGigaController@searchByArticleNumber')->name('atmos.searchbyarticle');
    Route::get('atmos/item/delete', 'QuotationController@deleteAtmosItemFromEditQuotation')->name('deleteAtmosItemFromEditQuotation');
    // Route::get('/atmos/search_addtocart', 'AtmosGigaController@addToCartForSearch')->name('atmos.addToCartForSearch');
    //Scp
    Route::get('/scp_pump', 'ScpController@index')->name('scp.pump');
    Route::post('/scp_price', 'ScpController@get_price')->name('scp.price');
    Route::post('/get_scp_frame', 'ScpController@get_frame')->name('scp.get_frame');
    Route::post('/get_scp_accessories', 'ScpController@get_accessories')->name('scp.get_accessories');
    Route::post('/get_scp_motor_price', 'ScpController@get_motor_price')->name('scp.get_motor_price');

    Route::post('/scp/ajax-optional-modal', 'ScpController@ajaxOptionalModal');
    Route::post('/scp/ajax-optional-selected-adder', 'ScpController@ajaxOptionalSelectedAdderData');
    Route::get('/scp/ajaxCalculate', 'ScpController@ajaxCalculate');
    // Route::post('/scp/ajax-optional-selected-adder', 'ScpController@ajaxOptionalSelectedAdderData');
    Route::post('/scp/addtocart', 'ScpController@addToCart')->name('scp.addtocart');
    Route::get('/scp/remove-cart/{id}', 'ScpController@removeCart')->name('scp.removecart');
    Route::get('/scp/ajax-qty-update', 'ScpController@ajaxQtyUpdate');
    Route::get('/scp/cart-item/{id}', 'ScpController@cartItems');
    Route::get('/scp/search-article-number', 'ScpController@searchByArticleNumber')->name('scp.searchbyarticle');
    Route::get('scp/item/delete', 'QuotationController@deleteSCPItemFromEditQuotation')->name('deleteSCPItemFromEditQuotation');

    // SCPV
    Route::get('/scpv_pump', 'ScpvController@index')->name('scpv.index');
    // A Code: 03-11-2025 Start
    Route::post('/scpv_price', 'ScpvController@get_price')->name('scpv.price');
    Route::post('/get_scpv_frame', 'ScpvController@get_frame')->name('scpv.get_frame');
    Route::post('/get_scpv_accessories', 'ScpvController@get_accessories')->name('scpv.get_accessories');
    Route::post('/get_scpv_motor_price', 'ScpvController@get_motor_price')->name('scpv.get_motor_price');

    Route::post('/scpv/ajax-optional-modal', 'ScpvController@ajaxOptionalModal');
    Route::post('/scpv/ajax-optional-selected-adder', 'ScpvController@ajaxOptionalSelectedAdderData');
    Route::get('/scpv/ajaxCalculate', 'ScpvController@ajaxCalculate');
    Route::post('/scpv/addtocart', 'ScpvController@addToCart')->name('scpv.addtocart');
    Route::get('/scpv/remove-cart/{id}', 'ScpvController@removeCart')->name('scpv.removecart');
    Route::get('/scpv/ajax-qty-update', 'ScpvController@ajaxQtyUpdate');
    Route::get('/scpv/cart-item/{id}', 'ScpvController@cartItems');
    Route::get('/scpv/search-article-number', 'ScpvController@searchByArticleNumber')->name('scpv.searchbyarticle');
    Route::get('scpv/item/delete', 'QuotationController@deleteSCPVItemFromEditQuotation')->name('deleteSCPVItemFromEditQuotation');
    // A Code: 03-11-2025 End

    Route::get('firefighting/item/delete', 'QuotationController@deleteFFItemFromEditQuotation')->name('deleteFFItemFromEditQuotation'); // A Code: 26-05-2026

    //SCH Pump
    Route::get('/sch_pump', 'schController@index_home')->name('sch.pump');
    //get_scp_frame
    // Route::get('/getCpbasic', 'addToCart@controlpanel_Basic')->name('cp.basicfile');
    Route::post('controlpanel/addtocart', 'CPCartController@addtocart')->name('cp.addtocart');
    Route::get('controlpanel/remove-cart/{id}', 'CPCartController@removeCart')->name('cp.removeCart');

    Route::get('controlpanel/cart/{id}', 'CPCartController@index')->name('cart.view');
    Route::get('controlpanel/cart-item/{id}', 'CPCartController@cartItems');
    Route::get('controlpanel/updatedTotalPrice', 'CPCartController@updatedTotalPrice');

    Route::get('/controlpanel/ajaxFilter', 'ControlpanelController@ajaxFilter');
    Route::get('/controlpanel/search-article-number', 'ControlpanelController@searchByArticleNumber')->name('cp.searchbyarticle');
    Route::get('/controlpanel/search-full-article-number-for-stock', 'ControlpanelController@searchByFullArticleNumberForStock')->name('cp.searchbyfullarticlestock'); // A Code: 13-04-2026
    Route::get('/controlpanel/ajax-qty-update', 'CPCartController@ajaxQtyUpdate');
    //Route::get('/controlpanel/ajax-detail-modal-cp', 'CPCartController@controlpanel/customer-information');
    Route::get('/controlpanel/ajax-detail-modal-cp', 'CPCartController@ajaxDetailModalControlPanel');
    Route::get('/scp/ajax-detail-modal-scp', 'ScpController@ajaxDetailModalScp');
    Route::get('/scpv/ajax-detail-modal-scpv', 'ScpvController@ajaxDetailModalScpv'); // A Code: 18-02-2026
    Route::get('/atmos/ajax-detail-modal-atmos', 'AtmosGigaController@ajaxDetailModalAtmos');
    Route::get('/booster/ajax-detail-modal-booster', 'BoosterSetController@ajaxDetailModalBooster');

    Route::post('/controlpanel/ajax-optional-modal', 'ControlpanelController@ajaxOptionalModal');
    Route::post('/controlpanel/ajax-optional-selected-adder', 'ControlpanelController@ajaxOptionalSelectedAdderData');
    // Route::get('/controlpanel/getMasterSheetPriceData', 'ControlpanelController@getMasterSheetPriceData');
    Route::get('/controlpanel/customer-information', 'CustomerController@index');
    Route::post('/controlpanel/save', 'CustomerController@save')->name('customer.save');
    Route::get('/controlpanel/quotation/{quotation_no}', 'QuotationController@index')->name('controlpanel.quotation');
    Route::get('controlpanel/quotations/updatedTotalPrice', 'QuotationController@updatedTotalPrice');
    Route::get('/controlpanel/quotations/user-list', 'QuotationController@userList')->name('controlpanel.quotations.userlist');
    Route::get('/controlpanel/quotations/edit/{quotation_no}', 'QuotationController@edit')->name('controlpanel.quotations.edit');
    Route::get('/controlpanel/quotations/status-update', 'QuotationController@ajaxStatusUpdate')->name('controlpanel.quotations.status.update');
    Route::get('/controlpanel/quotationquotations/user-lists/reason-update', 'QuotationController@ajaxReasonUpdate')->name('controlpanel.quotations.reason.update');
    Route::get('/controlpanel/quotations/pdf/{quotation_no}', 'PDFController@controlPanelQuotation');
    Route::get('/controlpanel/quotations/excel/{quotation_no}', 'PDFController@controlPanelQuotationExcel');

    Route::get('controlpanel/item/delete', 'QuotationController@deleteCPItemFromEditQuotation')->name('deleteCPFromEditQuotation');

    // booster/getPumpDetail
    Route::get('/booster-set', 'BoosterSetController@index')->name('boosterset');
    Route::get('/booster-set/getPumpDetail', 'BoosterSetController@getPumpDetailByType')->name('boosterset.pumpDetailByType');
    Route::get('/booster-set/getPumpAllModelNo', 'BoosterSetController@getPumpAllModelNo')->name('boosterset.getPumpAllModelNo');
    Route::get('/booster-set/calculateMechanicalComponent', 'BoosterSetController@calculateMechanicalComponent')->name('boosterset.calculateMechanicalComponent');
    Route::get('/booster-set/calcualtePriceInBOM', 'BoosterSetController@calcualtePriceInBOM'); //Temporary
    Route::get('/booster-set/mechanical-ajax-optional-html', 'BoosterSetController@ajaxOptionalHtml');
    Route::get('/booster-set/mechanical-ajax-adder-calculate', 'BoosterSetController@ajaxOptionalSelectedAdderCalulate');
    Route::post('/booster-set/addtocart', 'BoosterSetController@addToCart')->name('boosterset.addtocart');
    Route::get('/booster-set/remove-cart/{id}', 'BoosterSetController@removeCart')->name('boosterset.removecart');
    Route::get('/booster-set/ajax-qty-update', 'BoosterSetController@ajaxQtyUpdate');
    Route::get('/booster-set/cart-item/{id}', 'BoosterSetController@cartItems');
    Route::post('/booster/ajax-optional-modal-adder', 'BoosterAdderController@ajaxOptionalModal');
    Route::get('/booster/search-article-number', 'BoosterSetController@searchByArticleNumber')->name('boosterset.searchbyarticle');
    Route::get('/controlpanel/searchAjaxFilter', 'ControlpanelController@searchAjaxFilter');
    Route::get('/booster-set/searchCalculateMechanicalComponent', 'BoosterSetController@searchCalculateMechanicalComponent')->name('boosterset.searchCalculateMechanicalComponent');
    Route::get('booster-set/item/delete', 'QuotationController@deleteBoosterItemFromEditQuotation')->name('deleteBoosterFromEditQuotation');

    //add items with quotation number routes starts
    Route::get('/{quotation_number}', 'QuotationController@home_page')->name('quotatio_home_page');
    Route::get('/booster-set-by-quotation/{quotation_number}', 'BoosterSetController@index')->name('boostersetByQuotation');
    Route::get('/controlPanel-set-by-quotation/{quotation_number}', 'ControlpanelController@index')->name('controlPanelsetByQuotation');
    Route::get('/scp_pump-set-by-quotation/{quotation_number}', 'ScpController@index')->name('scpsetByQuotation');
    Route::get('/atmos_giga-set-by-quotation/{quotation_number}', 'AtmosGigaController@index')->name('atmos_gigaByQuotation');
    //add items with quotation number routes ends

});

// Route::get('change-password', 'ChangePasswordController@index');
// Route::post('change-password', 'ChangePasswordController@store')->name('change.password');
include('admin.php');
