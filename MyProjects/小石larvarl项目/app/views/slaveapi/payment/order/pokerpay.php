<script> 
function getPayPokerController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.total = {};
    $scope.processFrom = function() {
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/slave-api/poker/pay-new',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            $scope.total = data;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getPayPokerController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/poker/pay-new" method="get" role="form"
				ng-submit="processFrom('/slave-api/poker/pay-rate')"
				onsubmit="return false;">
				
				<div class="form-group" style="height:35px;">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix">
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
				<tr class="info" id="server">
					<td><?php echo Lang::get("slave.new_pays");?></td>
					<td><?php echo Lang::get("slave.new_fir");?></td>
					<td><?php echo Lang::get("slave.new_sec");?></td>
					<td><?php echo Lang::get("slave.new_thi");?></td>
					<td><?php echo Lang::get("slave.new_for");?></td>
					<td><?php echo Lang::get("slave.new_week");?></td>
					<td><?php echo Lang::get("slave.new_month");?></td>
					<td><?php echo Lang::get("slave.pay_rate");?></td>	
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>{{total.new_pays}}</td>
					<td>{{total.new_fir}}</td>
					<td>{{total.new_sec}}</td>
					<td>{{total.new_thi}}</td>
					<td>{{total.new_for}}</td>
					<td>{{total.new_week}}</td>
					<td>{{total.new_month}}</td>
					<td>{{total.pay_rate}}</td>
				</tr>

			</tbody>
		</table>
	</div> 
</div>