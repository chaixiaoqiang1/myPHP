<script>
	function ServerItemController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/server/item',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if (data.error == "没有数据") {
					alertService.add('danger', data.error);
				}else{
					$scope.items = data;
				}
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.download = function(url) {
			alertService.alerts = $scope.alerts;
		     $http({
	        'method': 'post',
	        'url': url,
	        'data': $.param($scope.formData),
	        'headers': {
	            'Content-Type': 'application/x-www-form-urlencoded'
	        }
	    	}).success(function(data) {
	    		alertService.add('success', 'OK');
	    		console.log(data.now);
	        	window.location.replace("/game-server-api/server/download?now=" + data.now);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};

	}
</script>
<div class="col-xs-12" ng-controller="ServerItemController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0" style="width:60%;">
						<option value="0"><?php echo Lang::get('serverapi.select_all_server') ?></option>
						<?php foreach ($server as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="item_id" ng-model="formData.item_id" ng-init="formData.item_id=0" style="width:60%;">
						<option value="0"><?php echo Lang::get('serverapi.enter_item_name') ?></option>
						<?php foreach ($item as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>
				</div>
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
			<br>
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" />
					</div>
			</div>
			<div class="col-md-4" style="padding: 30">
				<input type='button' class="btn btn-warning"
					value="<?php echo Lang::get('serverapi.download_csv') ?>"
					ng-click="download('/game-server-api/server/download')" />
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
		<table class="table table-striped" style="width:30%;">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id')?></b></td>
					<td><b><?php echo Lang::get('slave.server_name');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.server_name}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>