<script>
function neiwanController($scope, alertService, $http) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.user = {};
		$scope.processFrom = function(url) {
			$scope.formData.delete_uid = 0;
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/platform-api/user/neiwan',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.res);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});

			$scope.refresh();
		};

		$scope.delete = function(uid, user){
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.delete_uid = uid;
			$scope.formData.delete_user = user;
			$http({
				'method' : 'post',
				'url'	 : '/platform-api/user/neiwan',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.res);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
			$scope.formData.delete_uid = 0;

			$scope.refresh();
		}

		$scope.refresh = function(){
			setTimeout('window.location.reload()', 500);
		}
}
</script>
<div class="col-xs-12" ng-controller="neiwanController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="post" role="form"
				ng-submit="processFrom('/platform-api/user/neiwan')"
				onsubmit="return false;">
				<div class="form-group col-xs-5">
					<input type="text" class="form-control" id="uid"
						placeholder="<?php echo Lang::get('platformapi.enter_user_id') ?>"
						required ng-model="formData.uid" name="uid" />
				</div>
				<div class="form-group col-xs-5">
					<input type="text" class="form-control" id="user"
						placeholder="<?php echo Lang::get('platformapi.enter_user_name') ?>"
						required ng-model="formData.user" name="user" />
				</div>

				<input type="submit" class="btn btn-primary"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
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
					<td><?php echo Lang::get('platformapi.game_name') ?></td>
					<td><?php echo Lang::get('platformapi.neiwan_uid') ?></td>
					<td><?php echo Lang::get('platformapi.user') ?></td>
					<td><?php echo Lang::get('platformapi.created_time') ?></td>
					<td><?php echo Lang::get('platformapi.creater') ?></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
    <?php foreach ($neiwans as $k => $v) { ?>
        <tr>
					<td><?php echo $v['game_id'] ?></td>
					<td><?php echo $v['uid'] ?></td>
					<td><?php echo $v['user'] ?></td>
					<td><?php echo $v['created_time'] ?></td>
					<td><?php echo $v['creater'] ?></td>
					<td><input type="button" class="btn btn-danger"
					value="<?php echo Lang::get('basic.delete') ?>" ng-click=delete('<?php echo $v['uid'] ?>','<?php echo $v['user'] ?>') /></td>
				</tr>
    <?php } ?>  
        </tbody>
		</table>
	</div>
</div>