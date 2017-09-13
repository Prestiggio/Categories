<?php
Route::get('categories/{category}', 'PublicController@category');
Route::controller('categories/json', 'PublicJsonController');
Route::controller('ry/categories/admin', 'AdminController');
Route::controller('ry/categories', 'PublicController');
