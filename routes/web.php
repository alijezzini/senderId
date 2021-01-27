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

Auth::routes();

Route::get('/', 'HomeController@index')->middleware('auth')->name('home');

Route::get('/home', 'HomeController@index')->middleware('auth')->name('home');

Route::get('/vendornotes','noteController@index')->middleware('auth')->name('searchnote');;

Route::get('/searchsenders', 'SearchController@searchall')->middleware('auth')->name('searchall');

Route::get('/searchvendornotes', 'SearchController@searchallnotes')->middleware('auth')->name('searchall');

Route::get('/senders', 'SearchController@searchsender')->middleware('auth')->name('searchsender');

Route::get('/addvendor', 'vendorController@index')->middleware('auth')->name('addvendor');

Route::post('/submit','senderController@submit');

Route::post('/submitVendor','vendorController@submit');

Route::post('/submitNote','noteController@submit');

Route::post('/getOperators','senderController@getOperator');

Route::post('/deleteSender','senderController@deleteSender');

Route::post('/deleteNote','noteController@deleteNote');

Route::post('/deleteNotesFiles','noteController@deleteNotesFiles');

Route::post('/deleteFile','noteController@deleteFile');

Route::post('/editSender','senderController@editSender');

Route::post('/editNote','noteController@editNote');

Route::post('/editVendor','vendorController@editVendor');

Route::post('/deleteVendor','vendorController@deleteVendor');

Route::post('/searchsender','SearchController@getsenderTable');

Route::resource('column-searching', 'ColumnSearchingController');

Route::post('/searchnote','SearchController@getnote');