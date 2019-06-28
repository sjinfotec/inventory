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

<<<<<<< HEAD
Route::get('/', function () {
    return view('welcome');
});
=======
// Route::get('/', function () {
//     return view('home');
// });

Auth::routes();
// Route::get('/{any}', function () {
//     return view('app');
// })->where('any', '.*');

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/daily', 'DailyWorkingInformationController@index')->middleware('auth');
Route::get('/daily/show', 'DailyWorkingInformationController@show')->middleware('auth');

Route::get('/monthly', 'MonthlyWorkingInformationController@index')->middleware('auth');
Route::get('/create_shift_time', 'CreateShiftTimeController@index')->middleware('auth');
Route::post('/create_shift_time/store', 'CreateShiftTimeController@store')->middleware('auth');
>>>>>>> feature-takeda
