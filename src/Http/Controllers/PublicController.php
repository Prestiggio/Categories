<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicController extends Controller
{
	public function category($category) {
		return $category;
	}
	
	public function getEdit() {
		return view("rycategories::edit");
	}
	
	public function getCategories(Request $request) {
		return app("\Ry\Categories\Http\Controllers\AdminController")->getCategories($request);
	}
}