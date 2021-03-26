<?php

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

Route::get('/','core\Home@index','home')->name('home');
Route::get('/homepage','core\User_controller@index','homepage')->name('user_home');
Route::post('/uploadpic','core\User_controller@updateProfilePic','uploadpic')->name('uploadpic');
Route::post('/postwall','core\Posts_controller@post_wall','postwall')->name('post_wall');
Route::post('/load_posts','core\Posts_controller@get_posts','load_posts')->name('load_posts');
Route::post('/get_bookmarks','core\Posts_controller@get_bookmarks','get_bookmarks')->name('get_bookmarks');
Route::post('/add_bookmark','core\Posts_controller@post_bookmark','add_bookmark')->name('add_bookmark');
Route::get('view_user/{id}','core\User_controller@view_user','view_user')->name('view_user');
Route::post('like_post','core\Posts_controller@like_post','like_post')->name('like_post');
Route::post('unlike_post','core\Posts_controller@unlike_post','unlike_post')->name('unlike_post');
Route::post('delete_post','core\Posts_controller@delete_post','delete_post')->name('delete_post');
Route::get('follow_user/{id}','core\User_controller@follow_user','follow_user')->name('follow_user');
Route::get('view_posts/{id}','core\Posts_controller@view_posts','view_posts')->name('view_posts');
Route::post('comment/{id}','core\Posts_controller@comment_post','comment')->name('comment');
Route::get('unfollow_user/{id}','core\User_controller@unfollow_user','unfollow_user')->name('unfollow_user');
Route::get('set_parent_id/{id}','core\User_controller@set_parent_id','set_parent_id')->name('set_parent_id');
Route::post('update_profile','core\User_controller@update_profile','update_profile')->name('update_profile');
Route::post('change_password','core\User_controller@change_password','change_password')->name('change_password');
Route::post('send_message','core\User_controller@send_message','send_message')->name('send_message');
Route::get('update_seen','core\User_controller@update_seen','update_seen')->name('update_seen');
Route::post('search_api','core\Basic_functions_con@search_nav','search_api')->name('search_api');

Route::post('create_class','core\Class_con@create_class','create_class')->name('create_class');
Route::get('view_class/{id}','core\Class_con@view_class','view_class')->name('view_class');
Route::post('update_profilepic/{id}','core\Class_con@updateProfilePic','update_profilepic')->name('update_profilepic');
Route::post('post_class_wall/{id}','core\Class_con@postClassWall','post_class_wall')->name('post_class_wall');
Route::get('leave_class/{id}','core\Class_con@leave_class','leave_class')->name('leave_class');
Route::get('join_class/{id}','core\Class_con@join_class','join_class')->name('join_class');
Route::post('load_posts_class','core\Class_con@loadClassPosts','load_posts_class')->name('load_posts_class');
Route::post('my_class_posts','core\Class_con@myClassPosts','my_class_posts')->name('my_class_posts');
Route::post('like_class_post','core\Class_con@like_post','like_class_post')->name('like_class_post');
Route::post('unlike_class_post','core\Class_con@unlike_post','unlike_class_post')->name('unlike_class_post');
Route::post('delete_class_post','core\Class_con@delete_post','delete_class_post')->name('delete_class_post');


Route::post('create_assignment/{id}','core\Class_con@create_assignment','create_assignment')->name('create_assignment');
Route::post('update_assignment/{id}','core\Class_con@update_assignment','update_assignment')->name('update_assignment');
Route::get('deleteassignment/{id}','core\Class_con@delete_assignment','deleteassignment')->name('deleteassignment');
Route::post('submit_assignment/{id}','core\Class_con@submit_assignment','submit_assignment')->name('submit_assignment');
Route::post('mark_attendance/{id}','core\Class_con@mark_attendance','mark_attendance')->name('mark_attendance');
Route::post('mark_payments/{id}','core\Class_con@mark_payments','mark_payments')->name('mark_payments');
Route::get('set_grades/{id}/{id1}/{id2}','core\Class_con@set_grades','set_grades')->name('set_grades');
Route::get('leave_from_class/{id}/{id1}','core\Class_con@leave_from_class','leave_from_class')->name('leave_from_class');
Route::get('add_admin/{id}/{id1}','core\Class_con@add_admin','add_admin')->name('add_admin');
Route::get('delete_class/{id}','core\Class_con@delete_class','delete_class')->name('delete_class');

Route::post('api/location/save','core\Location_con@save_location','gps_log')->name('gps_log');
Route::get('view_map','core\Location_con@view_map','view_map')->name('view_map');

Route::post('api/location/search','Api\Location_con@search_location','location_search')->name('location_search');
Route::post('api/hotels/search','core\Home@search','search_hotel')->name('search_hotel');
Route::get('api/nearby/places','Api\Google_places@get_nearby_places','nearby_places')->name('nearby_places');

Route::get('web/login','Auth\User_auth@login','signin')->name('signin');

Route::get('web/register','Auth\User_auth@register','register')->name('register');
Route::post('web/register/save','Auth\User_auth@save_user','register')->name('save_user');
Route::post('web/login/check','Auth\User_auth@check_login','login_check')->name('login_check');
/*Route::get('web/logout','Auth\User_auth@logout','logout')->name('logout');*/

Route::get('web/logout', function(Request $request) {
    if (session()->has('user_data')) {
        session()->forget('user_data');
    }
    return redirect('/');
})->name('logout');
Route::get('web/dashboard','core\Dashboard@index','dashboard')->name('dashboard');
