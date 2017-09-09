<script>
	function oneKeyOperate($scope,$http, alertService){
		$scope.formData = {};
		$scope.alerts = [];
		$scope.process = function(){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : '/game-server-api/gm/onekey',
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data;
				var len  = result.length;
				for (var i = 0; i < len; i++) {
					if (result[i].status == 'OK') {
						alertService.add('success', result[i].msg);
					}else{
						alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(){
				alertService.add('danger', data.error)
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="oneKeyOperate">
	<div class="row">
		<div class="eb-content">
			<form ng-submit="process()" action="/game-server-api/gm/onekey" role="form" onsubmit="return false;" method="post">
				<div class="form-group">
					<label>
						<input type="radio" ng-model="formData.action_type" name="action_type" ng-init="formData.action_type=1" value='1'/>
						<?php echo Lang::get('serverapi.base_on_name');?>
					</label>

					<label>
						<input type="radio" ng-model="formData.action_type" name="action_type" value="2"/>
						<?php echo Lang::get('serverapi.base_on_player_id')?>
					</label>
				</div>
				<div class="form-group">
					<textarea name="content" ng-model="formData.content" placeholder="<?php echo Lang::get('serverapi.tips_gor_onekey')?>" rows="15" required class="form-control"></textarea>
				</div>
				<input type="submit" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit') ?>"/>
			</form>
		</div>
	</div>
	<div class = "row marfin-top-10">
		<div class = "col-xs-6">
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>			
		</div>
	</div>
</div>