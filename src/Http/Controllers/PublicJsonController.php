<?php
namespace Ry\Categories\Http\Controllers;

use App\Http\Controllers\Controller;
use Ry\Categories\Models\Categorie;

use Faker, Auth;
use LaravelLocalization;
use Illuminate\Http\Request;
use Ry\RealEstate\Models\Immobilier;

class PublicJsonController extends Controller
{
	public function getIndex(Request $request) {
		//$faker = Faker\Factory::create();
		
		//$root = Categorie::where("id", "=", 7)->first();
		//$child = $this->createCategory("Flash")->makeChildOf($root);
		$immobilier = Immobilier::where("id", "=", 10)->first();
		$categorie = Categorie::where("id", "=", 8)->first();
		if(!$immobilier->categories->contains($categorie))
			$immobilier->categories()->attach($categorie);
		
		return [];
	}
	
	private function createCategory($name, $lang=null) {
		$user = Auth::user();
		$user_id = 1;
		if($user)
			$user_id = $user->id;
		if(!$lang)
			$lang = LaravelLocalization::getCurrentLocale();
		
		$category = Categorie::create(["active" => 1, "categorygroup_id" => 1]);
		$category->terms()->createMany([
				["user_id" => $user_id,
						"lang" => $lang,
						"name" => $name,
						"descriptif" => ""]
		]);
		return $category;
	}
	
	public function getSeeder() {
		$categories = [
				["name" => "Location", "children" => [
						["name" => "Appartement", "children" => [
								["name" => "Studio F1"],
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres"]
						]],
						["name" => "Maison / Villa", "children" => [
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres et plus"]
						]],
						["name" => "Terrain"],
						["name" => "Locaux, fond de commerce"]
				]],
				["name" => "Meublé", "children" => [
						["name" => "Appartement", "children" => [
								["name" => "Studio F1"],
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres"]
						]],
						["name" => "Maison / Villa", "children" => [
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres et plus"]
						]],
						["name" => "Locaux, fond de commerce"]
				]],
				["name" => "Vente", "children" => [
						["name" => "Appartement", "children" => [
								["name" => "Studio F1"],
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres"]
						]],
						["name" => "Maison / Villa", "children" => [
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres et plus"]
						]],
						["name" => "Terrain"],
						["name" => "Locaux, fond de commerce"]
				]],
				["name" => "Location saisonnière", "children" => [
						["name" => "Appartement", "children" => [
								["name" => "Studio F1"],
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres"]
						]],
						["name" => "Maison / Villa", "children" => [
								["name" => "1 chambre"],
								["name" => "2 chambres"],
								["name" => "3 chambres"],
								["name" => "4 chambres et plus"]
						]],
						["name" => "Terrain"],
						["name" => "Locaux, fond de commerce"]
				]],
		];
		$this->saveTree($categories);
	}
	
	private function saveTree($ar, $parent=null) {
		foreach($ar as $node) {
			$me = $this->createCategory($node["name"]);
			if($parent)
				$me->makeChildOf($parent);
			if(isset($node["children"]))
				$this->saveTree($node["children"], $me);
		}
		if($parent) {
			foreach($parent->terms as $term)
				$term->makepath();
		}
	}
}