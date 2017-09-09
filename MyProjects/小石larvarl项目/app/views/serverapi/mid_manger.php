<script>
    function MangerMidController($scope, $http, alertService, $filter) {
    	$scope.alerts = [];
		$scope.formData = {};
		$scope.result = [];

		$scope.processDeal1 = function(type) {
			$scope.formData.type = type;
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/mid/manger',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				setTimeout("window.location.reload()", 100);
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		};

		$scope.processDeal2 = function(id){
			$scope.formData.id = id;
			$scope.processDeal1('4.5.12.5.20.5');
		}
	}
</script>
<div class="col-xs-12" ng-controller="MangerMidController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processDeal1('1.4.4')" onsubmit="return false;">

				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="number" class="form-control" required
						placeholder="<?php echo Lang::get('slave.mid')?>"
						ng-model="formData.mid" name="mid"?>
				</div>
				<div class="form-group col-md-3" style="padding-left:0;">
					<input type="number" class="form-control" required
						placeholder="<?php echo Lang::get('slave.valid_days')?>"
						ng-model="formData.valid_days" name="valid_days"?>
				</div>
				<input type="submit" class="btn btn-primary"
							value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div>
	<div class="col-xs-12" style="min-height:300px">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.mid');?></b></td>
					<td><b><?php echo Lang::get('slave.created_time');?></b></td>
					<td><b><?php echo Lang::get('slave.valid_time');?></b></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data2view as $value) { ?>
					<tr>
						<td><?php echo $value['mid']; ?></td>
						<td><?php echo $value['created_time']; ?></td>
						<td><?php echo $value['valid_time']; ?></td>
						<td><button class="btn btn-primary" ng-click="processDeal2(<?php echo $value['id']; ?>)">DELETE</button></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>