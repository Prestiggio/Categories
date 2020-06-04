<?php
namespace Ry\Categories;

use Ry\Categories\Models\Categorie;
use Illuminate\Support\Facades\DB;
use Ry\Admin\Models\LanguageTranslation;
use App;

class RyCategorie
{
    private $translations = [];
    
    public function __construct() {
        $this->translations = LanguageTranslation::join("ry_categories_categories", "ry_categories_categories.translation_id", "=", "ry_admin_language_translations.translation_id")->whereLang(App::getLocale())->pluck('translation_string', 'ry_categories_categories.id');
    }
    
	public function editor($id) {
		return view("rycategories::editor_assign", ["id" => $id]);
	}
	
	public function termName(Categorie $categorie) {
	    if(!isset($this->translations[$categorie->id])) {
	        $translations = LanguageTranslation::join("ry_categories_categories", "ry_categories_categories.translation_id", "=", "ry_admin_language_translations.translation_id")->where('ry_categories_categories.id', '=', $categorie->id)->select('translation_string')->get();
	        $found = false;
	        $alts = [];
	        foreach($translations as $translation) {
	            $alts[] = $translation->translation_string;
	            if($translation->lang==App::getLocale()) {
	                $found = true;
	                $this->translations[$categorie->id] = $translation->translation_string;
	                break;
	            }
	        }
	        if(!$found && count($alts)>0) {
	            $this->translations[$categorie->id] = $alts[0];
	        }
	        if(!isset($this->translations[$categorie->id])) {
	            $this->translations[$categorie->id] = '';
	        }
	    }
	    return $this->translations[$categorie->id];
	}
}