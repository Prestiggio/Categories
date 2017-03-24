<?php
namespace Ry\Categories\Models;

use Illuminate\Database\Eloquent\Model;

class Categorylang extends Model
{
	protected $table = "ry_categories_categorylangs";
	
	private $fallback = "en";
	
	protected $fillable = ["user_id", "path", "name", "descriptif", "lang"];
	
	protected $visible = ["name", "link", "tree"];
	
	protected $appends = ["tree", "link"];
	
	public function category() {
		return $this->belongsTo("Ry\Categories\Models\Categorie", "categorie_id");
	}
	
	public function makepath() {
		if($this->path)
			return $this->path;
		
		$ancestors = $this->category->getAncestorsAndSelf();
		$a = [];
		foreach($ancestors as $ancestor) {
			$a[] = str_slug($ancestor->term->name);
		}
		$this->path = implode("/", $a);
		$this->save();
		
		$descendants = $this->category->getDescendants();
		foreach($descendants as $descendant) {
			foreach($descendant->terms as $lang) {
				$lang->makepath();
			}
		}
	}
	
	public function getTreeAttribute() {
		$ancestors = $this->category->getAncestorsAndSelf();
		$a = [];
		foreach($ancestors as $ancestor) {
			$a[] = $ancestor->term->name;
		}
		return implode(" > ", $a);
	}
	
	public function getLinkAttribute()  {
		return action("\Ry\Categories\Http\Controllers\PublicController@getCategory", ["category" => $this->path]);
	}
}
