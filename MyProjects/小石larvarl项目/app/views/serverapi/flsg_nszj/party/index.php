<script>
	function getPartyMemberController($scope, $http, alertService) {
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
				if (data.error == "没有数据") {
					alertService.add('danger', data.error);
				}else{
					$scope.items = data;
				}
				//alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getPartyMemberController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="processFrom('/game-server-api/party')" onsubmit="return false;">
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
					<input type="number" class="form-control" min="0"
						ng-model="formData.party_id" name="party_id" required
						placeholder="<?php echo Lang::get('serverapi.enter_party_id') ?>" />
				</div>
				<div class="clearfix">
					<br />
					<input type="submit" class="btn btn-success" style="width:100px; font-weight:bold;"
						value=" <?php echo Lang::get('basic.btn_search') ?> " />
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

	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table class="table table-striped table-hover" cellpadding="0" cellspacing="0" border="0" >
				<thead>
					<tr class="info">
						<td><?php echo Lang::get('serverapi.player_id') ?></td>
						<td><?php echo Lang::get('serverapi.player_id') ?></td>
						<td><?php echo Lang::get('serverapi.player_id') ?></td>
					</tr>
				</thead>
				<tbody>
		             <tr ng-repeat="t in items">
						<td >{{t[0]}}</td>
						<td >{{t[1]}}</td>
						<td >{{t[2]}}</td>
					</tr>	
				</tbody>

			</table>
		</div>
	</div>

</div>