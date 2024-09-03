<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| View Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/**
 * Route for User view d
 */
// get method
Route::get('profile', 'PanelController@profile');
Route::get('loan-aggrement', 'PanelController@loanAggrement');
Route::get('loan-aggrement/{el}', 'PanelController@pickAggrement');
Route::get('member-loans', 'TsLoansController@index');
Route::get('get-loans', 'TsLoansController@getLoans');
Route::get('check-reschedule', 'TsLoansController@checkReschedule');
Route::get('loan-detail/{el}', 'TsLoansController@loanDetail');
Route::get('list-sisa-pinjaman-reschedule/{el}', 'TsLoansController@list_sisa_pinjaman_reschedule');
Route::get('list-sisa-pinjaman/{el}/{es}', 'TsLoansController@list_sisa_pinjaman');
Route::get('list-approval/{el}', 'TsLoansController@list_approval');

// post method
Route::post('detail-approved', 'TsLoansController@detailApproved');

