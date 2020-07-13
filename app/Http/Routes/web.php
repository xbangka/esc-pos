<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
date_default_timezone_set('Asia/Jakarta');

Route::get(sha1('file'.date('mjD')).'.js'   , 'Dashboard\JsViewCtrl@_index');
Route::get(sha1('file'.date('mY')).'.css'   , 'Dashboard\CssViewCtrl@_index');
Route::get('logout'                         , 'LogoutCtrl@_logout');
Route::get('barcode-image'                  , 'HomeCtrl@get_barcode_image');
Route::get('print-barcode'                  , 'HomeCtrl@printBarcode');
Route::get('get-price-references'           , 'HomeCtrl@get_price_references');
Route::get('/',  function (Request $request) {
    if($request->session()->has('udata')) {
        return redirect('dashboard');;
    }else{
        return view('welcome');
    }
});
//
// ================== Login & Logout =====================
//
Route::group(['middleware' => 'autoSignin'], function () {
    Route::get( 'admin'    , 'HomeCtrl@_login');
    Route::post('signin'   , 'HomeCtrl@_signin');
});
// =======================================================


//
// ========================== Admin Panel ===========================
//
Route::group(       ['middleware'   => 'checkSession'], function () {
    Route::group(   ['namespace'    => 'Dashboard'],    function()
    {   
        Route::get('dashboard'         , 'DashboardCtrl@_index');
        

        
        //
        // == Categories Produk ===========================
        //
        Route::post('categories/insert-update-delete'   , 'CategoriesCtrl@insert_update_delete');
        Route::get( 'categories/data'                   , 'CategoriesCtrl@getData');
        Route::get( 'categories'                        , 'CategoriesCtrl@index');
        
        
        //
        // -- Retail Units --------------------------------
        //
        Route::post('retail-units/insert-update-delete' , 'RetailUnitsCtrl@insert_update_delete');
        Route::get( 'retail-units/data'                 , 'RetailUnitsCtrl@getData');
        Route::get( 'retail-units'                      , 'RetailUnitsCtrl@index');
        

        //
        // ~~ Stores  ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~
        //
        Route::post('stores/insert-update-delete'   , 'StoresCtrl@insert_update_delete');
        Route::get( 'stores/data'                   , 'StoresCtrl@getData');
        Route::post('stores/get-users'              , 'StoresCtrl@getUsers');
        Route::get( 'stores'                        , 'StoresCtrl@index');


        //
        // /\ Products  /\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/\/
        //
        Route::post('products/insert-update-delete' , 'ProductsCtrl@insert_update_delete');
        Route::get( 'products/data'                 , 'ProductsCtrl@getData');
        Route::get( 'products'                      , 'ProductsCtrl@index');


        //
        // @@ Users  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        Route::post('users/insert-update-delete', 'UsersCtrl@insert_update_delete');
        Route::get( 'users/data'                , 'UsersCtrl@getData');
        Route::get( 'users'                     , 'UsersCtrl@index');
        
        
        //
        // ** Statuses  * * * * * * * * * * * * * * * * * *
        //
        Route::post('statuses/insert-update-delete' , 'StatusesCtrl@insert_update_delete');
        Route::get( 'statuses/data'                 , 'StatusesCtrl@getData');
        Route::get( 'statuses'                      , 'StatusesCtrl@index');


        //
        // == My Employes  = == = == = == = == = == = == ==
        //
        Route::post('my-employes/insert-update-delete'  , 'MyEmployesCtrl@insert_update_delete');
        Route::get( 'my-employes/data'                  , 'MyEmployesCtrl@getData');
        Route::get( 'my-employes'                       , 'MyEmployesCtrl@index');


        //
        // ++ My Products  +++++++++++++++++++++++++++++++++++++
        //
        Route::post('my-products/new-data'              , 'MyProductsCtrl@newData');
        Route::post('my-products/cek-new-barcode'       , 'MyProductsCtrl@cekNewBarcode');
        Route::post('my-products/get-price-variations'  , 'MyProductsCtrl@getPriceVariations');
        Route::post('my-products/change-status-price'   , 'MyProductsCtrl@changeStatusPrice');
        Route::post('my-products/change-status-discount', 'MyProductsCtrl@changeStatusDiscount');
        Route::post('my-products/save-data-price'       , 'MyProductsCtrl@saveDataPrice');
        Route::post('my-products/save-data-discount'    , 'MyProductsCtrl@saveDataDiscount');
        Route::get( 'my-products/data'                  , 'MyProductsCtrl@getData');
        Route::get( 'my-products'                       , 'MyProductsCtrl@index');
        
    });
    
});


//
// ========= Index Front ==========
//
Route::group(   ['namespace'    => 'Pos'],    function() { 
    
    /// test file * * * * * * * * * * * * * * * * * * * * * * * * * *  
    Route::get('repo-barcode'   , 'TestCtrl@_index');
    Route::get('get-val'        , 'TestCtrl@_get_val');

    Route::get('repo-prices'    , 'TestCtrl@_prices');
    Route::get('get-upc'        , 'TestCtrl@_get_upc');
    Route::get('get-prices'     , 'TestCtrl@_get_prices');
    
    Route::get('uuid'           , 'TestCtrl@_gen_uuid_view');
    Route::get('gen-uuid'       , 'TestCtrl@_gen_uuid');

    Route::get('gen-update'     , 'TestCtrl@_barcode_view');
    Route::get('u-product-id'   , 'TestCtrl@_update_product_id');
    
    /// Kasir POS file  * * * * * * * * * * * * * * * * * * * * * * *  
    Route::post('checking-keyas', 'AppposCtrl@_checking_keyas');
    Route::post('get-products'  , 'AppposCtrl@_get_products');
    Route::post('send-transaction' , 'AppposCtrl@_send_transaction');
    Route::post('update-data-local', 'AppposCtrl@_update_data_local');
    Route::get('{code}'         , 'AppposCtrl@_mystore');
});