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
 * Route for User insert data
 */
Route::post('save-loan', 'PanelController@saveLoan');
Route::post('save-reschedule', 'PanelController@saveReschedule');
Route::post('add-tenor', 'TsLoansController@addTenor');