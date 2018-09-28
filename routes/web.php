<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');

//Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

/*
 * Login Related
 */
$this->get('login', 'Auth\LoginController@showLoginForm')->name('login');
$this->post('login', 'Auth\LoginController@login');
$this->get('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('dashboard','DashboardController@dashboard');

/** Customer Related */
Route::group(['prefix' => 'customers'], function () {

    Route::group(['prefix' => '{id}/contacts'], function () {
        Route::post('/add','CustomerController@addContact');
        Route::get('/{contactId}','CustomerController@getContact');
        Route::patch('/{contactId}', 'CustomerController@updateContact');
        Route::delete('/{contactId}','CustomerController@deleteContact');
    });

    Route::group(['prefix' => '{id}/addresses'], function () {
        Route::post('/add','CustomerController@addAddress');
        Route::get('/{addressId}','CustomerController@getAddress');
        Route::patch('/{addressId}', 'CustomerController@updateAddress');
        Route::delete('/{addressId}','CustomerController@deleteAddress');

    });

    Route::get('/','CustomerController@index');
    Route::get('/create','CustomerController@createNew');
    Route::post('/create','CustomerController@store');
    Route::get('/{id}','CustomerController@show');
    Route::post('{id}','CustomerController@update');
});

/** Products Related */
Route::group(['prefix' => 'products'], function () {
    Route::get('/getdata', 'ProductController@getProductData')->name('products/getdata');
    Route::get('/','ProductController@index');
    Route::get('/{id}','ProductController@show');
    Route::post('/{id}','ProductController@update');
    Route::get('/getAttributes/{id}', 'ProductController@getAttributes');
//    Route::get('/getPrices/{id}', 'ProductController@getPrices');
//    Route::update('/getPrices/{id}', 'ProductController@updatePrices');
    Route::resource('prices','PriceController')->except([
        'index'
    ]);
    Route::get('/getImages/{id}', 'ProductController@getImages');
    Route::get('/getDownloads/{id}', 'ProductController@getDownloads');
    Route::get('/getStocks/{id}', 'ProductController@getStocks');
    Route::get('/getSetup/{id}', 'ProductController@getSetup');
    Route::get('/getSync/{id}', 'ProductController@getSync');
});

/** Vendors */
Route::group(['prefix' => 'vendors'], function () {
    Route::get('/','VendorController@index');
    Route::get('/create','VendorController@createNew');
    Route::get('/{id}','VendorController@show');
    Route::post('/create','VendorController@store');
    Route::post('/{id}','VendorController@update');

    Route::post('/{id}/contacts','VendorController@addContact');
    Route::get('/{id}/contacts/{contactId}','VendorController@getContact');
    Route::post('/{id}/contacts/{contactId}','VendorController@updateContact');
    Route::delete('/{id}/contacts/{contactId}','VendorController@deleteContact');
});

/** Expense */
Route::resource('expenses','ExpenseController');

/** Purchase */

Route::get('purchases/getdata', 'PurchaseController@getPurchaseData')->name('purchase/getdata');
Route::get('purchases/receive/{id}', 'PurchaseController@getReceive')->name('purchase.getReceive');
Route::post('purchases/receive/{id}', 'PurchaseController@postReceive')->name('purchase.postReceive');
Route::get('purchases/payments/{id}', 'PurchaseController@getPayments')->name('purchase.getPayments');
Route::get('purchases/records/{id}', 'PurchaseController@getRecords')->name('purchase.getRecords');
Route::resource('purchases','PurchaseController');


//Route::resource('/chart_of_accounts', 'ChartOfAccountController');
//Route::resource('/value_lists', 'ValueListController');
/** Order */
Route::get('orders/getdata', 'OrderController@getOrderData')->name('orders/getdata');
Route::get('orders/payments/{id}', 'OrderController@getPayments')->name('order.getPayments');
Route::post('orders/payments/{id}', 'OrderController@postPayments')->name('order.postPayments');
Route::delete('orders/payments/{id}', 'OrderController@deletePayment')->name('order.deletePayment');
Route::get('orders/records/{id}', 'OrderController@getRecords')->name('order.getRecords');
Route::get('orders/records/getdata', 'OrderController@getOrderRecordData')->name('orders/records/getdata');
Route::resource('orders', 'OrderController');