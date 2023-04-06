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

// Route::get('/', function () {
//     return view('home');
// });



//Auth::routes([
//    'register' => false,
//    'reset'    => false,
//]);

//Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
//Route::post('login', 'Auth\LoginController@login');
//Route::post('logout', 'Auth\LoginController@logout')->name('logout');

Route::get('login', 'HomeController@contents_select')->name('login');
Route::post('login', 'HomeController@contents_select');
Route::post('logout', 'HomeController@contents_select')->name('logout');


// Route::get('/{any}', function () {
//     return view('app');
// })->where('any', '.*');

// ------------------ ホーム --------------------------------
//Route::get('/', 'HomeController@index')->middleware('auth');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index');

Route::get('/view_inventory', 'ViewInventoryController@index');
Route::get('/inventory', 'ViewInventoryController@search');
Route::get('/stocksearch', 'StockController@index');
// 預かり
Route::get('/view_inventory_a', 'ViewInventoryController@inventorya');
Route::post('/view_inventory_a/get', 'ViewInventoryController@getDataA');
Route::post('/view_inventory_a/getone', 'ViewInventoryController@getDataAone');
Route::post('/view_inventory_a/update', 'ViewInventoryController@fixA');
Route::post('/view_inventory_a/insert', 'ViewInventoryController@storeA');
Route::post('/view_inventory_a/search', 'ViewInventoryController@getDataAsearch');

// 在庫
Route::get('/view_inventory_z', 'ViewInventoryController@inventoryz');
Route::post('/view_inventory_z/get', 'ViewInventoryController@getDataZ');
Route::post('/view_inventory_z/getone', 'ViewInventoryController@getDataZone');
Route::post('/view_inventory_z/update', 'ViewInventoryController@fixZ');
Route::post('/view_inventory_z/insert', 'ViewInventoryController@storeZ');
Route::post('/view_inventory_z/search', 'ViewInventoryController@getDataZsearch');

// ステータス変更（ゴミ箱）
Route::get('/view_inventory_dust', 'ViewInventoryController@inventorydust');
Route::post('/view_inventory_dust/get', 'ViewInventoryController@getDataDust');
Route::post('/view_inventory_dust/update_a', 'ViewInventoryController@statusA');
Route::post('/view_inventory_dust/update_z', 'ViewInventoryController@statusZ');

// 棚卸 stock
Route::get('/stock', 'StockController@stockTop');
Route::get('/stock_a', 'StockController@indexStockA');
Route::get('/stock_z', 'StockController@indexStockZ');
Route::post('/stock_a/invget', 'StockController@getDataMiniA');
Route::post('/stock_a/insert', 'StockController@storeAllA');
Route::post('/stock_a/stockget', 'StockController@getStockA');
Route::post('/stock_a/update', 'StockController@fixA');



// 資材 在庫
Route::get('/mm', 'MatManageController@home');
Route::get('/material_management', 'MatManageController@index');
Route::post('/material_management/get', 'MatManageController@getData');
Route::post('/material_management/getone', 'MatManageController@getDataone');
Route::post('/material_management/update', 'MatManageController@fix');
Route::post('/material_management/insert', 'MatManageController@store');
Route::post('/material_management/search', 'MatManageController@getDatasearch');
Route::post('/material_management/delete', 'MatManageController@delete');

// 資材 在庫 閲覧ONLY
Route::get('/v', 'MMviewController@index');

// 資材 棚卸 stock
Route::get('/mmstock_top', 'MMStockController@stockTop');
Route::get('/mmstock', 'MMStockController@indexStock');
Route::post('/mmstock/invget', 'MMStockController@getDataMini');
Route::post('/mmstock/insert', 'MMStockController@storeAll');
Route::post('/mmstock/stockget', 'MMStockController@getStock');
Route::post('/mmstock/update', 'MMStockController@fix');

// ステータス変更（抹消）
Route::get('/mmdust', 'MatManageController@dust');
Route::post('/mmdust/get', 'MatManageController@getDataDust');
Route::post('/mmdust/update', 'MatManageController@status');
