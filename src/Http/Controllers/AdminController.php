<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Ry\Categories\Models\Categorygroup;
use Ry\Categories\Models\Categorie;
use Illuminate\Database\Eloquent\Collection;
use Ry\Admin\Http\Controllers\AdminController as LanguageAdminController;
use Auth, App;
use Ry\Categories\Models\Categorizable;
use Ry\Admin\Models\LanguageTranslation;

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
		$results = $this->manageCategories($request->all());
		return ["status" => "ok", "redirect" => "", "results" => $results];
	}
	
	public function manageCategories($ar, $parent = null, $lang = null) {
	    LanguageTranslation::$exportOnSave = false;
	    
		$user = Auth::user ();
		
		$results = [];
		
		if (! $lang)
			$lang = "fr";
		
		$index = 0;
		
		foreach ( $ar as $a ) {
			if (isset ( $a ["deleted"] ) && $a ["deleted"] == true) {
				if (isset ( $a ["id"] )) {
					Categorie::where('id', '=', $a["id"])->delete();
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
				    $results[] = $p;
				    app(LanguageAdminController::class)->putTranslationById($p->translation_id, $a['term']['name'], $lang);
				}
			}
			elseif (isset ( $a ["tempid"] )) {
				Categorie::unguard();
				if($parent) {
				    
					$p = $parent->group->categories ()->create ( [
							"active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
					    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
					    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
					    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
					] );
					$p->makeChildOf ( $parent );
				}
				else {
					$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : 1)->first()->categories()->create ( [
					       "active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
					    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
					    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
					    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
					] );
				}
				$p->save ();
				Categorie::reguard();
			}
		
			if(isset($a['children'])) {
			    $results = array_merge($results, $this->manageCategories ($a ["children"], $p ));
			}
			if($p) {
			    $p->position = $index;
			    $p->save();
			    $results[] = $p;
			}
			$index++;
		}
		
		return $results;
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
							    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
							    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
							    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
							] );
							$p->makeChildOf ( $parent );
						}
						else {
							$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : $group)->first()->categories()->create ( [
							         "active" => isset($a["active"]) ? $a["active"] : 1,
									"multiple" => 1,
							    "position" => $index,
							    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
							    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
							    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
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
						    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
						    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
						    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
						] );
						$p->makeChildOf ( $parent );
					}
					else {
						$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : $group)->first()->categories()->create ( [
						    "active" => isset($a["active"]) ? $a["active"] : 1,
								"multiple" => 1,
						    "position" => $index,
						    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
						    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
						    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
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
				if($parent) {
					$p = $parent->group->categories ()->create ( [
					    "active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
					    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
					    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
					    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
					] );
					$p->makeChildOf ( $parent );
				}
				else {
					$p = Categorygroup::where("id", "=", isset($a["group"]["id"]) ? $a["group"]["id"] : $group)->first()->categories()->create ( [
					    "active" => isset($a["active"]) ? $a["active"] : 1,
							"multiple" => 1,
					    "position" => $index,
					    "translation_id" => app(LanguageAdminController::class)->postTranslation($a ["term"] ["name"], $lang)->id,
					    "path_translation_id" => app(LanguageAdminController::class)->putTranslation("path.".str_slug($a ["term"] ["name"], '_', $lang), $a ["term"] ["name"], $lang)->id,
					    "input" => json_encode(isset($a["ninput"]) ? Categorie::unescape($a["ninput"]) : ["type" => "text"])
					] );
				}
				$p->save ();
				Categorie::reguard();
			}
				
			if (isset ( $a ["selected"] ) && $a ["selected"] == true) {
			    if(isset($a['id']) && isset($a['categorizable']) && $a['categorizable']['id']!='') {
			        $joint = Categorizable::find($a['categorizable']['id']);
			        $joint->categorie_id = $a['id'];
			        $joint->save();
			    }
			    else {
			        $cz = $this->categorizable->categories ()->where("categorie_id", "=", $p->id);
			        if(!$cz->exists()) {
			            Categorie::unguard();
			            $this->categorizable->categories ()->create ([
			                "categorie_id" => $p->id
			            ]);
			            Categorie::reguard();
			        }
			    }
			}
			
			if(!isset($a['children']))
			    $a['children'] = [];
			$this->attributeCategories ($this->categorizable,  $a ["children"], $p );
			$index++;
		}
	}
}