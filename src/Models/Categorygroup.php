<?php
namespace Ry\Categories\Models;

use Illuminate\Database\Eloquent\Model;

class Categorygroup extends Model
{
	protected $table = "ry_categories_categorygroups";
	
	public function categories() {
		return $this->hasMany("\Ry\Categories\Models\Categorie", "categorygroup_id");
	}
}