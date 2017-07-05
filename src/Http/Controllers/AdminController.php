<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ry\Categories\Models\Categorygroup;
use Ry\Categories\Models\Categorie;
use Auth;

class AdminController extends Controller
{
	private $categorizable;
	
	public function __construct() {
		$this->middleware("admin");
	}
	
	public function getCategory($category) {
		return $category;
	}
	
	public function getEdit() {
		return view("rycategories::edit");
	}
	
	public function getCategories() {
		$roots = Categorie::roots ()->get ();
		$ar = [ ];
		foreach ( $roots as $root ) {
			$obj = $root->getDescendantsAndSelf ()->toHierarchy ();
			foreach ( $obj as $k => $v ) {
				$ar [] = $v;
			}
		}
		return $ar;
	}
	
	public function manageCategories($ar, $parent = null, $lang = null) {
		$user = Auth::user ();
		
		if (! $lang)
			$lang = "fr";
		
		foreach ( $ar as $a ) {
			if (isset ( $a ["deleted"] ) && $a ["deleted"] == true) {
				if (isset ( $a ["id"] )) {
					Categorie::where("id", "=", $a ["id"] )->first()->delete();
				}
				continue;
			}
		
			$p = null;
			if (isset ( $a ["id"] ))
				$p = Categorie::where ( "id", "=", $a ["id"] )->first ();
			elseif (isset ( $a ["tempid"] )) {
				if($parent) {
					$p = $parent->group->categories ()->create ( [
							"active" => 1,
							"multiple" => 1,
							"input" => "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"]
					] );
					$p->makeChildOf ( $parent );
				}
				else {
					$p = Categorygroup::where("id", "=", 1)->first()->categories()->create ( [
							"active" => 1,
							"multiple" => 1,
							"input" => "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"]
					] );
				}
				$p->save ();
			}
		
			$this->manageCategories ($a ["children"], $p );
		}
	}
	
	public function attributeCategories($categorizable, $ar, $parent = null, $lang = null) {
		$user = Auth::user ();
		
		$this->categorizable = $categorizable;
	
		if (! $lang)
			$lang = "fr";
	
		foreach ( $ar as $a ) {
			if (isset ( $a ["deleted"] ) && $a ["deleted"] == true) {
				if (isset ( $a ["id"] )) {
					$this->categorizable->categories ()->detach ( $a ["id"] );
				}
				continue;
			}
				
			$p = null;
			if (isset ( $a ["id"] ))
				$p = Categorie::where ( "id", "=", $a ["id"] )->first ();
			elseif (isset ( $a ["tempid"] )) {
				if($parent) {
					$p = $parent->group->categories ()->create ( [
							"active" => 1,
							"multiple" => 1,
							"input" => "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"]
					] );
					$p->makeChildOf ( $parent );
				}
				else {
					$p = Categorygroup::where("id", "=", 1)->first()->categories()->create ( [
							"active" => 1,
							"multiple" => 1,
							"input" => "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"]
					] );
				}
				$p->save ();
			}
				
			if (isset ( $a ["selected"] ) && $a ["selected"] == true) {
				$cz = $this->categorizable->categories ()->where("categorie_id", "=", $p->id);
				if(!$cz->exists()) {
					$this->categorizable->categories ()->create ([
							"categorie_id" => $p->id
					]);
				}
			}
				
			$this->attributeCategories ($this->categorizable,  $a ["children"], $p );
		}
	}
}