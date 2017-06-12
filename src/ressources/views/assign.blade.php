<script type="application/tree" id="categorieassign">
	<ul ng-show="!$parent.pipo">
		<li ng-repeat="child in children" rytree src="categorieassign" ng-if="!child.deleted" children="child.children">
			<div layout="row" layout-wrap layout-align="start center">
			<a href="#" ng-click="pipo=!pipo"><md-icon ng-show="!pipo" md-font-icon="fa fa-angle-down"></md-icon><md-icon ng-show="pipo" md-font-icon="fa fa-angle-right"></md-icon></a>
			<div layout="row" layout-align="start center">
				<div ng-if="child.id">
					<input type="checkbox" ng-model="child.selected" ng-click="$root.toggleCategorie(child)"/> @{{child.term.name}}
				</div>
				<div ng-if="!child.id" layout="row" layout-align="start center">
					<input type="checkbox" ng-model="child.selected" ng-click="$root.toggleCategorie(child)"/>
					<md-input-container>
						<label>Nom</label>
						<input type="text" ng-model="child.term.name" required/>
					</md-input-container>
				</div>
				<md-button class="md-icon-button" ng-click="child.deleted=true" aria-label="@lang("rycategories::overall.removechild")"><md-icon md-font-icon="fa fa-minus-circle"></md-icon></md-button>
				<md-button class="md-icon-button" ng-click="addChild(child)" aria-label="@lang("rycategories::overall.addchild")"><md-icon md-font-icon="fa fa-plus-circle"></md-icon></md-button>
			</div>
			</div>
		</li>
	</ul>
</script>