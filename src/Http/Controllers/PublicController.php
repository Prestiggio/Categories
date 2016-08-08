<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;

class PublicController extends Controller
{
	public function getCategory($category) {
		return $category;
	}
}