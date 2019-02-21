<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Ry\Categories\Models\Categorie;
use Ry\Categories\Models\Categorygroup;

class PublicJsonController extends Controller
{	
	private $group;
	
	public function getTest() {
		Categorygroup::unguard();
		$this->group = Categorygroup::create([
				"name" => "Immobilier"
		]);
		Categorygroup::reguard();
		$parent = $this->createCategorie( "za ian ty" , 'text');
		$me = $this->createCategorie("iny ndray izy", "text");
		$me->makeChildOf($parent);

		return [$me, $parent];
	}
	
	private function createCategorie($name, $input, $descriptif = "", $lang = null) {
		$user_id = 1;
		if (! $lang)
			$lang = "fr";
	
		Categorie::unguard();
		Categorylang::unguard();
		$categorie = $this->group->categories()->create ( [
				"active" => 1,
				"multiple" => 1,
				"input" => $input
		] );
		$categorie->terms ()->createMany ([[
				"user_id" => $user_id,
				"lang" => $lang,
				"name" => $name,
				"descriptif" => $descriptif
		]]);
		Categorie::reguard();
		Categorylang::reguard();
		return $categorie;
	}
}