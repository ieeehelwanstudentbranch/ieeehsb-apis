
<?php

use Illuminate\Http\Request;

//login & register
Route::POST('login', 'AuthApi\AuthController@login')->name('login');

Route::post('logout', 'AuthApi\AuthController@logout');

Route::get('register', 'AuthApi\RegisterController@registerPage');

Route::Post('register', 'AuthApi\RegisterController@register');

Route::get('register/verify/{confirmationCode}', 'AuthApi\ConfirmController@confirm');

Route::post('/password/reset', 'AuthApi\ResetPasswordController@recover')->name('password.reset');

Route::post('/password/reset/{reset_code}', 'AuthApi\ResetPasswordController@Reset')->name('password.reset');

//check token
Route::get('/check-token/{user_id}/{token}', 'AuthApi\AuthController@checkToken');


//committee resource
  Route::resource('committee','CommitteeController');

//chapter
Route::resource('chapter','ChapterController');

//posts
Route::resource('post','PostController');
Route::get('committee/{committee}/post','PostController@getCommPost');
Route::post('committee/{committee}/post','PostController@storeCommPost');
//Route::resource('committee.post', 'PostController')->shallow();
Route::get('chapter/{chapter}/post','PostController@getChapPost');
Route::post('chapter/{chapter}/post','PostController@storeChapPost');


//Route::resource('chapter.post', 'PostController')->shallow();



Route::get('/chapter/{chapter}/pending-posts', 'PostController@pendingChapPost');
Route::get('/committee/{committee}/pending-posts', 'PostController@pendingCommPost');
Route::get('/post-general','PostController@postGeneral');
Route::post('/post-general','PostController@storeGeneralPost');
Route::get('/pending-posts', 'PostController@pendingGeneralPost');

Route::post('/approve-posts', 'PostController@approvePost');
Route::post('/disapprove-posts', 'PostController@disapprovePost');

//comments
Route::resource('comment','CommentController');
Route::resource('post.comment','CommentController')->shallow();

//User
Route::resource('user','UserController');

Route::post('update-profile-image/{id}', 'UserController@updateProfileImage');
Route::PUT('update-profile-password/{id}', 'UserController@updateProfilePassword');
Route::delete('change-user/{id}', 'UserController@changeUser');

//task
Route::resource('task','TaskController');
//Route::get('create-task', 'TaskController@createPage');
//Route::post('create-task', 'TaskController@store');
Route::get('pending-tasks', 'TaskController@pendingTasks');
Route::get('complete-tasks', 'TaskController@completeTasks');
//Route::get('task/{id}', 'TaskController@viewTask');
Route::post('accept-task/{id}', 'TaskController@acceptTask');
Route::post('refuse-task/{id}', 'TaskController@refuseTask');
Route::post('deliver-task/{id}', 'TaskController@deliverTask');

//Notification
Route::get('/notification', 'NotificationController@getNotification');

//awards
Route::resource('award','AwardController');
