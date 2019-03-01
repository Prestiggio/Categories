<?php
namespace Ry\Categories\Models;

use Illuminate\Database\Eloquent\Model;
use Ry\Medias\Models\Media;
use Ry\Admin\Http\Controllers\AdminController as LanguageAdminController;

class Categorygroup extends Model
{
	protected $table = "ry_categories_categorygroups";
	
	protected $fillable = ['name'];
	
	public function categories() {
		return $this->hasMany("\Ry\Categories\Models\Categorie", "categorygroup_id");
	}
	
	private function createCategorie($name, $input, $descriptif = "", $lang = null) {
		if (! $lang)
			$lang = "fr";
	
		Categorie::unguard();
		$categorie = $this->categories()->create ( [
				"active" => 1,
				"multiple" => 1,
		    "translation_id" => app(LanguageAdminController::class)->postTranslation($name, $lang)->id,
				"input" => $input
		] );
		Categorie::reguard();
		return $categorie;
	}
	
	public function saveTree($ar, $parent = null) {
		foreach ( $ar as $node ) {
			$me = $this->createCategorie( $node ["name"] , isset($node["input"]) ? $node["input"] : 'text');
			if(isset($node["icon"])) {
				Media::unguard();
				$me->medias()->create([
						"owner_id" => 1,
						"title" => $node["name"],
						"path" => $node["icon"],
						"type" => "image"
				]);
				Media::reguard();
			}
			if ($parent)
				$me->makeChildOf ( $parent );
			$me->save();
			if (isset ( $node ["children"] ))
				$this->saveTree ( $node ["children"], $me );
		}
	}
}