<?php
namespace Ry\Categories\Models;

use Illuminate\Database\Eloquent\Model;
use Ry\Admin\Models\Traits\HasJsonSetup;

class Categorizable extends Model
{
    use HasJsonSetup;
    
	protected $table = "ry_categories_categorizables";
	
	protected $fillable = ["categorie_id", "main", "setup"];
	
	protected $hidden = ["setup"];
	
	//protected $with = ["category"];
	
	private static $sels = [];
	
	function categorizable() {
		return $this->morphTo();
	}
	
	public function category() {
		return $this->belongsTo("Ry\Categories\Models\Categorie", "categorie_id");
	}
	
	private static function selected($ar) {
		foreach ($ar as $a) {
			if(isset($a["selected"]) && $a["selected"]==true) {
				self::$sels[] = $a;
			}
			self::selected($a["children"]);
		}
	}
	
	public static function forTree($tree, $cast, $notIn = [], $in = []) {		
		self::$sels = [];
		self::selected($tree);
		$arcat = [];
		foreach(self::$sels as $categorie) {
			$arcat[] = $categorie["id"];
		}
		$query = Categorizable::whereIn("categorie_id", $arcat)->where("categorizable_type", "=", $cast);
		if(count($notIn)>0)
			$query->whereNotIn("categorizable_id", $notIn);
		if(count($in)>0)
			$query->whereIn("categorizable_id", $in);
		$cats = $query->get();
		$ar = [];
		foreach($cats as $cat)
			if($cat->categorizable)
				$ar[$cat->categorizable->id] = $cat->categorizable;
		
		return $ar;
	}
	
	public static function byName($q, $cast, $notIn = [], $in = []) {
		$ar = [];
		$results = app("rymd.search")->search("categorie", $q, ["categorie_id"]);
		foreach($results as $categories) {
			foreach($categories as $categorie) {
				$ar[$categorie->categorie_id] = $categorie;
			}
		}
		$query = Categorizable::whereIn("categorie_id", array_keys($ar))->where("categorizable_type", "=", $cast);
		if(count($notIn)>0)
			$query->whereNotIn("categorizable_id", $notIn);
		if(count($in)>0)
			$query->whereIn("categorizable_id", $in);
		
		$cats = $query->get();
		$ar = [];
		foreach($cats as $cat)
			$ar[] = $cat->categorizable;
		
		return $ar;
	}

	public function scopeHigh($query, $n=5) {
		$query->take($n);
	}
}