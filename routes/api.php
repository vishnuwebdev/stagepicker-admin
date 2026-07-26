<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\NotificationController;


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



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::any('/signupsendotp', 'App\Http\Controllers\api\UserController@signupsendotp')->name('signupsendotp');

Route::any('/signup', 'App\Http\Controllers\api\UserController@signup')->name('signup');
Route::any('/signupresendotp', 'App\Http\Controllers\api\UserController@signupResendotp')->name('signupresendotp');
Route::any('/verifyAccount', 'App\Http\Controllers\api\UserController@verifyAccount')->name('verifyAccount');
Route::any('/login', 'App\Http\Controllers\api\UserController@login')->name('login');
Route::any('/forgotpassword', 'App\Http\Controllers\api\UserController@ForgotPassword')->name('forgotpassword');
Route::any('/forgetresendotp', 'App\Http\Controllers\api\UserController@forgetResendotp')->name('forgetresendotp');
Route::any('/verifyforgototp', 'App\Http\Controllers\api\UserController@verifyForgotOtp')->name('verifyforgototp');
Route::any('/updateforgotpassword', 'App\Http\Controllers\api\UserController@updateForgotPassword')->name('updateForgotPassword');


Route::any('/updatepassword', 'App\Http\Controllers\api\UserController@updatepassword')->name('updatepassword');

//company Verification 

Route::any('/company-verifications', 'App\Http\Controllers\api\UserController@companyVerifications')->name('company-verifications');
Route::any('/verifications-data', 'App\Http\Controllers\api\UserController@verificationData')->name('verifications-data');

Route::any('/contact-request', 'App\Http\Controllers\api\UserController@contactRequest')->name('contact-request');

Route::any('/changepassword', 'App\Http\Controllers\api\UserController@changepassword')->name('changepassword');
Route::any('/profile', 'App\Http\Controllers\api\UserController@profile')->name('profile');
Route::any('/updateprofile', 'App\Http\Controllers\api\UserController@updateProfile')->name('updateprofile');
Route::any('social-login', 'App\Http\Controllers\api\UserController@SocialMediaLogin')->name('social-login');
Route::any('/notification', 'App\Http\Controllers\api\UserController@notification')->name('notification');


//Portfolio
Route::any('/getPortfolio', 'App\Http\Controllers\api\UserController@getPortfolio')->name('getPortfolio');
Route::any('/update-portfolio', 'App\Http\Controllers\api\UserController@updatePortfolio')->name('update-portfolio');
Route::any('/delete-portfolio', 'App\Http\Controllers\api\UserController@deletePortfolio')->name('delete-portfolio');
Route::any('/delete-account', 'App\Http\Controllers\api\UserController@deleteAccount')->name('deleteAccount');




//terms condition how to use
Route::any('/stageinfo', 'App\Http\Controllers\api\UserController@stageinfo')->name('stageinfo');

Route::any('/getFCMToken', 'App\Http\Controllers\api\UserController@getFCMToken')->name('getFCMToken');

Route::any('/audition-detail', 'App\Http\Controllers\api\PostauditionController@auditionDetail')->name('auditionDetail');

Route::any('/dashboarddata', 'App\Http\Controllers\api\PostauditionController@dashboardData')->name('dashboarddata');

Route::any('/has-role-applied', 'App\Http\Controllers\api\PostauditionController@hasAppliedForRole')->name('hasAppliedForRole');

//post audition
Route::any('/searchdata', 'App\Http\Controllers\api\PostauditionController@searchData')->name('searchdata');
Route::any('/add-postauditiondata', 'App\Http\Controllers\api\PostauditionController@addPostauditionData')->name('add-postauditiondata');
Route::any('/add-postaudition', 'App\Http\Controllers\api\PostauditionController@addPostaudition')->name('add-postaudition');
Route::any('/postauditionlist', 'App\Http\Controllers\api\PostauditionController@list')->name('postauditionlist');
// producerAuditionDetail
Route::any('/producerAuditionDetail', 'App\Http\Controllers\api\PostauditionController@producerAuditionDetail')->name('producerAuditionDetail');
Route::any('/updatepostaudition', 'App\Http\Controllers\api\PostauditionController@updatePostaudition')->name('updatepostaudition');
Route::any('/auditionfeedlist', 'App\Http\Controllers\api\PostauditionController@auditionfeedlist')->name('auditionfeedlist');
Route::any('/actormodelfeed', 'App\Http\Controllers\api\PostauditionController@actormodelfeed')->name('actormodelfeed');
Route::any('/postauditionapply', 'App\Http\Controllers\api\PostauditionController@postAuditionApply')->name('postauditionapply');
Route::any('/postauditionappliedlist', 'App\Http\Controllers\api\PostauditionController@postauditionappliedlist')->name('postauditionappliedlist');
Route::any('/postauditionparticipant', 'App\Http\Controllers\api\PostauditionController@auditionParticipants')->name('postauditionparticipant');

Route::any('/selectParticipant', 'App\Http\Controllers\api\PostauditionController@selectParticipant')->name('selectParticipant');

Route::any('/deSelectParticipant', 'App\Http\Controllers\api\PostauditionController@deSelectParticipant')->name('deSelectParticipant');

Route::any('/deleteaudiotion', 'App\Http\Controllers\api\PostauditionController@deleteaudiotion')->name('deleteaudiotion');



//photography audition

Route::any('/photographylist', 'App\Http\Controllers\api\PostauditionController@photographylist')->name('photographylist');
Route::any('/photographysearch', 'App\Http\Controllers\api\PostauditionController@photographysearch')->name('photographysearch');
Route::any('/external-casting-list', 'App\Http\Controllers\api\PostauditionController@external_casting_list')->name('external_casting_list');






Route::any('/subscription', 'App\Http\Controllers\api\UserController@subscription')->name('subscription');

Route::any('/addstripeammount', 'App\Http\Controllers\api\UserController@addstripeammount')->name('addstripeammount');

Route::any('/add-role', 'App\Http\Controllers\api\UserController@addRole')->name('add-role');
Route::any('/roletype', 'App\Http\Controllers\api\UserController@roletype')->name('roletype');

Route::any('/audiroletype', 'App\Http\Controllers\api\UserController@audiroletype')->name('audiroletype');


Route::any('/add-photography', 'App\Http\Controllers\api\PostauditionController@addPhotography')->name('add-photography');


Route::any('/update-photography', 'App\Http\Controllers\api\PostauditionController@updatePhotography')->name('update-photography');

Route::any('/producer-dashboard', 'App\Http\Controllers\api\PostauditionController@producerDashboard')->name('producer-dashboard');

Route::any('/producer-archive-posts', 'App\Http\Controllers\api\PostauditionController@archivePosts')->name('producer-archive-posts');

Route::any('/sliders', 'App\Http\Controllers\api\PostauditionController@sliders')->name('sliders');

Route::any('/analytics', 'App\Http\Controllers\api\PostauditionController@analytics')->name('analytics');
Route::any('/audition-viewed', 'App\Http\Controllers\api\PostauditionController@postAuditionViewed')->name('audition-viewed');

Route::any('/favourite-add', 'App\Http\Controllers\api\PostauditionController@favouriteAdd')->name('favourite-add');

Route::any('/favourite-remove', 'App\Http\Controllers\api\PostauditionController@favouriteRemove')->name('favourite-remove');

Route::any('/myfavourite-list', 'App\Http\Controllers\api\PostauditionController@favouriteList')->name('myfavourite-list');
 
Route::any('/add-production-crew', 'App\Http\Controllers\api\PostauditionController@addProductionCrew')->name('add-production-crew');

Route::any('/update-production-crew', 'App\Http\Controllers\api\PostauditionController@updateProductionCrew')->name('update-production-crew');

Route::any('/production-crewlist', 'App\Http\Controllers\api\PostauditionController@productioncrewlist')->name('production-crewlist');

Route::any('/delete-production-crew', 'App\Http\Controllers\api\PostauditionController@deleteProductionCrew')->name('delete-production-crew');
 
Route::any('/report', 'App\Http\Controllers\api\PostauditionController@report')->name('report');
 
Route::any('/productioncrew-appliedlist', 'App\Http\Controllers\api\PostauditionController@productioncrewappliedlist')->name('productioncrew-appliedlist');

Route::any('/productioncrew-participants', 'App\Http\Controllers\api\PostauditionController@crewParticipants')->name('productioncrew-participants');

Route::any('/crew-analytics', 'App\Http\Controllers\api\PostauditionController@crewAnalytics')->name('crew-analytics');

//new route//
Route::any('/get-data', 'App\Http\Controllers\api\ClassController@get_data')->name('get-data');
Route::any('/get-class-details', 'App\Http\Controllers\api\ClassController@get_class_details')->name('get-class-details');
Route::any('/get-webinar-details', 'App\Http\Controllers\api\ClassController@get_webinar_details')->name('get-webinar-details');
Route::any('/get-merchandise-detail', 'App\Http\Controllers\api\ClassController@get_merchandise_details')->name('get-merchandise-detail');

//marketplace order management (Merchandise checkout / order history / returns)
Route::any('/create-order', 'App\Http\Controllers\api\OrderController@create_order')->name('create-order');
Route::any('/get-orders', 'App\Http\Controllers\api\OrderController@get_orders')->name('get-orders');
Route::any('/get-order-detail', 'App\Http\Controllers\api\OrderController@get_order_detail')->name('get-order-detail');
Route::any('/request-order-return', 'App\Http\Controllers\api\OrderController@request_order_return')->name('request-order-return');
Route::any('/order-invoice', 'App\Http\Controllers\api\OrderController@order_invoice')->name('order-invoice');

//credit management

Route::any('/update-credit', 'App\Http\Controllers\api\ClassController@updateCredit')->name('updateCredit');
Route::any('/add-credit', 'App\Http\Controllers\api\ClassController@addCredit')->name('add-credit');
Route::any('/get-credit', 'App\Http\Controllers\api\ClassController@get_credits')->name('get-credit');
Route::any('/delete-credit', 'App\Http\Controllers\api\ClassController@delete_credits')->name('delete-credit');
Route::any('/get-emoji', 'App\Http\Controllers\api\ClassController@get_emoji')->name('get-emoji');

//credit management
//Route::any('/update-credit', 'App\Http\Controllers\api\ClassController@updateCredit')->name('updateCredit');
Route::any('/add-compcard', 'App\Http\Controllers\api\ClassController@add_compcard')->name('add-compcard');
Route::any('/get-compcard', 'App\Http\Controllers\api\ClassController@get_compcard')->name('get-compcard');
Route::any('/delete-compcard', 'App\Http\Controllers\api\ClassController@delete_compcard')->name('delete-compcard');
//Route::any('/get-emoji', 'App\Http\Controllers\api\ClassController@get_emoji')->name('get-emoji');

//external casting 
Route::any('/get-ext-casting', 'App\Http\Controllers\api\ClassController@get_ext_casting')->name('get-ext-casting');
Route::any('/get-ext-casting-search', 'App\Http\Controllers\api\ClassController@get_ext_casting_search')->name('get-ext-casting-search');
Route::any('/get-ext-casting-applied', 'App\Http\Controllers\api\ClassController@get_ext_casting_applied')->name('get-ext-casting-applied');

//verification api

Route::any('/save-standard-verification', 'App\Http\Controllers\api\ClassController@save_standard_verification')->name('save-standard-verification');

Route::post('/standard-verification', 'App\Http\Controllers\api\ClassController@getStandardVerification')->name('standard-verification');

Route::any('/save-company-verification', 'App\Http\Controllers\api\ClassController@save_company_verification')->name('save-company-verification');

Route::any('/company-verification', 'App\Http\Controllers\api\ClassController@getCompanyVerification')->name('company-verification');



Route::any('/check-verification', 'App\Http\Controllers\api\ClassController@check_verification')->name('check-verification');

Route::get('/run-notifications', function () {
    \Artisan::call('notifications:send-daily');
    // 2. Get the text output that usually goes to the terminal
    $output = Artisan::output();

    // 3. Return it to the browser formatted for readability
    return response("<pre>$output</pre>");
    // return 'Notification command executed';
});

Route::post('/notifications', [NotificationController::class, 'store']);
Route::get('/notifications/{user_id}', [NotificationController::class, 'index']);
Route::post('/notifications/{id}', [NotificationController::class, 'markAsRead']);