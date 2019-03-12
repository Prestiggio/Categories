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
	    return $this->translations[$categorie->id];
	}
}