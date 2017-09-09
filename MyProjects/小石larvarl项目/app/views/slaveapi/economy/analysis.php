<script>
	function getEconomyAnalysisController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.start_time = null;
	    $scope.end_time = null;
	    $scope.formData.no_name=0;
		$scope.processFrom = function() {
			$scope.total={};
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/analysis',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getEconomyAnalysisController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/economy/analysis" method="get" role="form"
				ng-submit="processFrom('/slave-api/economy/analysis')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=10>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
				<div class="col-md-6" style="padding: 0">
					<select class="form-control" name="type" ng-model="formData.type"
						ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('slave.yuanbao')?></option>
						<option value="1"><?php echo Lang::get('slave.tongqian')?></option>
						<?php if('yysg' != $game_code && 'mnsg' != $game_code){?>
							<option value="2"><?php echo Lang::get('slave.gongxun')?></option>
						<?php }?>
					</select>
				</div>
				<div class="col-md-6" style="padding: 2">
					<select class="form-control" name="action_type" ng-model="formData.action_type"
						ng-init="formData.action_type=0">
						<option value="0"><?php echo Lang::get('slave.quanbu')?></option>
						<?php if('flsg' == $game_code){?>
							<option value="123456789"><?php echo Lang::get('slave.zhuangyuanzongji')?></option>
						<?php }elseif ('nszj' == $game_code) {?>
							<option value="123456789"><?php echo Lang::get('slave.zhuangyuanzongji')?></option>
							<option value="123456790"><?php echo Lang::get('slave.gold_round')?></option>
						<?php }?>
						<?php foreach ($mids as $v) {?>
							<option value="<?php echo $v['mid']?>"><?php echo $v['desc']?></option>
						<?php }?>
					</select>
				</div>
				</div>
				<div class="clearfix">
				<div class="form-group" style="height:30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix">
					<br />
				</div>
				<div class="form-group" style="height: 10px;">
				<input type="text" class="form-control"
					ng-model="formData.lower_bound" name="lower_bound"
					placeholder="<?php echo Lang::get('slave.pay_money_lower_gold') ?>" />
				</div>
				<div class="clearfix">
					<br />
				</div>
				<div calss="form-group">
					<?php if(in_array($game_code, array('mnsg','yysg'))){?>
					<div class="col-md-6">
					    <label>
					        <input type="checkbox" ng-init="formData.no_name=0" ng-true-value="1" ng-false-value="0" ng-model="formData.no_name"/>
					        不查询昵称(可以提高查询效率)
					    </label>
					</div>
					<?php }?>
					<div class="col-md-6">
						<input type="button" class="btn btn-info" style=""
							ng-click="processFrom('/slave-api/economy/analysis')" value="<?php echo Lang::get('basic.btn_show') ?>" />
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped table-hover">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.consumption_statics');?></b></td>
					<td><b><?php echo Lang::get('slave.player_id');?></b></td>
					<td ng-if="formData.no_name==0"><b><?php echo Lang::get('slave.player_name');?></b></td>
					<td><b><?php echo Lang::get('slave.server_name');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td>{{t.spend}}</td>
					<td>{{t.player_id}}</td>
					<td ng-if="formData.no_name==0">{{t.player_name}}</td>
					<td>{{t.server_name}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>