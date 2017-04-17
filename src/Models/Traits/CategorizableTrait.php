<?php
namespace Ry\Categories\Models\Traits;

trait CategorizableTrait
{
	public function categories() {
		return $this->morphMany('Ry\Categories\Models\Categorizable', 'categorizable');
	}
}