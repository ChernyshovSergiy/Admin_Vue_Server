<?php
use app\Http\Controllers\api\Admin\LanguageController;
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
Route::prefix('v1')->group(function () {
    // Admin
    Route::group(['middleware' => ['guest:api']], function() {
        Route::post('login', 'Auth\LoginController@login');
        Route::post('login/refresh', 'Auth\LoginController@refresh');

        Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
        Route::post('password/reset', 'Auth\ResetPasswordController@reset');

        Route::post('register', 'Auth\RegisterController@register');
    });

    Route::group(['middleware' => ['jwt', 'cLang']], function() {
        Route::group(['namespace' => 'Auth'], function (){
            Route::post('logout', 'LoginController@logout');
            Route::get('me', 'LoginController@me');
        });
        Route::put('profile', 'ProfileController@update');
        Route::group(['namespace' => 'Admin'], static function (){
            Route::apiResource('language', 'LanguageController');
            Route::get('languages', 'LanguageController@actives');
            Route::get('statuses/{cLang}', 'StatusController@index');
            Route::apiResource('status', 'StatusController')->except(['index']);
            Route::get('contents/{cLang}', 'ContentController@index');
            Route::apiResource('content', 'ContentController')->except(['index']);
            Route::get('menus/{cLang}', 'MenuController@index');
            Route::apiResource('menu', 'MenuController')->except(['index']);
            Route::get('modelings/{cLang}', 'ModelingOrderController@index');
            Route::apiResource('modeling', 'ModelingOrderController')->except(['index']);
            Route::get('executors/{status}', 'ExecutorController@index');
            Route::apiResource('executor', 'ExecutorController')->except(['index']);
            Route::get('printings/{cLang}', 'PrintingOrderController@index');
            Route::apiResource('printing', 'PrintingOrderController')->except(['index']);
            Route::get('/countries/{cLang}', 'CountryListController@index');
            Route::get('sizes/{cLang}', 'SizeController@index');
        });

    });

//    // Site
//    Route::group(['prefix' => '/auth', ['middleware' => 'throttle:20,5']], function(){
//        Route::post('/register', 'Auth\RegisterController@site_register');
//        Route::post('/login', 'Auth\LoginController@site_login');
//        // Send reset password mail
//        Route::post('reset-password', 'AuthController@sendPasswordResetLink');
//        // handle reset password form process
//        Route::post('reset/password', 'AuthController@callResetPassword');
//    });
//
//    Route::group(['middleware' => 'jwt.auth'], function () {
//        Route::get('/user', 'MeController@index');
//        Route::get('/auth/logout', 'MeController@logout');
//    });
//
//    Route::group(['prefix' => '/page', ['middleware' => 'throttle:20,5']], function () {
//        Route::post('/contents', 'Pages\ContentPageController@content');
//        Route::post('/menus', 'Pages\MenuPageController@menu');
//        Route::get('/languages', 'Pages\languagesController@index');
//    });
//
//    Route::group(['prefix' => '/order', ['middleware' => 'throttle:20,5']], function () {
//        Route::post('/modeling', 'Orders\ModelingOrderController@store');
//        Route::get('/modeling/verify/{token}', 'Orders\ModelingOrderController@verify')->name('modelingOrder.verify');
//        Route::post('/countries', 'Orders\CountryListController@index');
//        Route::post('/masks', 'Orders\CountryListController@mask');
//        Route::post('/printing', 'Orders\PrintingOrderController@store');
//        Route::get('/printing/verify/{token}', 'Orders\PrintingOrderController@verify')->name('printingOrder.verify');
////        Route::post('/painting', 'Orders\PaintingOrderController@store');
//    });
});

