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

Route::group(['middleware' => ['web']], function () {
    Route::get('/', 'HomeController@index');
    Route::post('login', 'LoginController@authenticate')->name('login');
    Route::post('logout', 'LoginController@logout')->name('logout');    

    Auth::routes();
});


Route::middleware(['auth', 'web'])->group(function(){
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/product', function () { return view('product'); })->name('product');    
    // Route::get('/adjust', function () { return view('adjust'); })->name('adjust');    
    Route::get('/report', function () { return view('report'); })->name('report');
    Route::get('/invoice_create', function () { return view('invoice_create'); })->name('invoice_create');
    Route::get('/invoice_view', function () { return view('invoice_view'); })->name('invoice_view');
    Route::get('/invoice_edit/{id}', function () { return view('invoice_edit'); })->name('invoice_edit');
    Route::get('/purchase_create', function () { return view('purchase_create'); })->name('purchase_create');
    Route::get('/purchase_view', function () { return view('purchase_view'); })->name('purchase_view');
    Route::get('/purchase_edit/{id}', function () { return view('purchase_edit'); })->name('purchase_edit');
    Route::get('/print', function () { return view('print'); })->name('print');

    Route::get('/newbranch', 'NewBranchController@create')->name('newbranch');   
    Route::post('/newbranch', 'NewBranchController@store')->name('newbranch');
    
});
