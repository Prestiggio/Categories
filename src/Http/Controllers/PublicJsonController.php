<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;

class PublicJsonController extends Controller
{
	public function getCharacteristics() {
		return Characteristic::allLeaves()->get();
	}
}