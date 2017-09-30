<?php
namespace Ry\Categories;

class RyCategorie
{
	public function editor($id) {
		return view("rycategories::editor_assign", ["id" => $id]);
	}
}