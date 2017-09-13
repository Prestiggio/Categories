<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;

class PublicController extends Controller
{
	public function category($category) {
		return $category;
	}
	
	public function getEdit() {
		return view("rycategories::edit");
	}
}