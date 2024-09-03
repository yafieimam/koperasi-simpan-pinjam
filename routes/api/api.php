<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/**
 * Route for Guest
 */
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'Auth\AuthController@login');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::post('me', 'Auth\AuthController@me');
});

Route::post('plafon', 'RumusController@plafon');
Route::get('regions', 'ApiController@regions');
Route::get('projects', 'ApiController@projects');
Route::get('locations', 'ApiController@locations');
Route::get('global-policy/{id}', 'ApiController@policy');
Route::get('policy/{id}', 'ApiController@getPolicy');
Route::get('testcron', 'ApiController@crontest');
Route::get('testone', 'ApiController@postOnesignal');
Route::get('position', 'ApiController@getJabatan');
Route::post('register', 'ApiController@register');

Route::group(['prefix' => 'account-setting'], function (){
    Route::get('/password', 'ApiController@password');
    Route::put('/password/update', 'ApiController@updatePassword');

    Route::get('/email', 'ApiController@email');
    Route::put('/email/update', 'ApiController@updateEmail');
});


Route::group(['prefix' => 'member'], function () {
	Route::post('register', 'ApiController@register');
});
Route::get('testauth', 'RumusController@testauth');

Route::get('profile-member/{id}', 'ApiController@getProfile');
Route::get('getlocation', 'ApiController@getlocation');
Route::post('reset-password', 'ApiController@sendResetLinkResponse');

/**
 * Route Block for authenticated User and Registered Member
 */
Route::middleware(['auth:api','auth.member'])->group(function () {
	Route::prefix('auth')->group(function () {
		Route::get('logout', 'Auth\AuthController@logout');
	});

    Route::get('member-only', function () {
        return 'only-member';
	});
});

//Route::post('get-data-main', 'ApiController@getDataDashboard');

Route::middleware(['auth:api'])->group(function () {
    Route::post('get-data-main', 'ApiController@getDataDashboard');
    Route::post('change-avatar', 'ApiController@changeAvatar');
    Route::get('check', 'Auth\AuthController@check');
    Route::get('news', 'ApiController@news');
    Route::get('slider-news', 'ApiController@sliderNews');
    Route::get('deposit', 'ApiController@getDeposit');
    Route::get('my-deposit-detail/{id}', 'ApiController@myDetailDeposit');
    Route::post('filter-deposit/{id}', 'ApiController@filterDeposit');

    Route::get('loan', 'ApiController@getLoan');
    Route::get('my-loan', 'ApiController@myLoan');
    Route::get('list-approval/{loan_id}', 'ApiController@listApproval');
    Route::get('sisa-loan/{id}/{loan_id}', 'ApiController@sisaLoan');
    Route::get('sisa-loan-reschedule/{id}', 'ApiController@sisaLoanReschedule');
    Route::get('my-loan-detail/{id}', 'ApiController@myDetailLoan');
    Route::post('filter-loan', 'ApiController@filterLoan');
    Route::post('post-contact', 'ApiController@postContact');
    Route::post('post-loan', 'ApiController@postLoan');
    Route::post('post-reschedule', 'ApiController@postReschedule');
    Route::get('get-loan-approval', 'ApiController@getLoanApproval');
    Route::post('approve-loan', 'ApiController@approveLoan');
    Route::post('approval-loan', 'ApiController@approvalLoan');
    Route::get('get-top-loan', 'ApiController@getTopLoan');
    Route::get('get-penjamin-loan', 'ApiController@penjaminLoan');
    Route::get('get-bunga-berjalan', 'ApiController@getBungaBerjalan');
    Route::get('rincian-simpanan', 'ApiController@getRincianSimpanan');
    Route::post('update-revisi-loan', 'ApiController@updateRevisiLoan');
    Route::get('check-reschedule', 'ApiController@checkReschedule');
    Route::get('list-unpaid-loan', 'ApiController@listUnpaidLoan');


    Route::post('change-status-loan', 'ApiController@changeStatusLoan');
    Route::post('update-status-loan', 'ApiController@updateStatusLoan');

    Route::get('my-deposit', 'ApiController@myDeposit');
    Route::get('my-profile', 'ApiController@myProfile');

    Route::post('post-resign', 'ApiController@postResign');
    Route::get('list-resign', 'ApiController@listResign');
    Route::post('approve-resign', 'ApiController@approveResign');
    Route::post('get-status-resign', 'ApiController@getStatusResign');
    Route::post('post-retrieve-deposit', 'ApiController@postRetrieveDeposit');
    Route::post('ajukan-perubahan-simpanan ', 'ApiController@ajukanPerubahanSimpanan');

    Route::get('policy/{id}', 'ApiController@policy');
    Route::get('qna', 'ApiController@getQna');
    Route::get('my-bank', 'ApiController@myBank');
    Route::post('update-my-bank', 'ApiController@updateMyBank');

    Route::get('notifications', 'ApiController@notifications');
    Route::get('read-notification/{id}', 'ApiController@markAsReadNotification');
    Route::get('read-all-notification', 'ApiController@markAllAsReadNotification');
    Route::get('count-notification', 'ApiController@countNotification');
    Route::get('delete-notification/{id}', 'ApiController@deleteNotification');

    Route::put('update-salary', 'ApiController@updateSalary');
    Route::put('update-bank', 'ApiController@updateBank');

    Route::post('list-pengambilan-simpanan/get-status', 'ApiController@getListPencairan');
    Route::get('list-pengambilan-simpanan/member/{id}', 'ApiController@list_pengambilan_simpanan_member');

    Route::post('change-member-deposits/form', 'ApiController@post_change_deposit');
});
Route::post('shu', 'RumusController@shu');

Route::get('article/{id}/image', '\App\Http\Controllers\ArticleController@getImage');
