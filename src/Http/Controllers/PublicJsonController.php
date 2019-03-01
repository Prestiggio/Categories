<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Ry\Categories\Models\Categorie;
use Ry\Categories\Models\Categorygroup;
use Ry\Admin\Http\Controllers\AdminController as LanguageAdminController;
use App;

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
		if (! $lang)
			$lang = App::getLocale();
	
		Categorie::unguard();
		$categorie = $this->group->categories()->create ( [
				"active" => 1,
				"multiple" => 1,
		        "translation_id" => app(LanguageAdminController::class)->postTranslation($name, $lang)->id,
				"input" => $input
		] );
		Categorie::reguard();
		return $categorie;
	}
}