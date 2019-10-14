
<?php

use Illuminate\Http\Request;

//login & register
Route::POST('login', 'AuthApi\AuthController@login')->name('login');

Route::POST('logout', 'AuthApi\AuthController@logout');

Route::get('register', 'AuthApi\RegisterController@registerPage');

Route::Post('register', 'AuthApi\RegisterController@register');

Route::get('register/verify/{confirmationCode}', 'AuthApi\ConfirmController@confirm');

Route::post('/password/reset', 'AuthApi\ResetPasswordController@recover')->name('password.reset');

Route::post('/password/reset/{reset_code}', 'AuthApi\ResetPasswordController@Reset')->name('password.reset');


//Committee
Route::get('/committees', 'CommitteeController@index');
Route::get('/committee/{id}', 'CommitteeController@view');
Route::get('/addcommittee', 'CommitteeController@addPage');
Route::post('/addcommittee', 'CommitteeController@add');
Route::get('/updatecommittee', 'CommitteeController@updatePage');
Route::put('/updatecommittee/{id}', 'CommitteeController@update');
Route::delete('/deletecommittee/{id}', 'CommitteeController@destroy');

//posts
Route::get('posts', 'PostController@index');
Route::get('post/{id}', 'PostController@show');
Route::post('create-post', 'PostController@store');
Route::put('update-post/{id}', 'PostController@update');
Route::delete('/post/{id}', 'PostController@destroy');

//comments
Route::get('/post/{id}/comments', 'CommentController@index');
Route::post('/post/{id}/add-comment', 'CommentController@addComment');
Route::put('/update-comment/{id}', 'CommentController@updateComment');
Route::delete('/destroy-comment/{id}', 'CommentController@destroyComment');

//User
Route::get('update-profile/{id}', 'UserController@updateProfilePage');
Route::PUT('update-profile/{id}', 'UserController@updateProfile');
Route::post('update-profile-image/{id}', 'UserController@updateProfileImage');
Route::PUT('update-profile-password/{id}', 'UserController@updateProfilePassword');
Route::get('user/{id}', 'UserController@index');
Route::delete('delete-user/{id}', 'UserController@deleteUser');

//task
Route::get('create-task', 'TaskController@createPage');
Route::post('create-task', 'TaskController@store');
Route::get('pending-tasks', 'TaskController@pendingTasks');
Route::get('complete-tasks', 'TaskController@completeTasks');
Route::get('task/{id}', 'TaskController@viewTask');
Route::post('accept-task/{id}', 'TaskController@acceptTask');
Route::post('refuse-task/{id}', 'TaskController@refuseTask');
Route::post('deliver-task/{id}', 'TaskController@deliverTask');

//Notification
Route::get('/notification', 'NotificationController@getNotification');

//check token
Route::get('/check-token/{user_id}/{token}', 'AuthApi\AuthController@checkToken');
