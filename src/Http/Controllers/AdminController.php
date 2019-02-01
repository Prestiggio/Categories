<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ry\Categories\Models\Categorygroup;
use Ry\Categories\Models\Categorie;
use Ry\Categories\Models\Categorylang;
use Illuminate\Database\Eloquent\Collection;
use Auth;

class AdminController extends Controller
{
	private $categorizable;
	
	public function __construct() {
		$this->middleware(["web", "admin"]);
	}
	
	public function getCategory($category) {
		return $category;
	}
	
	public function getEdit() {
		return view("rycategories::edit");
	}
	
	public function getCategories(Request $request) {
		$notIn = [];
		if(count($notIn)>0) {
			$query = Categorie::roots ()->whereNotIn("id", $notIn);
		}
		else {
			$query = Categorie::roots ();
		}
		if($request->has("group")) {
			$query->where("categorygroup_id", "=", $request->get("group")["id"]);
		}
		$roots = $query->get();
		$ar = [ ];
		foreach ( $roots as $root ) {
			$obj = $root->getDescendantsAndSelf ()->toHierarchy ();
			foreach ( $obj as $k => $v ) {
				$ar [] = $v;
			}
		}
		return $ar;
	}
	
	public function getRootCategories($notIn = []) {
		if(count($notIn)>0) {
			$roots = Categorie::roots ()->whereNotIn("id", $notIn)->get ();
		}
		else {
			$roots = Categorie::roots ()->get ();
		}
		return $roots;
	}
	
	public function postCategories(Request $request) {
		$this->manageCategories($request->all());
		return ["status" => "ok", "redirect" => ""];
	}
	
	public function manageCategories($ar, $parent = null, $lang = null) {
		$user = Auth::user ();
		
		if (! $lang)
			$lang = "fr";
		
		$index = 0;
		foreach ( $ar as $a ) {
			if ((isset ( $a ["deleted"] ) && $a ["deleted"] == true) || (isset ( $a ["selected"] ) && $a ["selected"] == false)) {
				if (isset ( $a ["id"] )) {
					Categorie::where("id", "=", $a ["id"] )->first()->delete();
				}
				continue;
			}
		
			$p = null;
			if (isset ( $a ["id"] )) {
				$p = Categorie::where ( "id", "=", $a ["id"] )->first ();
				if(isset($a['dirty']) && $a['dirty']) {
				    if(isset($a['active'])) {
				        $p->active = $a['active'];
				    }
				    $p->save();
				    if(isset($a['term']['name'])) {
				        $p->term->name = $a['term']['name'];
				        $p->term->save();
				    }
				}
			}
			elseif (isset ( $a ["tempid"] )) {
				Categorie::unguard();
				Categorylang::unguard();
				if($parent) {
					$p = $parent->group->categories ()->create ( [
							"active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
					    "input" => isset($a["input"]) ? $a["input"] : "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"],
					    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
					] );
					$p->makeChildOf ( $parent );
				}
				else {
					$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : 1)->first()->categories()->create ( [
					       "active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
					    "input" => isset($a["input"]) ? $a["input"] : "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"],
					    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
					] );
				}
				$p->save ();
				Categorie::reguard();
				Categorylang::reguard();
			}
		
			if(isset($a['children'])) {
			    $this->manageCategories ($a ["children"], $p );
			}
			if($p) {
			    $p->position = $index;
			    $p->save();
			}
			$index++;
		}
	}
	
	public function attributeCategories($categorizable, $ar, $parent = null, $lang = "fr", $group = 1) {
		$user = Auth::user ();
		
		$this->categorizable = $categorizable;
	
		$index = 0;
		foreach ( $ar as $a ) {
			if ((isset ( $a ["deleted"] ) && $a ["deleted"] == true) || (isset ( $a ["selected"] ) && $a ["selected"] == false)) {
				if (isset ( $a ["id"] )) {
					$this->categorizable->categories()->where("categorie_id", "=", $a["id"])->delete();
				}
				if(isset ( $a ["deleted"] ) && $a ["deleted"] == true) {
					continue;
				}
				if(isset($a["children"])) {
					$p = null;
					if (isset ( $a ["id"] ))
						$p = Categorie::where ( "id", "=", $a ["id"] )->first ();
					elseif (isset ( $a ["tempid"] )) {
						if($parent) {
							$p = $parent->group->categories ()->create ( [
							         "active" => isset($a["active"]) ? $a["active"] : 1,
									"multiple" => 1,
							    "position" => $index,
							    "input" => isset($a["input"]) ? $a["input"] : "text"
							] );
							$p->terms ()->create ( [
									"user_id" => $user->id,
									"lang" => $lang,
									"name" => $a ["term"] ["name"],
							    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
							] );
							$p->makeChildOf ( $parent );
						}
						else {
							$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : $group)->first()->categories()->create ( [
							         "active" => isset($a["active"]) ? $a["active"] : 1,
									"multiple" => 1,
							    "position" => $index,
							    "input" => isset($a["input"]) ? $a["input"] : "text"
							] );
							$p->terms ()->create ( [
									"user_id" => $user->id,
									"lang" => $lang,
									"name" => $a ["term"] ["name"],
							    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
							] );
						}
						$p->save ();
					}
					$this->attributeCategories ($this->categorizable,  $a ["children"], $p );
					$index++;
				}
				elseif(isset ( $a ["tempid"] )) {
				    $p = null;
					if($parent) {
						$p = $parent->group->categories ()->create ( [
						    "active" => isset($a["active"]) ? $a["active"] : 1,
								"multiple" => 1,
						    "position" => $index,
						    "input" => isset($a["input"]) ? $a["input"] : "text"
						] );
						$p->terms ()->create ( [
								"user_id" => $user->id,
								"lang" => $lang,
								"name" => $a ["term"] ["name"],
						    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
						] );
						$p->makeChildOf ( $parent );
					}
					else {
						$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : $group)->first()->categories()->create ( [
						    "active" => isset($a["active"]) ? $a["active"] : 1,
								"multiple" => 1,
						    "position" => $index,
						    "input" => isset($a["input"]) ? $a["input"] : "text"
						] );
						$p->terms ()->create ( [
								"user_id" => $user->id,
								"lang" => $lang,
								"name" => $a ["term"] ["name"],
						    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
						] );
					}
					$p->save ();
					$index++;
				}
				continue;
			}
				
			$p = null;
			if (isset ( $a ["id"] )) {
				$p = Categorie::where ( "id", "=", $a ["id"] )->first ();
			}
			elseif (isset ( $a ["tempid"] )) {
				Categorie::unguard();
				Categorylang::unguard();
				if($parent) {
					$p = $parent->group->categories ()->create ( [
					    "active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
							"input" => isset($a["input"]) ? $a["input"] : "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"],
					    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
					] );
					$p->makeChildOf ( $parent );
				}
				else {
					$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : $group)->first()->categories()->create ( [
					    "active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
					    "input" => isset($a["input"]) ? $a["input"] : "text"
					] );
					$p->terms ()->create ( [
							"user_id" => $user->id,
							"lang" => $lang,
							"name" => $a ["term"] ["name"],
					    "descriptif" => isset($a["descriptif"]) ? $a["descriptif"] : ''
					] );
				}
				$p->save ();
				Categorie::reguard();
				Categorylang::reguard();
			}
				
			if (isset ( $a ["selected"] ) && $a ["selected"] == true) {
				$cz = $this->categorizable->categories ()->where("categorie_id", "=", $p->id);
				if(!$cz->exists()) {
					Categorie::unguard();
					$this->categorizable->categories ()->create ([
							"categorie_id" => $p->id
					]);
					Categorie::reguard();
				}
			}
				
			$this->attributeCategories ($this->categorizable,  $a ["children"], $p );
			$index++;
		}
	}
}