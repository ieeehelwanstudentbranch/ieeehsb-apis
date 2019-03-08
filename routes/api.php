<?php

use Illuminate\Http\Request;

//login & register
Route::POST('login', 'AuthApi\AuthController@login')->name('login');

Route::POST('logout', 'AuthApi\AuthController@logout');

Route::Post('register', 'AuthApi\RegisterController@register');

Route::get('register/verify/{confirmationCode}' ,  'AuthApi\ConfirmController@confirm');

Route::post('/password/reset', 'AuthApi\ResetPasswordController@recover')->name('password.reset');

Route::post('/password/reset/{reset_code}', 'AuthApi\ResetPasswordController@Reset')->name('password.reset');


//Committee
Route::get('/committees', 'CommitteeController@index');

Route::get('/committee/{id}', 'CommitteeController@view');

Route::get('/addcommittee', 'CommitteeController@addPage');

Route::post('/addcommittee', 'CommitteeController@add');

Route::get('/updatecommittee', 'CommitteeController@updatePage');

Route::post('/updatecommittee/{id}', 'CommitteeController@update');

Route::delete('/deletecommittee/{id}', 'CommitteeController@destroy');

