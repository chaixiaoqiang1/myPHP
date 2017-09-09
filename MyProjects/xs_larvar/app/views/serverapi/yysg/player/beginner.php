<script>
	function setBeginnerController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
		};
	}
</script>
<div class="col-xs-12" ng-controller="setBeginnerController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/players/beginner" method="post" role="form"
				ng-submit="processFrom('/game-server-api/players/beginner')"
				onsubmit="return false;">

				<div class="form-group">
					<input type="text" class="form-control" id="<?php echo $player_key; ?>"
						placeholder="<?php echo Lang::get('serverapi.enter_'.$player_key) ?>"
						required ng-model="formData.<?php echo $player_key; ?>" name="<?php echo $player_key; ?>" />
				</div>
				<b>选择新增或删除</b>
				<div class="form-group">
					<select class="form-control" name="is_beginner"
							id="is_beginner" ng-model="formData.is_beginner"
							ng-init="formData.is_beginner=1">
							<option value="0">删除新手指导员</option>
							<option value="1">新增新手指导员</option>
					</select>
				</div>
				<input type="submit" class="btn btn-default"
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
	<br>
	<div class="eb-content">
		<p><b><?php echo Lang::get('slave.beginner_now');?></b></p>
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($masters as $k => $v) { ?>
					<tr>
						<td><?php echo $k; ?></td>
						<td><?php echo $v; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>