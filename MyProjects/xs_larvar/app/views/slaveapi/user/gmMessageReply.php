<script> 
function getGmMessage($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.items = {};

    $scope.processFrom = function() {
    	$scope.items = {};
    	$scope.alerts = [];
    	$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
    	$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/slave-api/gm/message/reply',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {  
			$scope.items = data.result;
			$scope.gm_items = data.gm_result;
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getGmMessage">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group">
					<div class="col-md-4" style="padding-left: 0;padding-bottom: 15px;">
						<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=5>
							<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
							<?php foreach ($servers as $k => $v) { ?>
								<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
							<?php } ?>		
						</select>
					</div>
					<div class="col-md-4">
					    <div class="input-group">
					        <quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
					        <i class="glyphicon glyphicon-calendar"></i>
					    </div>
					</div>
					<div class="col-md-4">
					    <div class="input-group">
					        <quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
					        <i class="glyphicon glyphicon-calendar"></i>
					    </div>
					</div>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="gm_name"
						placeholder="请输入GM名字(非必须)"
						ng-trim="true" ng-model="formData.gm_name" name="gm_name" />
				</div>
				<div class="col-md-6" style="padding: 0">
					<input type="submit" class="btn btn-default" style=""
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
	<div class="col-xs-6">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>所在服</b></td>
					<td><b>提问总数</b></td>
					<td><b>回答总数</b></td>
					<td><b>回复率</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.question}}</td>
					<td>{{t.answer}}</td>
					<td>{{t.rate}}</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-xs-6">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>GM</b></td>
					<td><b>GM平均回复时间</b></td>
					<td><b>GM回复总数</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in gm_items">
					<td>{{t.username}}</td>
					<td>{{t.avg_time}}</td>
					<td>{{t.gm_answer}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>