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

Auth::routes();
// Route::get('/{any}', function () {
//     return view('app');
// })->where('any', '.*');

// ------------------ ホーム --------------------------------
//Route::get('/', 'HomeController@index')->middleware('auth');
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@index')->name('home');

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

