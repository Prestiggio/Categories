<?php
namespace Ry\Categories\Models;

use Baum\Node;
use Ry\Medias\Models\Traits\MediableTrait;
use Ry\Medias\Models\Media;
use Illuminate\Support\Facades\Cache;

class Categorie extends Node {
	
	use MediableTrait;
	
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected $table = 'ry_categories_categories';
	
	protected $hidden = ["parent_id", "depth", "categorygroup_id", "multiple", "input", "created_at", "updated_at"];

	protected $fillable = ["active", "multiple", "input"];

	protected $appends = ["selected"];
	
	protected $orderColumn = 'position';
	
	private $type;
	
	// protected $orderColumn = null;
	
	// protected $guarded = array('id', 'parent_id', 'lft', 'rgt', 'depth');
	
	//
	// This is to support "scoping" which may allow to have multiple nested
	// set trees in the same database table.
	//
	// You should provide here the column names which should restrict Nested
	// Set queries. f.ex: company_id, etc.
	//
	
	// /**
	// * Columns which restrict what we consider our Nested Set list
	// *
	// * @var array
	// */
	protected $scoped = ["categorygroup_id"];
	
	// ////////////////////////////////////////////////////////////////////////////
	
	//
	// Baum makes available two model events to application developers:
	//
	// 1. `moving`: fired *before* the a node movement operation is performed.
	//
	// 2. `moved`: fired *after* a node movement operation has been performed.
	//
	// In the same way as Eloquent's model events, returning false from the
	// `moving` event handler will halt the operation.
	//
	// Please refer the Laravel documentation for further instructions on how
	// to hook your own callbacks/observers into this events:
	// http://laravel.com/docs/5.0/eloquent#model-events
	
	protected static function boot() {
	    parent::boot();
	    
	    static::addGlobalScope("positionOrder", function($q){
	        $q->orderBy("position");
	    });
	    
	    static::saved(function(Categorie $categorie){
	        Cache::forget('rycategorygroup.'.$categorie->group->name);
	    });
	}
	
	public function term() {
		return $this->hasOne("Ry\Categories\Models\Categorylang", "categorie_id")->where(function($query){
			$query->where("lang", "=", "fr");
		});
	}
	
	public function terms() {
		return $this->hasMany("Ry\Categories\Models\Categorylang", "categorie_id");
	}
	
	public function getIconAttribute() {
		if($this->medias->count()>0)
			return $this->medias;
	
		$parent = $this->parent();
		if($parent->exists())
			return $parent->first()->getIconAttribute();
	
		$media = new Media();
		$media->type = "image";
		$media->path = "ico_autre.png";
		return [$media];
	}
	
	public function getRouteKeyName() {
		return "slug";
	}
	
	public function getSlugAttribute() {
		return $this->term->path;
	}
	
	public function newQueryWithoutScopes() {
		if(!in_array("term", $this->with))
			$this->with[] = "term";
		
		return parent::newQueryWithoutScopes();
	}
	
	public function group() {
		return $this->belongsTo("Ry\Categories\Models\Categorygroup", "categorygroup_id");
	}
	
	public function setTypeAttribute($type) {
		$this->type = $type;
	}
	
	public function categorizables() {
		return $this->morphedByMany($this->type, 'categorizable', 'ry_categories_categorizables');
	}

	public function getSelectedAttribute() {
		return false;
	}
	
	public static function cacheGroup($groupname, $levels=2) {
	    return Cache::rememberForever('rycategorygroup.'.$groupname, function()use($groupname, $levels){
	        $children = [];
	        for($i=0;$i<$levels;$i++)
	            $children[] = 'children';
	        return static::whereNull("parent_id")->whereHas("group", function($q)use($groupname){
	            $q->whereName($groupname);
	        })->with([implode(".", $children)])->get();
	    });
	}
}
