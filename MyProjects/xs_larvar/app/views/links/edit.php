<script type="text/javascript">
	function updateAdJsController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		
		
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'put',
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
<div class="col-xs-12" ng-controller="updateAdJsController">
	<div class="row">
		<div class="eb-content">
			<form action="/ad/link/<?php echo $campaign->campaign_id?>" method="put" role="form" ng-submit="processFrom('/ad/link/<?php echo $campaign->campaign_id;?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="campaign_name"></label>
					<input type="text" class="form-control" id="campaign_name"  readonly ng-init="formData.campaign_name='<?php echo $campaign->campaign_name?>'"  required ng-model="formData.campaign_name" name="campaign_name" autofocus="autofocus" ng-autofocus="true" /> 
				</div>
				
				<div class="form-group">
					<label for="lp"></label>
				    <select name="lp" ng-model="formData.lp" ng-init="formData.lp='<?php echo $lp_id?>'" class="form-control">
						<option value="<?php echo $lp_id?>"> <?php echo $lp_id ?></option>
						<?php 
						    
						    foreach ($lp as $k => $v) {
						    	if($lp_id == $v->lp_id ){
						    		continue;
						    	}
						?>
						    <option value="<?php echo $v->lp_id?>" ><?php echo $v->lp_id?></option>
						<?php
							}
						?>
					</select>
				</div>
				
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit')?>" />
			</form>
		</div>
	</div>
	
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	
</div>