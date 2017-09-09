<script type="text/javascript">
	function AdLinkController($scope, $http, alertService) {
	    
	    $scope.alerts = [];
		$scope.formData = {};
		$scope.getGame = function() {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action1?type=game',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.game = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	    
		$scope.getLp = function() {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action2?type=lp',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.link = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="AdLinkController">
	<div class="row">
		<div class="eb-content">
		        <div class="form-group">
		            <a href="/ad/campaign/create" target = "_blank"><input type="button" class="btn btn-primary" value="<?php echo Lang::get('basic.btn_add') ?>"/></a>
	            </div>
		        <div class="form-group">
					<select name="platform" ng-model="formData.platform" ng-init="formData.platform=0" id="platform" class="form-control" ng-change="getGame()">
						<option value="0"><?php echo Lang::get('campaigns.enter_platform_name')?></option>
						<?php foreach(Platform::all() as $key  => $v){?>
							<option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name?></option>
							<?php
						}
							?>	
					</select>
		        </div>
				<div class="form-group">
					<select name="game" ng-model="formData.game" ng-init="formData.game=''" id="game" class="form-control" >
						<option value=''><?php echo Lang::get('campaigns.enter_game_name')?></option>
						<option ng-repeat="game in game" value="{{game.game_id}}">{{game.game_name}}</option>
					</select>
				
				</div>
				<div class="form-group" >
					<select name="source" id="source" ng-model="formData.source"   class="form-control">
						<option value=''><?php echo Lang::get('campaigns.select_source')?></option>
						<?php foreach($source as $key => $v){?>
						    <option value="<?php echo $v->source_id?>"><?php echo $v->source_name?></option> 
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.btn_search') ?>" ng-click="getLp()"/>
				</div>
				<div class="form-group">
					<table class="table table-striped">
					<tr>
						<td><?php echo Lang::get('campaigns.game_name')?></td>
						<td><?php echo Lang::get('campaigns.source_name')?></td>
						<td><?php echo Lang::get('campaigns.campaign_name')?></td>
						<td><?php echo Lang::get('campaigns.lp_id')?></td>
						<td><?php echo Lang::get('campaigns.lp_name')?></td>
						<td><?php echo Lang::get('campaigns.show_oper')?></td>
					</tr>
					<tr ng-repeat="link in link">
						<td>{{link.game_name}}</td>
						<td>{{link.source_name}}</td>
						<td>{{link.campaign_name}}</td>
						<td>{{link.lp_id}}</td>
						<td>{{link.lp_name}}</td>
						<td><a href="/ad/link/{{link.campaign_id}}/edit" target="_blank">修改</a></td>
					</tr>
				</table>
				</div>
			
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>

