<script type="text/javascript">
	function AdCampaignController($scope, $http, alertService){
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
		
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				$scope.formData.platform = 0;
				$scope.formData.game = 0;
				$scope.formData.source = 0;
				$scope.formData.campaign_name = '';
				$scope.formData.campaign_value = '';
				$scope.formData.default_lp = 0;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
</script>

<div class="col-xs-12" ng-controller="AdCampaignController">
	<div class="row" >
		<div class="eb-content">
			<form action="/ad/campaign" method="post" role="form" ng-submit="processFrom('/ad/campaign')" onsubmit="return false;">
				<div class="form-group">
					<select name="platform" ng-model="formData.platform" ng-init="formData.platform=0" class="form-control" ng-change="getGame()">
						<option value="0"><?php echo Lang::get('campaigns.enter_platform_name')?></option>
						<?php foreach($plats as $key => $v){?>
							<option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name?></option>
						<?php }?>
					</select>
				</div>
				
				<div class="form-group">
					<select name="game" ng-model="formData.game" ng-init="formData.game=0" class="form-control" ng-change="getSource()">
						<option value="0"><?php echo Lang::get('campaigns.enter_game_name')?></option>
							<option ng-repeat="game in game" value="{{game.game_id}}">{{game.game_name}}</option>
					</select>
				</div>
				
				<div class="form-group" >
					<select name="source" id="source" ng-model="formData.source"   class="form-control">
						<option value=""><?php echo Lang::get('campaigns.select_source')?></option>
						<?php foreach($source as $key => $v){?>
						    <option value="<?php echo $v->source_id?>"><?php echo $v->source_name?></option> 
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="campaign_name"></label>
					<input type="text" class="form-control" id="campaign_name" placeholder="<?php echo Lang::get('campaigns.enter_campaign_name') ?>" required ng-model="formData.campaign_name" name="campaign_name" autofocus="autofocus"/> 
				</div>				
				
				<div class="form-group">
					<label for="campaign_value"></label>
					<input type="text" class="form-control" id="campaign_value" placeholder="<?php echo Lang::get('campaigns.enter_campaign_value') ?>" required ng-model="formData.campaign_value" name="campaign_value" autofocus="autofocus"/> 
				</div>			
				
				<div class="form-group">
					<label for="default_lp"></label>
					 <select name="default_lp" ng-model="formData.default_lp" ng-init="formData.default_lp=0" class="form-control">
						<option value="0"><?php echo Lang::get('campaigns.enter_default_lp')?></option>
					   <?php foreach($lp as $key => $v){?>
					   	    <option value="<?php echo $v->lp_id?>"><?php echo $v->lp_id?></option>
					   	<?php } ?>
					</select>
				</div>								

				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>