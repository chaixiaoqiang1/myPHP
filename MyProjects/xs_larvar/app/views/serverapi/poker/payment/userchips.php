<script>

	function getAllChipsController($scope, $http, alertService, $filter) {
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
				'url'	 : '/slave-api/poker/user-chips',
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
<div class="col-xs-12" ng-controller="getAllChipsController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/poker/cash" method="get" role="form"
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
		<table class="table table-striped" >
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.date')?></b></td>
					<td><b><?php echo Lang::get('slave.gold')?></b></td>
					<td><b><?php echo Lang::get('slave.total')?></b></td>
					<td><b><?php echo Lang::get('slave.chips1')?></b></td>
					<td><b><?php echo Lang::get('slave.chips2')?></b></td>
					<td><b><?php echo Lang::get('slave.chips3')?></b></td>
					<td><b><?php echo Lang::get('slave.chips4')?></b></td>
					<td><b><?php echo Lang::get('slave.chips5')?></b></td>
					<td><b><?php echo Lang::get('slave.chips6')?></b></td>
					<td><b><?php echo Lang::get('slave.chips7')?></b></td>
					<td><b><?php echo Lang::get('slave.chips8')?></b></td>
					<td><b><?php echo Lang::get('slave.chips9')?></b></td>
					<td><b><?php echo Lang::get('slave.chips10')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat = "t in items">
					<td>{{t.date}}</td>
					<td>{{t.gold}}</td>
					<td>{{t.all}}</td>
					<td>{{t.chips1}}</td>
					<td>{{t.chips2}}</td>
					<td>{{t.chips3}}</td>
					<td>{{t.chips4}}</td>
					<td>{{t.chips5}}</td>
					<td>{{t.chips6}}</td>
					<td>{{t.chips7}}</td>
					<td>{{t.chips8}}</td>
					<td>{{t.chips9}}</td>
					<td>{{t.chips10}}</td>
					
				</tr>
			</tbody>
			
		</table>	
	</div>
</div>
</div>