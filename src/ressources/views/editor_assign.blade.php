<script type="application/tree" id="{{$id}}">
	<ul ng-show="!$parent.pipo">
		<li ng-repeat="child in children" rytree src="{{$id}}" ng-if="!child.deleted" children="child.children">
			<div layout="row" layout-wrap layout-align="start center">
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
				</div>
			</div>
		</li>
	</ul>
</script>