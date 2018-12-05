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

/** User Profile */
Route::resource('userProfiles', 'UserProfileController');

/** User */
Route::get('usersList/loginAs/{id}','UserController@postLoginAs');
Route::resource('usersList', 'UserController');

/** User */
Route::resource('companies','CompanyController');

/** Currency Calculator */
Route::resource('currency_calculator','CurrencyCalculatorController');

/** Setting */
Route::group(['prefix' => 'settings'], function () {
    Route::resource('/','SettingController');
    /** Value List */
    Route::resource('/value_lists','ValueListController');
    /** Product Categories */
    Route::get('/product_categories/lower_level/{id}','ProductCategoryController@showDifferentLevel');
    Route::patch('/product_categories/update/{id}','ProductCategoryController@updateAddThumbnail');
    Route::get('/product_categories/update/{id}','ProductCategoryController@getUpdate');
    Route::delete('/product_categories/update/category_image_delete/{id}','ProductCategoryController@deleteImage');
    Route::patch('/product_categories/update/update_downloadable_files/{id}','ProductCategoryController@updateDownloadableFile');
    Route::patch('/product_categories/update/category_atrributes/{id}','ProductCategoryController@updateAttributes');
    Route::resource('/product_categories','ProductCategoryController');
    /** Taxcode */
    Route::resource('/taxcodes','TaxcodeController');
    /** Payment Terms */
    Route::resource('/payment_terms','PaymentTermController');
    /** Shipping Terms */
    Route::resource('/shipping_terms','ShippingTermController');
    /** Chart of Accounts Terms */
    Route::get('/chart_of_accounts/lower-level/{id}','ChartOfAccountController@showDifferentLevel');
    Route::resource('/chart_of_accounts','ChartOfAccountController');
});





/** Customer Related */
Route::group(['prefix' => 'customers'], function () {

    Route::get('/getdata', 'CustomerController@getCustomerData')->name('customers/getdata');
    Route::group(['prefix' => '{id}/contacts'], function () {
        Route::post('/add','CustomerController@addContact');
        Route::get('/{contactId}','CustomerController@getContact');
        Route::patch('/{contactId}', 'CustomerController@updateContact');
        Route::delete('/{contactId}','CustomerController@deleteContact');
    });
    Route::get('/getPricelist/{id}','CustomerController@getPricelist');


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

    Route::get('/history/{id}/getdata', 'HistoryController@anyDtOrders')->name('history/getdata');
    Route::resource('history','HistoryController');
    Route::get('/products/{id}','CustomerController@getProducts');
    Route::get('/products/getdata', 'CustomerController@getPosts')->name('products/getdata');




});

/** Products Related */
Route::group(['prefix' => 'products'], function () {

    Route::resource('setup', 'SetupController')->except([
        'index'
    ]);
    Route::resource('stock','StockController')->except([
        'index'
    ]);
    Route::get('downloads/file-download/{image}','DownloadController@downloadFile');
    Route::resource('downloads','DownloadController')->except([
        'index'
    ]);
    Route::resource('attributes', 'AttributeController')->except([
        'index'
    ]);
    Route::resource('prices','PriceController')->except([
        'index'
    ]);
    Route::group(['prefix' => 'images'], function () {

        Route::patch('/mark-as-main-image/{image}', 'ImageController@getMarkAsMainImage');
        Route::patch('/unmark-as-main-image/{image}', 'ImageController@getUnmarkAsMainImage');
        Route::get('/image-download/{image}', 'ImageController@downloadImage');

    });
    Route::resource('images', 'ImageController')->except([
        'index'
    ]);

    Route::get('/getdata', 'ProductController@getProductData')->name('products/getdata');
    Route::get('/','ProductController@index');
    Route::get('/create','ProductController@createNew');
    Route::get('/{id}','ProductController@show');
    Route::patch('/{id}','ProductController@update');
    Route::post('','ProductController@store');

    Route::get('/getSync/{id}', 'ProductController@getSync');
});

/** Vendors */
Route::group(['prefix' => 'vendors'], function () {

    Route::get('/getdata', 'VendorController@getVendorData')->name('vendors/getdata');
    Route::get('/','VendorController@index');
    Route::get('/create','VendorController@createNew');
    Route::get('/{id}','VendorController@show');
    Route::post('/create','VendorController@store');
    Route::post('/{id}','VendorController@update');

    Route::get('/history/{id}/getdata', 'VendorController@anyDtPurchases')->name('history/getdata');
    Route::get('/history/{id}','VendorController@getHistory');


    Route::post('/{id}/contacts','VendorController@addContact');
    Route::get('/{id}/contacts/{contactId}','VendorController@getContact');
    Route::post('/{id}/contacts/{contactId}','VendorController@updateContact');
    Route::delete('/{id}/contacts/{contactId}','VendorController@deleteContact');
});

/** Expense */
Route::get('expenses/getdata', 'ExpenseController@getExpenseData')->name('expenses/getdata');
Route::resource('expenses','ExpenseController');

/** Purchase */

Route::group(['prefix' => 'purchases'], function () {

    Route::get('/getdata', 'PurchaseController@getPurchaseData')->name('purchase/getdata');

    Route::delete('/{id}/lineItem/{item}','PurchaseController@lineItemDelete');
    Route::get('/update_line_item/{item}','PurchaseController@getLineItemUpdate');
    Route::patch('/update_line_item/{item}','PurchaseController@postLineItemUpdate');

    Route::get('/line_item_add/{id}','PurchaseController@showLineItemAdd');
    Route::get('/line_item_add/{id}/getdata', 'PurchaseController@anyDtAvailableProducts')->name('purchase_line_items/getdata');
    Route::post('/line_item_add/','PurchaseController@postLineItemAdd')->name('add_line_items');

    Route::get('/receive/{id}', 'PurchaseController@getReceive')->name('purchase.getReceive');
    Route::post('/receive/{id}', 'PurchaseController@postReceive')->name('purchase.postReceive');

    Route::get('/payments/{id}', 'PurchaseController@getPayments')->name('purchase.getPayments');
    Route::post('/payment-add/{id}', 'PurchaseController@postPaymentAdd');
    Route::delete('/payments-delete/{id}','PurchaseController@getPaymentDelete');

    Route::get('/records/{id}', 'PurchaseController@getRecords')->name('purchase.getRecords');
    Route::delete('/receive-delivery-delete/{id}','PurchaseController@getDeliveryDelete');

    Route::get('/vendorsList','PurchaseController@vendorsList');
    Route::get('/vendorsList/getdata', 'PurchaseController@getVendorslist')->name('vendorsList/getdata');

    Route::get('/duplicate_order/{id}','PurchaseController@getDuplicate');

    Route::get('/change_vendor/{id}','PurchaseController@getChangeVendor');
    Route::get('/change_vendor/{id}/getdata', 'PurchaseController@getVendorslistChange')->name('change_vendor/getdata');
    Route::post('/change_vendor/{id}', 'PurchaseController@postChangeVendor');

    Route::get('/change_status/{id}','PurchaseController@getChangeStatus');
    Route::post('/change_status/{id}','PurchaseController@postChangeStatus');



//    Route::resource('','PurchaseController');

});
Route::resource('purchases','PurchaseController');



/** Order */
Route::get('orders/getdata', 'OrderController@getOrderData')->name('orders/getdata');
Route::get('orders/payments/{id}', 'OrderController@getPayments')->name('order.getPayments');
Route::post('orders/payments/{id}', 'OrderController@postPayments')->name('order.postPayments');
Route::delete('orders/payments/{id}', 'OrderController@deletePayment')->name('order.deletePayment');
Route::get('orders/records/{id}', 'OrderController@getRecords')->name('order.getRecords');
Route::get('orders/records/getdata', 'OrderController@getOrderRecordData')->name('orders/records/getdata');

Route::get('orders/update_line_item/{item}','OrderController@getLineItemUpdate');
Route::patch('orders/update_line_item/{item}','OrderController@postLineItemUpdate');
Route::delete('orders/line_item_delete/{item}','OrderController@lineItemDelete')->name('lineItem.delete');

Route::get('orders/line_item_add/{id}','OrderController@showLineItemAdd');
Route::get('orders/line_item_add/{id}/getdata', 'OrderController@anyDtAvailableProducts')->name('line_items/getdata');
Route::post('orders/line_item_add/','OrderController@postLineItemAdd')->name('add_line_items');

Route::post('orders/records/{id}','OrderController@postRecord');
Route::get('orders/customersList','OrderController@customersList');
Route::get('orders/customersList/getdata', 'OrderController@getCustomerslist')->name('cusomtersList/getdata');
Route::resource('orders', 'OrderController');

Route::get('orders/changelog/{id}','OrderController@getChangelog');
Route::get('orders/changelog/invoices/{id}','OrderController@getInvoices');


/** Report */
//Route::group(['prefix' => 'reports'], function () {
//
//    Route::resource('/','ReportController');
//});

Route::group( [ 'prefix' => 'reports' ], function()
{
    //routes for ReportController
//    Route::get('getOrderbacklog','ReportController@getOrderbacklog')->middleware('auth');
//    Route::get('getExports','ReportController@getExports')->middleware('auth');
//    Route::get('getDownloads','ReportController@getDownloads')->middleware('auth');

    Route::get('getStocklist','ReportController@getStocklist')->middleware('auth');
//    Route::get('postStocklist','ReportController@postStocklist')->middleware('auth');
    Route::get('getTopCustomer','ReportController@getTopCustomer')->middleware('auth');
//    Route::get('getTest','ReportController@getTest')->middleware('auth');
    Route::get('getTopProducts','ReportController@getTopProducts')->middleware('auth');
//    Route::post('postTopProducts','ReportController@postTopProducts')->middleware('auth');
//    Route::get('getCustomerProducts','ReportController@getCustomerProducts')->middleware('auth');
//    Route::post('postTopCustomer','ReportController@postTopCustomer')->middleware('auth');

//    Route::get('postCustomerProducts','ReportController@postCustomerProducts')->middleware('auth');
//    Route::get('getProductsCustomer','ReportController@getProductsCustomer')->middleware('auth');
//    Route::get('postProductsCustomer','ReportController@postProductsCustomer')->middleware('auth');
//    Route::get('getEmployeeBasicData','ReportController@getEmployeeBasicData')->middleware('auth');
//    Route::get('getDailysales','ReportController@getDailysales')->middleware('auth');
//    Route::get('anyCustomerTurnoverInvoices','ReportController@anyCustomerTurnoverInvoices')->middleware('auth');
//    Route::get('anyCustomerTurnoverOrders','ReportController@anyCustomerTurnoverOrders')->middleware('auth');
//    Route::get('anyVendorTurnover','ReportController@anyVendorTurnover')->middleware('auth');
//    Route::get('getMonthlyTurnover','ReportController@getMonthlyTurnover')->middleware('auth');
//    Route::get('getGeneralHealth','ReportController@getGeneralHealth')->middleware('auth');
//    Route::get('getPurchasesEarlyDeliveries','ReportController@getPurchasesEarlyDeliveries')->middleware('auth');
//    Route::get('getProduction','ReportController@getProduction')->middleware('auth');
//    Route::post('postDownloads','ReportController@postDownloads')->middleware('auth');

    Route::get('/dashboard','ReportController@getDashboard')->middleware('auth');
    Route::get('kpi','ReportController@getKpi')->middleware('auth');
    Route::get('getExpensesByCategory','ReportController@getExpensesByCategory')->middleware('auth');
//    Route::post('/save_downloads','ReportController@saveDownloads');//new route
//    Route::post('/dashboard',"ReportController@postAnyDashboard");//new route for reclaculate fucntion
//    Route::get('/total_gross_profit/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@total_gross_profit');
//    Route::get('/detailed_gross/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@detailed_gross_profit');
//    Route::get("/details_get_pos_placed/start_date={date_start}/end_date={date_end}/currency_code={currency_code}","ReportController@detailed_get_pos_placed");
//    Route::get("/details_po_payments/start_date={date_start}/end_date={date_end}/currency_code={currency_code}","ReportController@details_po_payments");
//    Route::get("/detailed_expenses/start_date={date_start}/end_date={date_end}/currency_code={currency_code}","ReportController@detailed_expenses");
//    Route::get("/detailed_invoices_written/start_date={date_start}/end_date={date_end}/currency_code={currency_code}","ReportController@detailed_invoices_written");
//    Route::get("/details_invoices_shipped/start_date={date_start}/end_date={date_end}/currency_code={currency_code}","ReportController@details_invoices_shipped");
//    Route::get("/details_invoices_payment_recieved/start_date={date_start}/end_date={date_end}/currency_code={currency_code}","ReportController@details_invoices_payment_recieved");
//    Route::get('/get-export-StockList','ReportController@getExportDownloads');
//    Route::get('/exportGrossProfit/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@exportGrossProfit');
//    Route::get('/exportPoPlaced/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@exportPoPlaced');
//    Route::get('/exportPoPayments/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@exportPoPayments');
//    Route::get('/exportExpenses/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@exportExpenses');
//    Route::get('/export_invoices_written/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@export_invoices_written');
//    Route::get('/export_invoices_shipped/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@export_invoices_shipped');
//    Route::get('/export_invoices_payment_recieved/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@export_invoices_payment_recieved');
//    Route::post('/turnover_0','ReportController@get_turnover_0');

    Route::get('/export_top_customers/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@export_top_customers');
//    Route::get('/export_top_products/start_date={date_start}/end_date={date_end}/currency_code={currency_code}','ReportController@export_top_products');
//    Route::get('/export_kpis','ReportController@export_kpis');
    Route::get('/export_excel/excel','ExportExcelController@excel')->name('export_excel.excel');
//    testing route
//    Route::get('/download_test_excel','ExportExcelController@export');
//
//    kpi export routes
    Route::get('/createKpiExcel','ExportExcelController@downloadKpiExcel');

    //    topProduct export routes
    Route::get('/createTopProductExcel','ExportExcelController@downloadTopProductExcel');

    //export Expenses routes
    Route::get('/createExpensesExcel','ExportExcelController@downloadExpensesByCategory');

    //export stocklist routes
    Route::get('/createInventoryExcel','ExportExcelController@downloadStockList');


    Route::resource('/','ReportController');
});


/** PDF Generator */

Route::group( [ 'prefix' => 'pdf' ], function() {
    Route::get('/purchase-pdf/{id}', 'PDFController@purchasePDF');
    Route::get('/quotation/{id}', 'PDFController@quotation');
    Route::get('/acknowledgement/{id}', 'PDFController@order_acknowledgement');
    Route::get('/order-confirmation/{id}', 'PDFController@order_confirmation');
    Route::get('/proforma_invoice/{id}', 'PDFController@proforma_invoice');
    Route::get('/commercial_invoice/{id}', 'PDFController@commercial_invoice');
    Route::get('/packing_list/{id}', 'PDFController@package_list');

//snappy pdf testing
    Route::get('/purchase_order/{id}', 'PDFController@purchasePDF');
    Route::get('/download_saved_pdf_file/{id}', 'PDFController@downloadPdfFile')->name('pdf.download');

});

/** email */
Route::get('testmail','TestController@testmail');
//testing route
Route::get('testmailpreview', function(){
    return new App\Mail\TestEmail();
});

Route::post('orders/records/{id}/sendEmail','EmailController@sendOrderEmail');
Route::post('purchases/records/{id}/sendEmail','EmailController@sendPurchaseEmail');







