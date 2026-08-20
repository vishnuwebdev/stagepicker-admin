<?php

use Illuminate\Support\Facades\Route;

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
Route::any('checkexpireaudition', 'App\Http\Controllers\AuditionExpireCheckController@checkexpireaudition')->name('checkexpireaudition');
Route::any('notifybeforeexpire', 'App\Http\Controllers\AuditionExpireCheckController@notifybeforeexpire')->name('notifybeforeexpire');

Route::any('terms-condition', 'App\Http\Controllers\HomeController@terms')->name('terms-condition');
Route::any('privacy-policy', 'App\Http\Controllers\HomeController@privacy')->name('privacy-policy');
Route::any('firestore-test', 'App\Http\Controllers\HomeController@firestoretest')->name('firestore-test');

Route::group(['middleware' => 'auth'] , function() {

    // $this->middleware

    // admin user management

    Route::get('admin/adminuser/list', 'App\Http\Controllers\admin\AdminUserController@list')->name('adminuser/list');
    Route::get('admin/adminuser/add', 'App\Http\Controllers\admin\AdminUserController@add')->name('admin/adminuser/add');
    Route::any('admin/adminuser/store', 'App\Http\Controllers\admin\AdminUserController@store')->name('admin/adminuser/store');
    Route::any('admin/adminuser/delete/{key}', 'App\Http\Controllers\admin\AdminUserController@delete')->name('admin/adminuser/delete');
    Route::any('admin/adminuser/edit/{key}', 'App\Http\Controllers\admin\AdminUserController@edit')->name('admin/adminuser/edit');
    Route::any('admin/adminuser/update', 'App\Http\Controllers\admin\AdminUserController@update')->name('admin/adminuser/update');

    // Admin User Management
    Route::get('admin/users', 'App\Http\Controllers\admin\UserController@list')->name('admin/users');
    Route::any('admin/userdetail/{key}', 'App\Http\Controllers\admin\UserController@userDetail')->name('admin/userdetail');
    Route::any('admin/userdelete/{key}', 'App\Http\Controllers\admin\UserController@delete')->name('admin/userdelete');
    Route::get('admin/user/download', 'App\Http\Controllers\admin\UserController@download')->name('admin/user/download');
    Route::any('admin/usersuspend', 'App\Http\Controllers\admin\UserController@suspend')->name('admin/usersuspend');
	
	Route::any('admin/useredit/{key}', 'App\Http\Controllers\admin\UserController@edit')->name('admin/useredit');
	 
	 Route::any('admin/editUser', 'App\Http\Controllers\admin\UserController@editUser')->name('admin/editUser');
    
    Route::any('admin/company-verification', 'App\Http\Controllers\admin\UserController@companyVerifications')->name('admin/company-verification');
    Route::any('admin/edit-verification/{key}', 'App\Http\Controllers\admin\UserController@editVerification')->name('admin/edit-verification');
    Route::any('admin/view-verification/{key}', 'App\Http\Controllers\admin\UserController@viewVerification')->name('admin/view-verification');
    Route::any('admin/update-verification-data', 'App\Http\Controllers\admin\UserController@updateVerificationData')->name('admin/update-verification-data');
    Route::any('admin/update-verification/{key}', 'App\Http\Controllers\admin\UserController@updateVerification')->name('admin/update-verification');

    
    
    // Admin Profile Management
    Route::get('admin/dashboard', 'App\Http\Controllers\admin\HomeController@dashboard')->name('admin/dashboard');
    Route::get('admin/profile', 'App\Http\Controllers\admin\HomeController@profile')->name('admin/profile');
    Route::get('admin/changepassword', 'App\Http\Controllers\admin\HomeController@changepassword')->name('admin/changepassword');
    Route::get('admin/updatepass', 'App\Http\Controllers\admin\HomeController@updatepass')->name('admin/updatepass');
    Route::get('admin/updateprofile', 'App\Http\Controllers\admin\HomeController@updateprofile')->name('admin/updateprofile');
    
    // Admin Category Management
    Route::get('admin/category', 'App\Http\Controllers\admin\CategoryController@list')->name('admin/category');
    Route::get('admin/add-category', 'App\Http\Controllers\admin\CategoryController@add')->name('admin/add-category');
    Route::post('admin/categories/store', 'App\Http\Controllers\admin\CategoryController@store')->name('admin/categories/store');
    Route::any('admin/edit-category/{key}', 'App\Http\Controllers\admin\CategoryController@edit')->name('admin/edit-category');
    Route::any('admin/update-category/{key}', 'App\Http\Controllers\admin\CategoryController@update')->name('admin/update-category');
    Route::any('admin/category-delete/{id}', 'App\Http\Controllers\admin\CategoryController@delete')->name('admin/category-delete'); 

    // Admin Skill Management
    Route::get('admin/skill', 'App\Http\Controllers\admin\SkillController@skillList')->name('admin/skill');
    Route::get('admin/add-skill', 'App\Http\Controllers\admin\SkillController@add')->name('admin/add-skill');
    Route::post('admin/skill/store', 'App\Http\Controllers\admin\SkillController@store')->name('admin/skill/store');
    Route::any('admin/edit-skill/{key}', 'App\Http\Controllers\admin\SkillController@edit')->name('admin/edit-skill');
    Route::any('admin/update-skill/{key}', 'App\Http\Controllers\admin\SkillController@update')->name('admin/update-skill');
    Route::any('admin/skill-delete/{id}', 'App\Http\Controllers\admin\SkillController@delete')->name('admin/skill-delete');
    
    // Admin Roll Management
    Route::get('admin/role', 'App\Http\Controllers\admin\RoleController@list')->name('admin/role');
    Route::get('admin/add-role', 'App\Http\Controllers\admin\RoleController@add')->name('admin/add-skill');
    Route::post('admin/role/store', 'App\Http\Controllers\admin\RoleController@store')->name('admin/role/store');
    Route::any('admin/edit-role/{key}', 'App\Http\Controllers\admin\RoleController@edit')->name('admin/edit-role');
    Route::any('admin/update-role/{key}', 'App\Http\Controllers\admin\RoleController@update')->name('admin/update-role');
    Route::any('admin/role-delete/{id}', 'App\Http\Controllers\admin\RoleController@delete')->name('admin/role-delete');
    
    
	//classes Management   
    Route::get('admin/classes', 'App\Http\Controllers\admin\ClassController@list')->name('admin/classes'); 
    Route::get('admin/add-classes', 'App\Http\Controllers\admin\ClassController@add_classes')->name('admin/add_classes');
    Route::post('admin/store_classes', 'App\Http\Controllers\admin\ClassController@store_classes')->name('admin/store_classes');  
    Route::any('admin/view-classes/{id}', 'App\Http\Controllers\admin\ClassController@view_classes')->name('admin/view_classes');  
    Route::get('admin/edit-classes/{id}', 'App\Http\Controllers\admin\ClassController@edit_classes')->name('admin/edit_classes');  
    Route::post('admin/update-classes/{id}', 'App\Http\Controllers\admin\ClassController@update_classes')->name('admin/update_classes');  
    Route::any('admin/delete-classes/{id}', 'App\Http\Controllers\admin\ClassController@delete_classes')->name('admin/delete_classes');  
    
     //Webinars Management add-webinars delete_
    Route::get('admin/webinars', 'App\Http\Controllers\admin\WebinarsController@list')->name('admin/webinars'); 
    Route::get('admin/add-webinars', 'App\Http\Controllers\admin\WebinarsController@add')->name('admin/add_webinars'); 
    Route::post('admin/store-weinars', 'App\Http\Controllers\admin\WebinarsController@store')->name('admin/store_weinars'); 
    Route::any('admin/view-webinar/{id}', 'App\Http\Controllers\admin\WebinarsController@view')->name('admin/view_webinar');  
    Route::get('admin/edit-webinar/{id}', 'App\Http\Controllers\admin\WebinarsController@edit')->name('admin/edit_webinar');  
    Route::post('admin/update-webinar/{id}', 'App\Http\Controllers\admin\WebinarsController@update')->name('admin/update_webinar');  
    Route::any('admin/delete-webinar/{id}', 'App\Http\Controllers\admin\WebinarsController@delete')->name('admin/delete_webinar');

    //Merchandise Management
    Route::get('admin/merchandise', 'App\Http\Controllers\admin\MerchandiseController@list')->name('admin/merchandise');
    Route::get('admin/add-merchandise', 'App\Http\Controllers\admin\MerchandiseController@add')->name('admin/add_merchandise');
    Route::post('admin/store-merchandise', 'App\Http\Controllers\admin\MerchandiseController@store')->name('admin/store_merchandise');
    Route::any('admin/view-merchandise/{id}', 'App\Http\Controllers\admin\MerchandiseController@view')->name('admin/view_merchandise');
    Route::get('admin/edit-merchandise/{id}', 'App\Http\Controllers\admin\MerchandiseController@edit')->name('admin/edit_merchandise');
    Route::post('admin/update-merchandise/{id}', 'App\Http\Controllers\admin\MerchandiseController@update')->name('admin/update_merchandise');
    Route::any('admin/delete-merchandise/{id}', 'App\Http\Controllers\admin\MerchandiseController@delete')->name('admin/delete_merchandise');
    Route::any('admin/delete-merchandise-image/{id}', 'App\Http\Controllers\admin\MerchandiseController@deleteImage')->name('admin/delete_merchandise_image');

    //Orders Management (Merchandise checkout / order history / returns)
    Route::get('admin/orders', 'App\Http\Controllers\admin\OrderController@list')->name('admin/orders');
    Route::any('admin/view-order/{id}', 'App\Http\Controllers\admin\OrderController@view')->name('admin/view_order');
    Route::post('admin/update-order-status/{id}', 'App\Http\Controllers\admin\OrderController@updateStatus')->name('admin/update_order_status');

    //Bookings Management (Class enrollment / attendee lists — cancelling
    //here is bookkeeping only, refunds are handled in the Stripe dashboard)
    Route::get('admin/bookings', 'App\Http\Controllers\admin\BookingController@list')->name('admin/bookings');
    Route::get('admin/bookings/class/{classId}/attendees', 'App\Http\Controllers\admin\BookingController@attendees')->name('admin/bookings/attendees');
    Route::post('admin/bookings/{id}/status', 'App\Http\Controllers\admin\BookingController@updateStatus')->name('admin/bookings/status');
    Route::get('admin/bookings/{id}', 'App\Http\Controllers\admin\BookingController@view')->name('admin/bookings/view');

    //Payment Transactions (Stripe attempt history — read-only, refunds are
    //handled directly in the Stripe dashboard, see PaymentTransactionController)
    Route::get('admin/payment-transactions', 'App\Http\Controllers\admin\PaymentTransactionController@list')->name('admin/payment-transactions');
    Route::get('admin/payment-transactions/user/{userId}', 'App\Http\Controllers\admin\PaymentTransactionController@userHistory')->name('admin/payment-transactions/user');
    Route::post('admin/payment-transactions/{id}/refresh', 'App\Http\Controllers\admin\PaymentTransactionController@refreshStatus')->name('admin/payment-transactions/refresh');
    Route::get('admin/payment-transactions/{id}', 'App\Http\Controllers\admin\PaymentTransactionController@view')->name('admin/payment-transactions/view');

    //Route::get('admin/add-subscription', 'App\Http\Controllers\admin\SubscriptionController@add')->name('admin/add-subscription');
    //Route::post('admin/subscription/store', 'App\Http\Controllers\admin\SubscriptionController@store')->name('admin/subscription/store');
    //Route::any('admin/edit-subscription/{key}', 'App\Http\Controllers\admin\SubscriptionController@edit')->name('admin/edit-subscription');
    //Route::any('admin/update-subscription/{key}', 'App\Http\Controllers\admin\SubscriptionController@update')->name('admin/update-subscription');
    //Route::any('admin/subscription-delete/{id}', 'App\Http\Controllers\admin\SubscriptionController@delete')->name('admin/subscription-delete');


    // Subscription Management
    Route::get('admin/subscription', 'App\Http\Controllers\admin\SubscriptionController@subscriptionlist')->name('admin/subscription');
    Route::get('admin/add-subscription', 'App\Http\Controllers\admin\SubscriptionController@add')->name('admin/add-subscription');
    Route::post('admin/subscription/store', 'App\Http\Controllers\admin\SubscriptionController@store')->name('admin/subscription/store');
    Route::any('admin/edit-subscription/{key}', 'App\Http\Controllers\admin\SubscriptionController@edit')->name('admin/edit-subscription');
    Route::any('admin/update-subscription/{key}', 'App\Http\Controllers\admin\SubscriptionController@update')->name('admin/update-subscription');
    Route::any('admin/subscription-delete/{id}', 'App\Http\Controllers\admin\SubscriptionController@delete')->name('admin/subscription-delete');

    // Admin Post Audition Management
    Route::get('admin/postaudition', 'App\Http\Controllers\admin\PostauditionController@list')->name('admin/postaudition');
    Route::post('admin/update-audition-status', 'App\Http\Controllers\admin\PostauditionController@updateStatus')->name('admin/postauditionstatusupdate');
    Route::any('admin/auditiondetail/{key}', 'App\Http\Controllers\admin\PostauditionController@detail')->name('admin/auditiondetail');
    Route::get('admin/postaudition/edit/{key}', 'App\Http\Controllers\admin\PostauditionController@edit')->name('admin/edit-postaudition');
    Route::any('admin/editAudition', 'App\Http\Controllers\admin\PostauditionController@editAudition')->name('admin/editAudition');
	
	// Admin Productio Crew Management
    Route::get('admin/productioncrew', 'App\Http\Controllers\admin\PostauditionController@productioncrewlist')->name('admin/productioncrew');
    Route::any('admin/crewdetail/{key}', 'App\Http\Controllers\admin\PostauditionController@crewdetail')->name('admin/crewdetail');
	
	Route::any('admin/production-post-crew', 'App\Http\Controllers\admin\PostauditionController@auditionProdCrew')->name('production-post-crew');
	
	 
    // Admin Roll Management
    Route::get('admin/crewrole', 'App\Http\Controllers\admin\CrewRoleController@list')->name('admin/role');
    Route::get('admin/add-crewrole', 'App\Http\Controllers\admin\CrewRoleController@add')->name('admin/add-skill');
    Route::post('admin/crewrole/store', 'App\Http\Controllers\admin\CrewRoleController@store')->name('admin/crewrole/store');
    Route::any('admin/edit-crewrole/{key}', 'App\Http\Controllers\admin\CrewRoleController@edit')->name('admin/edit-crewrole');
    Route::any('admin/update-crewrole/{key}', 'App\Http\Controllers\admin\CrewRoleController@update')->name('admin/update-crewrole');
    Route::any('admin/crewrole-delete/{id}', 'App\Http\Controllers\admin\CrewRoleController@delete')->name('admin/crewrole-delete');
	
    
    // Admin  Photography Management
    Route::get('admin/photography', 'App\Http\Controllers\admin\PostauditionController@photographylist')->name('admin/photography');
    Route::any('admin/photographydetail/{key}', 'App\Http\Controllers\admin\PostauditionController@detail')->name('admin/auditiondetail');

    // Admin CMS
    Route::get('admin/cms', 'App\Http\Controllers\admin\HomeController@cmslist')->name('admin/cms');
    Route::any('admin/edit-cms/{key}', 'App\Http\Controllers\admin\HomeController@editpage')->name('admin/edit-cms');
    Route::any('admin/update-page/{key}', 'App\Http\Controllers\admin\HomeController@updatePage')->name('admin/update-page');
	
	
	// Admin RoleType
    Route::any('admin/edit-roletype/{key}', 'App\Http\Controllers\admin\RoleController@editRoletype')->name('admin/edit-cms');
    Route::any('admin/update-roletype/{key}', 'App\Http\Controllers\admin\RoleController@updateRoletype')->name('admin/update-roletype');
	
	Route::get('admin/roletype', 'App\Http\Controllers\admin\RoleController@roletype')->name('admin/cms');
    Route::get('admin/add-roletype', 'App\Http\Controllers\admin\RoleController@addroletype')->name('admin/add-roletype');
    Route::post('admin/role/storeroletype', 'App\Http\Controllers\admin\RoleController@storeroletype')->name('admin/role/storeroletype');
    Route::any('admin/edit-roletype/{key}', 'App\Http\Controllers\admin\RoleController@editroletype')->name('admin/edit-roletype');
    Route::any('admin/update-roletype/{key}', 'App\Http\Controllers\admin\RoleController@updateroletype')->name('admin/update-role');
    Route::any('admin/roletype-delete/{id}', 'App\Http\Controllers\admin\RoleController@deleteroletype')->name('admin/roletype-delete');
	
    //Slider Management 
    Route::any('admin/slider', 'App\Http\Controllers\admin\SliderController@sliderList')->name('slider');
    Route::any('admin/slider', 'App\Http\Controllers\admin\SliderController@sliderList')->name('admin/slider');
    Route::any('admin/add-slider', 'App\Http\Controllers\admin\SliderController@addSlider')->name('add-slider');
    Route::any('admin/save-slider', 'App\Http\Controllers\admin\SliderController@saveSlider')->name('admin/save-slider');
    Route::any('admin/edit-slider/{id}', 'App\Http\Controllers\admin\SliderController@editSlider')->name('edit-slider');
    Route::any('admin/update-slider', 'App\Http\Controllers\admin\SliderController@updateSlider')->name('admin/update-slider');
    Route::any('admin/slider-delete/{id}', 'App\Http\Controllers\admin\SliderController@delete')->name('slider-delete');
    
    
    //Contact Request Management 
    Route::any('admin/contactlist', 'App\Http\Controllers\admin\UserController@contactlist')->name('slider');
    
    Route::any('admin/deletecontact/{id}', 'App\Http\Controllers\admin\UserController@deletecontact')->name('deletecontact');
	
	
	Route::any('admin/audition-post-audition', 'App\Http\Controllers\admin\PostauditionController@auditionPostAudition')->name('audition-post-audition');
	
	
	Route::any('admin/update-user-status', 'App\Http\Controllers\admin\UserController@updateUserStatus')->name('update-user-status');
	
	
	// Admin career Management
    Route::get('admin/career', 'App\Http\Controllers\admin\CareerController@careerList')->name('admin/career');
    Route::get('admin/add-career', 'App\Http\Controllers\admin\CareerController@add')->name('admin/add-career');
    Route::post('admin/career/store', 'App\Http\Controllers\admin\CareerController@store')->name('admin/career/store');
    Route::any('admin/edit-career/{key}', 'App\Http\Controllers\admin\CareerController@edit')->name('admin/edit-career');
    Route::any('admin/update-career/{key}', 'App\Http\Controllers\admin\CareerController@update')->name('admin/update-career');
    Route::any('admin/career-delete/{id}', 'App\Http\Controllers\admin\CareerController@delete')->name('admin/career-delete');
	
	//push notification
    Route::any('admin/push-notification', 'App\Http\Controllers\admin\UserController@pushNotification')->name('push-notification');
    Route::post('admin/sendpushnotification', 'App\Http\Controllers\admin\UserController@sendpushnotification')->name('sendpushnotification');
	
	
	Route::get('admin/report', 'App\Http\Controllers\admin\PostauditionController@report')->name('admin/report');
	
	Route::get('admin/report-delete/{id}', 'App\Http\Controllers\admin\PostauditionController@reportdelete')->name('admin/report-delete');
	

    
    // Users
    Route::prefix('users')->group(function () {
       
        // Route::get('/profile', function() {
        //     // $category_name = '';
        //     $data = [
        //         'category_name' => 'users',
        //         'page_name' => 'profile',
        //         'has_scrollspy' => 0,
        //         'scrollspy_offset' => '',
        //     ];
        //     // $pageName = 'profile';
        //     return view('pages.users.user_profile')->with($data);
        // });
    });
});

Auth::routes();

Route::get('/', 'HomeController@index');

Route::get('/register', function() {
    return redirect('/login');    
});
Route::get('/password/reset', function() {
    return redirect('/login');    
});

Route::get('/', function() {
    return redirect('/admin/dashboard');    
});