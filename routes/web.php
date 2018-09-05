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

// Registration Routes...
/*$this->get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
$this->post('register', 'Auth\RegisterController@register')*/;

Route::get('dashboard','DashboardController@dashboard');

/*
 * Customer Related
 */
Route::group(['prefix' => 'customer'], function () {
    /*Route::get('/anyDatatable','CustomerController@anyDatatable');
    Route::get('/_getOutstandingBalance','CustomerController@_getOutstandingBalance');
    Route::get('/postCreate','CustomerController@postCreate');
    Route::get('/postDuplicate','CustomerController@postDuplicate');
    Route::get('/getProducts/{id}','CustomerController@getProducts');*/

    Route::get('getIndex','CustomerController@getIndex');
    Route::get('create','CustomerController@postCreate');
    Route::get('getShow/{id}','CustomerController@getShow');
    Route::get('contact-edit/{id}','CustomerController@getContactEdit');
    Route::get('contact-delete/{id}','CustomerController@postContactDelete');
    Route::get('address-edit/{id}','CustomerController@getAddressEdit');

    ////////////////////////////////////////////////////////////////////////////

    Route::post('contact-add','CustomerController@postContactAdd');
    Route::post('contact-delete/{id}','CustomerController@postContactDelete');
    Route::post('address-add','CustomerController@postAddressAdd');
    Route::post('address-delete/{id}','CustomerController@postAddressDelete');
    Route::post('contact-edit/{id}', 'CustomerController@postContactEdit');
    Route::post('address-edit/{id}', 'CustomerController@postAddressEdit');

    Route::post('{id}','CustomerController@update');
});


