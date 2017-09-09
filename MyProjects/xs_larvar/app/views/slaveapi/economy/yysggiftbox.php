<script>
	function GiftBoxController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		//pagination

		$scope.processFrom = function() {
	    	$scope.alerts = [];
	        alertService.alerts = $scope.alerts;
	        $scope.sqls = [];
	        $scope.formData.dealsqls = 0;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
	        $http({
	            'method': 'post',
	            'url': '/slave-api/yysg/giftbox',
	            'data': $.param($scope.formData),
	            'headers': {
	                'Content-Type': 'application/x-www-form-urlencoded'
	            }
	        }).success(function(data) {
	        	if('' != data.info){
	        		alertService.add('success', data.info);
	        	}
	        	$scope.keywords = {};
	    		$scope.sqlresult = {};
	            $scope.keywords = data.keywords;
	            $scope.sqlresult = data.sqlresult;
	        }).error(function(data) {
	        	$scope.keywords = {};
	    		$scope.sqlresult = {};
	            alertService.add('danger', data.error);
	        });
	    };
	}
</script>
<div class="col-xs-12" ng-controller="GiftBoxController">
	<div class="row" id="top">
		<div class="col-xs-5">
			<form action="/slave-api/economy/yysg/player" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				  <div class="form-group col-md-5">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-5">
					<input type="text" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" />
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="table_id"
						id="table_id" ng-model="formData.table_id"
						ng-init="formData.table_id=0">
						<option value="0"><?php echo Lang::get('slave.enter_table_id') ?></option>
						<?php foreach ($giftbox as $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.':'.$v->name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-5" ng-if="formData.table_id==0">
					<input type="text" class="form-control" id="table_id_input"
						placeholder="<?php echo Lang::get('slave.enter_table_id') ?>"
						 ng-model="formData.table_id_input" name="table_id_input" />
				</div>
				<div class="form-group col-md-5" ng-if="formData.table_id!=0">
					<input type="text" class="form-control" id="table_id_input"
						placeholder="<?php echo Lang::get('slave.enter_table_id') ?>"
						 ng-model="formData.table_id_input" name="table_id_input" disabled />
				</div>
				<div class="form-group col-md-5">
					<select class="form-control" name="action_type_num"
						id="action_type_num" ng-model="formData.action_type_num"
						ng-init="formData.action_type_num=0">
						<option value="0"><?php echo Lang::get('slave.action_type_num') ?></option>
						<?php foreach ($mid as $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->desc;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-5" ng-if="formData.action_type_num==0">
					<input type="text" class="form-control" id="action_type_num_input"
						placeholder="<?php echo Lang::get('slave.action_type_num') ?>"
						 ng-model="formData.action_type_num_input" name="action_type_num_input" />
				</div>
				<div class="form-group col-md-5" ng-if="formData.action_type_num!=0">
					<input type="text" class="form-control" id="action_type_num_input"
						placeholder="<?php echo Lang::get('slave.action_type_num') ?>"
						 ng-model="formData.action_type_num_input" name="action_type_num_input" disabled />
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>

				<input type="submit" class="btn btn-success" style="margin-left:20px;"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />

			</form>
		</div>
	</div>
	<br>
	<div class="col-xs-8">
		<alert ng-repeat="alert in alerts" type="alert.type"
			close="alert.close()">{{alert.msg}}</alert>
	</div>

	<div class="col-xs-8">
		<table class="table table-striped">
			<thead>
				<tr class="info" ng-repeat="t in keywords">
				<?php
					for($i=0;$i<20;$i++){
					?><td>{{t.key<?php echo $i;?>}}</td><?php
					} 
				?>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="s in sqlresult">
					<?php
					for($i=0;$i<20;$i++){
					?><td>{{s.key<?php echo $i;?>}}</td><?php
					} 
				?>
				</tr>
			</tbody>
		</table>
	</div>
</div>