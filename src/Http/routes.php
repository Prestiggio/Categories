<?php
Route::get('categories/{category}', 'PublicController@category');
Route::get('categories/json/test', 'PublicJsonController@getTest');
Route::get('ry/categories/admin/category', 'AdminController@getCategory');
Route::get('ry/categories/admin/edit', 'AdminController@getEdit');
Route::get('ry/categories/admin/categories', 'AdminController@getCategories');
Route::get('ry/categories/admin/root-categories', 'AdminController@getRootCategories');
Route::post('ry/categories/admin/categories', 'AdminController@postCategories');
Route::get('ry/categories/edit', 'PublicController@getEdit');
Route::get('ry/categories/categories', 'PublicController@getCategories');
