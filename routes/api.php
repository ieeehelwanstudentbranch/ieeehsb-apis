<?php

use Illuminate\Http\Request;

//login & register
Route::POST('login', 'AuthApi\AuthController@login')->name('login');

Route::POST('logout', 'AuthApi\AuthController@logout');

Route::get('register', 'AuthApi\RegisterController@registerPage');

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

//posts

Route::get('articles', 'PostController@index');

Route::get('article/{id}', 'PostController@show');

Route::post('create-article', 'PostController@store');

Route::PUT('update-article/{id}', 'PostController@update');

Route::delete('/article/{id}', 'PostController@destroy');

//comments
Route::post('/article/{id}/addComment', 'CommentController@addComment');

Route::get('/article/{id}/destroyComment', 'CommentController@destroyComment');