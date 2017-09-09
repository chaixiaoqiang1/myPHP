<script>
	function YuanbaoRankSearchController($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time,'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.items = data;
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="YuanbaoRankSearchController">
	<div class="row">
	<div class="eb-content">
		<form class="form-group" ng-submit="process('/slave-api/payment/order/search-cehua')" onsubmit="return false">
			<div class="form-group">
			    <select class="form-control" name="server_id"
			            id="select_game_server" ng-model="formData.server_id"
			            ng-init="formData.server_id=0" multiple="multiple"
			            ng-multiple="true" size=10>
			        <optgroup
			            label="<?php echo Lang::get('serverapi.select_game_server') ?>(按住Ctrl可多选)">
			            <?php foreach ($servers as $k => $v) { ?>
			                <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name.' ['.Lang::get('slave.open_server_time').'] '.date("Y-m-d H:i:s",$v->open_server_time);?></option>
			            <?php } ?>
			        </optgroup>
			    </select>
			</div>
			<div class="form-group">
				<input type="radio" name="time_type" value="1" ng-value="1" ng-model="formData.time_type" ng-init="formData.time_type=1"/>
				<?php echo Lang::get('serverapi.from_open_server');?>
				<input type="radio" ng-model="formData.time_type" name="time_type" value="2" ng-value="2"/>
				<?php echo Lang::get('serverapi.from_select_time');?>
			</div>
			<div class="form-group" style="height: 30px;">
			<div class="col-md-6" style="padding-left: 0px ;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="col-md-6" style="padding-left:15px;width:50%">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
			<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
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
				<tr class="info">
					<td><b><?php echo Lang::get('slave.server_name');?></b></td>
					<td><b><?php echo Lang::get('slave.player_uid');?></b></td>
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
					<td><b><?php echo Lang::get('slave.vip_level');?></b></td>
					<td><b><?php echo Lang::get('slave.order_recharge_yuanbao');?></b></td>
					<td><b><?php echo Lang::get('slave.order_recharge_dollar');?></b></td>
					<td><b><?php echo Lang::get('slave.no_recharege_days');?></b></td>
					<td><b><?php echo Lang::get('slave.no_visit_days');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.uid}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.vip_level}}</td>
					<td>{{t.total_yuanbao_amount|number:2}}</td>
					<td>{{t.total_dollar_amount|number:2}}</td>
					<td>{{t.no_recharge_days}}</td>
					<td>{{t.no_visit_days}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>