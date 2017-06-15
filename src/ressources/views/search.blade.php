<script type="application/tree" id="categoriesearch">
	<ul ng-show="!$parent.pipo">
		<li ng-repeat="child in children" rytree src="categoriesearch" children="child.children">
			<div layout="row" layout-wrap layout-align="start center" ng-show="$root.match($root.categories.search, child.term.name)">
				<a href="#" ng-click="pipo=!pipo"><md-icon ng-show="!pipo" md-font-icon="fa fa-angle-down"></md-icon><md-icon ng-show="pipo" md-font-icon="fa fa-angle-right"></md-icon></a>
				<div layout="row">
					<input type="checkbox" ng-model="child.selected" tree-check ng-click="toggle(child)"/> @{{child.term.name}}
				</div>
			</div>
		</li>
	</ul>
</script>