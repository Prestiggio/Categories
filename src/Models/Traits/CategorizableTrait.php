<?php
namespace Ry\Categories\Models\Traits;

trait CategorizableTrait
{
	public function categories() {
		return $this->morphToMany('Ry\Categories\Models\Categorie', 'ry_categories_categorizable');
	}
}