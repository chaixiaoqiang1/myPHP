<script>
	function changeController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="changeController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/change/lingshi" method="post"
				role="form"
				ng-submit="processFrom('/game-server-api/change/lingshi')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
						<input type="text" class="form-control"
							ng-model="formData.player_id" name="player_id"
							placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
				</div>
				<div class="form-group">
						<input type="text" class="form-control" ng-model="formData.num"
							name="num"
							placeholder="<?php echo Lang::get('serverapi.change_num') ?>" />
				</div>
				<div class="form-group">
					<label>
					<input type="radio" ng-model="formData.type" name="type" ng-init="formData.type = '<?php echo $types[0]?>'" value="<?php echo $types[0] ?>"/>
					<?php echo Lang::get('serverapi.change_lingshi')?>
					</label>
					<label>
					<input type="radio" ng-model="formData.type" name="type" value="<?php echo $types[1] ?>"/>
					<?php echo Lang::get('serverapi.change_qiyundian')?>
					</label>
					<label>
					<input type="radio" ng-model="formData.type" name="type" value="<?php echo $types[2] ?>"/>
					<?php echo Lang::get('serverapi.change_zaochuanling')?>
					</label>
					<label>
					<input type="radio" ng-model="formData.type" name="type" value="<?php echo $types[3] ?>"/>
					<?php echo Lang::get('serverapi.change_xinfa')?>
					</label>
				</div>
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_change') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><?php echo Lang::get('serverapi.change_type')?></td>
						<td><?php echo Lang::get('serverapi.player_id')?></td>
						<td><?php echo Lang::get('serverapi.server_name')?></td>
						<td><?php echo Lang::get('serverapi.change_num')?></td>
						<td><?php echo Lang::get('serverapi.user_name')?></td>	
						<td><?php echo Lang::get('serverapi.log_time')?></td>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($logs as  $k => $v) { ?>
		             <tr>
						<td><?php echo $v->log_key?></td>
						<td><?php echo $v->player_id?></td>
						<td><?php echo $v->server_name?></td>
						<td><?php echo $v->num?></td>
						<td><?php echo $v->user->username?></td>
						<td><?php echo $v->created_at?></td>
					</tr>
	                   <?php } ?>	
					</tbody>

			</table>
		</div>
	</div>
</div>