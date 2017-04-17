<?php
namespace Ry\Categories\Models;

use Baum\Node;
use LaravelLocalization;
use Ry\Medias\Models\Traits\MediableTrait;
use Ry\Caracteres\Models\Traits\RankableTrait;
use Ry\Medias\Models\Media;

class Categorie extends Node {
	
	use MediableTrait, RankableTrait;
	
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected $table = 'ry_categories_categories';
	
	protected $visible = ["id", "term", "active"];
	
	protected $with = ["term"];
	
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
	
	public function term() {
		return $this->hasOne("Ry\Categories\Models\Categorylang", "categorie_id")->where(function($query){
			$query->where("lang", "=", LaravelLocalization::getCurrentLocale());
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
}
