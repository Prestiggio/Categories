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
		
		$categories = $this->categories();
		
		if($categories->exists()) {
			if($categories->first()->category->getAncestors())
				return $categories->first()->category->getAncestors()->first();
			if($categories->first()->category->parent())
				return $categories->first()->category->parent();
		}
		
		return $this->categories()->first();
	}
}