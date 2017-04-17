<?php
namespace Ry\Categories\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelLocalization;

class Categorizable extends Model
{
	protected $table = "ry_categories_categorizables";
	
	protected $fillable = ["categorie_id"];
	
	protected $visible = ["id", "category"];
	
	protected $with = ["category"];
	
	function categorizable() {
		return $this->morphTo();
	}
	
	public function category() {
		return $this->belongsTo("Ry\Categories\Models\Categorie", "categorie_id");
	}
}