<?php
namespace Ry\Categories\Models;

use Baum\Node;
use Ry\Medias\Models\Traits\MediableTrait;
use Ry\Medias\Models\Media;
use Illuminate\Support\Facades\Cache;
use Ry\Admin\Models\Translation;
use App;
use Ry\Admin\Http\Controllers\AdminController as LanguageTranslationController;

class Categorie extends Node {
	
	use MediableTrait;
	
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected $table = 'ry_categories_categories';
	
	protected $hidden = ["parent_id", "categorygroup_id", "multiple", "input", "created_at", "updated_at"];

	protected $fillable = ["active", "multiple", "input"];

	protected $appends = ["selected", "term", "ninput"];
	
	protected $orderColumn = 'position';
	
	public static $scope_enabled = false;
	
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
	
	protected $with = ["translation"];
	
	private $_selected = false;
	
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
	}
	
	public function translation() {
	    return $this->belongsTo(Translation::class, "translation_id");
	}
	
	public function getTermAttribute() {
	    return (object)[
	        'name' => app('rycategories')->termName($this)
	    ];
	}
	
	public function getTermsAttribute() {
	    return $this->translation->meanings()->current()->first();
	}
	
	public function translatedPath() {
	    return $this->belongsTo(Translation::class, "path_translation_id");
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
		return $this->path;
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
		return $this->_selected;
	}
	
	public function setSelectedAttribute($selected) {
	    $this->_selected = $selected;
	}
	
	public static function cacheGroup($groupname, $cache_suffix='', $levels=2) {
	    return Cache::rememberForever('rycategorygroup.'.$groupname.'.'.$cache_suffix, function()use($groupname, $levels){
	        $children = [];
	        for($i=0;$i<$levels;$i++)
	            $children[] = 'children';
	        static::$scope_enabled = true;
	        return static::whereNull("parent_id")->whereHas("group", function($q)use($groupname){
	            $q->whereName($groupname);
	        })->with([implode(".", $children)])->get()->sort(function($item){
	            return $item->term->name;
	        });
	    });
	}
	
	public static function attributeByIds(&$tree, $ids, $attrs) {
	    foreach ($tree as &$item) {
	        if(in_array($item->id, $ids)) {
	            foreach($attrs as $k=>$v) {
	                $item->setAttribute($k, $v);
	            }
	        }
	        static::attributeByIds($item->children, $ids, $attrs);
	    }
	    return $tree;
	}
	
	public static function attributeAll(&$tree, $attrs) {
	    foreach ($tree as &$item) {
            foreach($attrs as $k=>$v) {
                $item->setAttribute($k, $v);
            }
	        static::attributeAll($item->children, $attrs);
	    }
	    return $tree;
	}
	
	public function getPathAttribute() {
	    if($this->path_translation_id>0) {
	        $meanings = $this->translatedPath->meanings();
	        if($meanings->current()->exists())
	            return $meanings->current()->first()->translation_string;
	        return $meanings->first()->translation_string;
	    }
	}
	
	public function makepath() {    
        $ancestors = $this->getAncestorsAndSelf();
        $a = [];
        foreach($ancestors as $ancestor) {
            $a[] = str_slug($ancestor->term->name);
        }
        $path = implode("/", $a);
        app(LanguageTranslationController::class)->putTranslationById($this->path_translation_id, $path);
        
        $descendants = $this->getDescendants();
        foreach($descendants as $descendant) {
            $descendant->makepath();
        }
	}
	
	public function getTreeAttribute() {
	    $ancestors = $this->getAncestorsAndSelf();
	    $a = [];
	    foreach($ancestors as $ancestor) {
	        $a[] = $ancestor->term->name;
	    }
	    return implode(" > ", $a);
	}
	
	public function getLinkAttribute() {
	    return action("\Ry\Categories\Http\Controllers\PublicController@category", ["category" => $this->path]);
	}
	
	public function getNinputAttribute() {
	    if($this->input) {
	        return json_decode($this->input, true);
	    }
	    return [];
	}
	
	public function setNinputAttribute($ar) {
	    static::unescape($ar);
	    $this->input = json_encode($ar);
	}
	
	public static function unescape($ar) {
	    array_walk_recursive($ar, function(&$v, $k){
	        if(!preg_match("/^0\d+/", $v))
	            $v = is_numeric($v)?doubleval($v):$v;
            if($v==='false')
                $v = false;
            if($v==='true')
                $v = true;
	    });
	    return $ar;
	}
}
