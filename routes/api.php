<?php

Route::group(['prefix' => 'v1', 'as' => 'admin.', 'namespace' => 'Api\V1\Admin'], function () {
    Route::apiResource('permissions', 'PermissionsApiController');

    Route::apiResource('roles', 'RolesApiController');

    Route::apiResource('users', 'UsersApiController');

    Route::apiResource('products', 'ProductsApiController');
});

// Route::group(['middleware' => ['auth', 'user']], function () {
//    Route::get('/abc', 'Frontend\CPBasicController@controlpanel_Basic');     
//});

Route::group(['namespace' => 'Api\Quotation'], function () {
    Route::post('get_all_products', 'ApiGetQuotationController@get_all_products');
    Route::post('getBOM', 'ApiGetQuotationController@getBOM');
    Route::post('getBOMCheckStatus', 'ApiGetQuotationController@getBOMCheckStatus');
});

Route::group(['namespace' => 'Api\Project_order_from_WIPTracker'], function () {
    Route::post('complete_order_from_wiptracker', 'ApiGetWIPTrackerDataController@complete_order_from_wiptracker');
});