<script type="application/tree" id="categorieassign">
	<ul ng-show="!$parent.pipo">
		<li ng-repeat="child in children" rytree src="categorieassign" ng-if="!child.deleted" children="child.children">
			<div layout="row" layout-wrap layout-align="start center" ng-show="$root.match($root.categories.search, child.term.name)">
				<a href="#" ng-click="pipo=!pipo"><md-icon ng-show="!pipo" md-font-icon="fa fa-angle-down"></md-icon><md-icon ng-show="pipo" md-font-icon="fa fa-angle-right"></md-icon></a>
				<div layout="row">
					<div ng-if="child.id">
						<input type="checkbox" ng-model="child.selected" tree-check ng-click="toggle(child)"/> @{{child.term.name}}
					</div>
					<div ng-if="!child.id" layout="row" layout-align="start center">
						<input type="checkbox" ng-model="child.selected" tree-check ng-click="toggle(child)"/>
						<md-input-container>
							<label>Nom</label>
							<input type="text" ng-model="child.term.name" required/>
						</md-input-container>
					</div>
					<md-input-container>
						<md-button class="md-icon-button" ng-click="child.deleted=true" aria-label="@lang("rycategories::overall.removechild")"><md-icon md-font-icon="fa fa-minus-circle"></md-icon></md-button>
					</md-input-container>
					<md-input-container>
						<md-button class="md-icon-button" ng-click="addChild(child)" aria-label="@lang("rycategories::overall.addchild")"><md-icon md-font-icon="fa fa-plus-circle"></md-icon></md-button>
					</md-input-container>
					<md-input-container>
						<md-radio-button ng-value="child" aria-label="@{{child.term.name}}">Par défaut</md-radio-button>
					</md-input-container>
				</div>
			</div>
		</li>
	</ul>
</script>