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
    Route::group(['prefix' => '/order', ['middleware' => 'throttle:20,5']], function () {
        Route::group(['namespace' => 'Admin'], static function (){
            Route::post('/masks', 'CountryListController@mask');
        });
    });

    // Site
    Route::group(['prefix' => '/auth', ['middleware' => 'throttle:20,5']], function(){
        Route::group(['namespace' => 'Auth\Front'], static function () {
            Route::post('/register', 'RegisterController@register');
            Route::post('/login', 'LoginController@login');
            // Send reset password mail
            Route::post('reset-password', 'AuthController@sendPasswordResetLink');
            // handle reset password form process
            Route::post('reset/password', 'AuthController@callResetPassword');
        });

    });

    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::group(['namespace' => 'Auth\Front'], static function () {
            Route::get('/user', 'MeController@index');
            Route::get('/auth/logout', 'MeController@logout');
        });
    });

    Route::group(['prefix' => '/page', ['middleware' => 'throttle:20,5']], function () {
        Route::group(['namespace' => 'Pages'], static function () {
            Route::post('/contents', 'ContentPageController@content');
            Route::post('/menus', 'MenuPageController@menu');
            Route::get('/languages', 'languagesController@index');
        });
    });

    Route::group(['prefix' => '/order', ['middleware' => 'throttle:20,5']], function () {
        Route::group(['namespace' => 'Orders'], static function () {
            Route::post('/modeling', 'ModelingOrderController@store');
            Route::get('/modeling/verify/{token}', 'ModelingOrderController@verify')->name('modelingOrder.verify');
            Route::get('/countries/{cLang}', 'CountryListController@index');
            Route::post('/masks', 'CountryListController@mask');
            Route::post('/printing', 'PrintingOrderController@store');
            Route::get('/printing/verify/{token}', 'PrintingOrderController@verify')->name('printingOrder.verify');
        //        Route::post('/painting', 'PaintingOrderController@store');
        });
    });
});

