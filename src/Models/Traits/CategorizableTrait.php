<?php
namespace Ry\Categories\Models\Traits;

use Ry\Categories\Models\Categorie;

trait CategorizableTrait
{
	public function categories() {
		return $this->morphMany('Ry\Categories\Models\Categorizable', 'categorizable');
	}
	
	public function cats() {
		return $this->morphToMany('Ry\Categories\Models\Categorie', 'categorizable', 'ry_socin_categorizables', 'categorie_id');
	}
	
	public function getMainCategoryAttribute() {
		$categories = $this->categories();
		
		$main = $categories->where("main", "=", true);
		if($main->exists())
			return $main->first();
		
		if($categories->exists())
			return $categories->first()->getAncestors()->first();
		
		return Categorie::where("id", "=", 1)->first();
	}
}