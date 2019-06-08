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

Route::get('posts', 'PostController@index');

Route::get('post/{id}', 'PostController@show');

Route::post('create-post', 'PostController@store');

Route::PUT('update-post/{id}', 'PostController@update');

Route::delete('/post/{id}', 'PostController@destroy');

//comments
Route::post('/post/{id}/addComment', 'CommentController@addComment');

Route::get('/post/{id}/destroyComment', 'CommentController@destroyComment');

//User

Route::get('user/{id}', 'UserController@updateProfilePage');
Route::PUT('user/{id}', 'UserController@updateProfile');

//task

Route::get('create-task', 'TaskController@createPage');
Route::post('create-task', 'TaskController@store');
Route::get('pendingtasks', 'TaskController@pendingTasks');
Route::get('completetasks', 'TaskController@completeTasks');
Route::get('task/{id}', 'TaskController@viewTask');
Route::post('accepttask/{id}', 'TaskController@acceptTask');
Route::get('refusetask/{id}', 'TaskController@refuseTask');
Route::post('delivertask/{id}', 'TaskController@deliverTask');
