<script>
	function getEconomyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		//$scope.end_time = null;
		$scope.formData = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/poker/queryEconomy',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data.result;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div ng-app = "yApp">
<div class="col-xs-12" ng-controller="getEconomyController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/game-server-api/poker/queryEconomy" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<br/>
				<div class="clearfix"></div>

				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding-left: 15px ;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding-left: 15px ;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					
				</div>
				<div class="clearfix"></div>
				
				<div class="col-md-6" style="padding-left:15px">
					<div class="input-group">
						<input type="submit" class="btn btn-default" style="" value="<?php echo Lang::get('basic.btn_submit') ?>" />
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

			<div class="col-xs-12">
				<table class="table table-striped">
					<thead>
						<tr class="info">
							<td><?php echo Lang::get('serverapi.date')?></td>
							<td><?php echo Lang::get('serverapi.date_currency') ?></td>
							<td><?php echo Lang::get('serverapi.date_currency_recover')?></td>
							<td><?php echo Lang::get('serverapi.date_active_tongqian')?></td>
							<td><?php echo Lang::get('serverapi.date_anonymity_tongqian')?></td>
							<td><?php echo Lang::get('serverapi.date_tongqian')?></td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat="t in items">
							<td>{{t.date}}	</td>
							<td>{{t.issue}}</td>
							<td>{{t.recover}}</td>
							<td>{{t.active}}</td>
							<td>{{t.anonymous}}</td>
							<td>{{t.all}}</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
	</div>

</div>