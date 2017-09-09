<script src="/js/auto_input.js"></script>
<script>
	function PlayerEquipmentController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			$scope.items = {};
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.table_id = document.getElementById("table_id").value;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/log/player/wj',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PlayerEquipmentController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group">
					<select class="form-control" name="choice" id="select_choice"
						ng-model="formData.choice" ng-init="formData.choice=0">
						<option value="0"><?php echo Lang::get('player.select_by_player_name') ?></option>
						<option value="1"><?php echo Lang::get('player.select_by_player_id') ?></option>
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="id_or_name"
						placeholder="输入昵称或ID"
						required ng-trim="false" ng-model="formData.id_or_name" name="id_or_name" />
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
			<div class="form-group">
					<input type="text" class="form-control" id="table_id" onkeyup="autoComplete.start(event)"
						autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_wj') ?>"
						 name="table_id" />
						<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>
			</div>
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" 
						ng-click="process()"/>
					</div>
			</div>
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
				<tr class="info">
					<td><b>玩家ID</b></td>
					<td><b>MID</b></td>
					<td><b>partner_id</b></td>
					<td><b>武将</b></td>
					<td><b>等级</b></td>
					<td><b>星数</b></td>
					<td><b>时间</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.mid}}</td>
					<td>{{t.partner_id}}</td>
					<td>{{t.table_id}}</td>
					<td>{{t.lev}}</td>
					<td>{{t.star}}</td>
					<td>{{t.created_at}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>
<script>
    var autoComplete=new AutoComplete('table_id','auto',[<?php 
    	foreach (Lang::get('yysgwj') as $k => $v) {
    		echo "'".$k.':'.$v."',";
    	} ?>
    ]);
</script>