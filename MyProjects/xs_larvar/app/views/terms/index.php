<script type="text/javascript">
	function AdTermController($scope, $http, alertService) {
	    
	    $scope.alerts = [];
		$scope.formData = {};
		$scope.current_platfomr_id = <?php echo Session::get('platform_id')?>;
		$scope.current_game_id = <?php echo Session::get('game_id') ?>;

		$scope.getGame = function(is_load) {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action2?type=game',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if ($scope.formData.platform == $scope.current_platfomr_id && is_load) {
					$scope.formData.game = $scope.current_game_id;
				} else {
					$scope.formData.game = 0;
				}

				$scope.game = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	    $scope.getSource= function() {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action2?type=source',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.source = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
		
		 $scope.getCampaign= function() {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action1?type=campaign',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.campaign = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
		
		$scope.getLp= function() {
			$http({
				'method' : 'get',
				'url'	 : 'ad/action2?type=lp',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.campaign = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
	    
		$scope.getTerm= function() {
			$http({
				'method' : 'get',
				'url'	 : '/ad/action2?type=term',
				'params'   : $scope.formData,
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.term = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
		
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'get',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg, 2000);
				$scope.formData.campaign_id = 0;
				$scope.formData.term = '';
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};		
	}
</script>
<div class="col-xs-12" ng-controller="AdTermController">
	<div class="row" ng-init="getGame(true)">
		<div class="eb-content">
		        <div class="form-group">
		            <a href="/ad/term/create" target = "_blank"><input type="button" class="btn btn-primary" value="<?php echo Lang::get('basic.btn_add') ?>"/></a>
	            </div>
		        <div class="form-group">
					<select name="platform" ng-model="formData.platform" ng-init="formData.platform=<?php echo Session::get('platform_id')?>" id="platform" class="form-control" ng-change="getGame()">
						<option value="0"><?php echo Lang::get('campaigns.enter_platform_name')?></option>
						<?php foreach(Platform::all() as $key  => $v){?>
							<option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name?></option>
							<?php
						}
							?>	
					</select>
		        </div>
				<div class="form-group">
					<select name="game" ng-model="formData.game" id="game" class="form-control">
						<option value="0" ng-selected="{{formData.game == 0}}"><?php echo Lang::get('campaigns.enter_game_name')?></option>
						<option ng-repeat="g in game" value="{{g.game_id}}" ng-selected="{{g.game_id == formData.game}}">{{g.game_name}}</option>
					</select>
				
				</div>
				<div class="form-group">
					<select name="source" ng-model="formData.source" ng-init="formData.source=0" id="game" class="form-control" ng-change="getCampaign()">
						<option value="0"><?php echo Lang::get('campaigns.enter_source_name')?></option>
						<?php foreach($source as $key => $v){?>
							<option value="<?php echo $v->source_id?>"><?php echo $v->source_name?></option>
						<?php } ?>
					</select>
				
				</div>
				<div class="form-group">
					<select name="campaign" ng-model="formData.campaign" ng-init="formData.campaign=0" id="campaign" class="form-control" >
						<option value="0"><?php echo Lang::get('campaigns.select_campaign_name')?></option>
						<option ng-repeat="campaign in campaign" value="{{campaign.campaign_id}}">{{campaign.campaign_name}}</option>
					</select>
				
				</div>
				<div class="form-group">
					<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.btn_search') ?>" ng-click="getTerm()"/>
				</div>
				<div class="form-group">
					<table class="table table-striped">
					<tr>
						<td><?php echo Lang::get('campaigns.game_name')?></td>
						<td><?php echo Lang::get('campaigns.source_name')?></td>
						<td><?php echo Lang::get('campaigns.campaign_name')?></td>
						<td><?php echo Lang::get('campaigns.term_name')?></td>
						<td><?php echo Lang::get('campaigns.term_value')?></td>
						<td><?php echo Lang::get('campaigns.lp_id')?></td>
						<td><?php echo Lang::get('campaigns.show_oper')?></td>
					</tr>
					<tr ng-repeat="link in term">
						<td>{{link.game_name}}</td>
						<td>{{link.source_name}}</td>
						<td>{{link.campaign_name}}</td>
						<td>{{link.term_name}}</td>
						<td>{{link.term_value}}</td>
						<td>{{link.lp_id}}</td>
						<td><a href="/ad/term/{{link.term_id}}/edit" target="_blank">修改</a></td>
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
