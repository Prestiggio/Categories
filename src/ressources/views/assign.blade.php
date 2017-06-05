<ul>
	<li ng-repeat="child in children" rytree src="categorieassign" children="child.children">
		<a class="toggle-accordion" href="#">fold/unfold</a>
		<input type="checkbox" ng-model="$root.selectedcategories[child.id]" ng-click="$app.toggleCategorie(child)"/> @{{child.term.name}}
	</li>
</ul>