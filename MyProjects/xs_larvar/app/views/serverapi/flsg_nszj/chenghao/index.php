<script>
	function changeChenghaoController($scope, $http, alertService) {
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
<div class="col-xs-12" ng-controller="changeChenghaoController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/change-chenghao" method="post" role="form"
				ng-submit="processFrom('/game-server-api/change-chenghao')" onsubmit="return false;">
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
				<div class="form-group" style="height: 30px;">
					<input type="text" class="form-control"
						ng-model="formData.player_id" name="player_id" required
						placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="operate_type" ng-model="formData.operate_type"
							ng-init="formData.operate_type=1">
							<option value="1"><?php echo Lang::get('serverapi.chenghao_add') ?></option>
							<option value="2"><?php echo Lang::get('serverapi.chenghao_remove') ?></option>
						</select>
					</div>
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="chenghao" id="select_chenghao"
							ng-model="formData.chenghao" ng-init="formData.chenghao=0">
							<option value="0"><?php echo Lang::get('serverapi.select_chenghao') ?></option>
							<?php foreach ($chenghaos as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' . '.$v->des;?></option>
							<?php } ?>	
						</select>

					</div>
				</div>
				<div class="clearfix">
					<br />
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
						<td><?php echo Lang::get('serverapi.player_id')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_server')?></td>
						<td><?php echo Lang::get('serverapi.chenghao_name')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_operate_type')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_operator')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_operate_time')?></td>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($chenghao_logs as $k => $v) { ?>
		             <tr>
						<td><?php echo $v->player_id?></td>
						<td><?php echo $v->server_name?></td>
						<td><?php echo $v->chenghao_name?></td>
						<td><?php echo $v->operate_type?></td>
						<td><?php echo $v->user_id?></td>
						<td><?php echo $v->created_at?></td>
					</tr>
	                   <?php } ?>	
					</tbody>

			</table>
		</div>
	</div>
</div>