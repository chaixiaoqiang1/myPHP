<script type="text/javascript">
	function createAdTermController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.getCampaign = function() {
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
		
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
	
</script>
<div class="col-xs-12" ng-controller="createAdTermController">
	<div class="row">
		<div class="eb-content">
			<form action="/ad/term" method="post" role="form" ng-submit="processFrom('/ad/term')" onsubmit="return false;">
				<div class="form-group">
					<select name="game" ng-model="formData.game" ng-init="formData.game=0" class="form-control" >
					    <option value="0"><?php echo Lang::get('campaigns.enter_game_name')?></option>
						<?php foreach ( $game as $key => $v){?>
							<option value="<?php echo $v->game_id?>"><?php echo $v->game_name?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<select name="source" ng-model="formData.source" ng-init="formData.source=0" class="form-control" ng-change="getCampaign()">
					    <option value="0"><?php echo Lang::get('campaigns.select_source')?></option>
						<?php foreach($source as $key => $v){?>
						    <option value="<?php echo $v->source_id?>"><?php echo $v->source_name?></option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<select name="campaign" ng-model="formData.campaign" ng-init="formData.campaign=0" class="form-control">
					    <option value="0"><?php echo Lang::get('campaigns.select_campaign')?></option>
							<option ng-repeat="campaign in campaign" value="{{campaign.campaign_id}}">{{campaign.campaign_name}}</option>
					</select>
				</div>
				
				<div class="form-group">
					<label for="term"></label>
					<textarea class="form-control" id="term" placeholder="<?php echo Lang::get('attr.enter_term') ?>" required ng-model="formData.term" name="term" autofocus="autofocus" ng-autofocus="true" /></textarea>
				    <font color="red"></font>*请依次输入term_name,term_value,默认lp_id</font>
				</div>
				
				
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>
			</form>
		</div>
	</div>
	
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>