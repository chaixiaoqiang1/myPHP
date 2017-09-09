<script src="/js/auto_input.js"></script>
<script>
	function LogPlayerWjController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			$scope.formData.yysgwj_id = document.getElementById("yysgwj_id").value.split(":")[0];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/log/wj/is_eat',
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
<div class="col-xs-12" ng-controller="LogPlayerWjController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group col-md-3" style="padding: 0">
					<input type="text" class="form-control" id="player_id" style="padding: 0"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" />
				</div>
				<div class="form-group col-md-3">
								<select class="form-control" name="yysgwj_id_name"
									id="yysgwj_id_name"
									ng-model="formData.yysgwj_id_name"
									ng-init="formData.yysgwj_id_name=0">
									<option value="0"><?php echo Lang::get('serverapi.select_wj') ?></option>
							<?php foreach ($yysgwj as $k => $v) { ?>
							<option value="<?php echo $k?>"><?php echo $k.' :　'.$v;?></option>
						<?php } ?>	
					</select>
				</div>
				<div class="form-group col-md-6">
					<input type="text" class="form-control" style="padding: 0;height:30px;font-size:12pt;overflow-y:auto;" id="yysgwj_id" onkeyup="autoComplete.start(event)"
						autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_wj') ?>"
						 ng-model="formData.yysgwj_id" name="yysgwj_id" />
						<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>
				</div>
				<div class="clearfix">
                	<br/>
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
			<br>
			<br>
			<p><font color=red>说明：选择武将和输入武将ID二选一即可，建议输入ID,二者都没有值时查询该玩家的所有武将被吃记录</font></p>	
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
					<td><b>ParterID</b></td>
					<td><b>被喂养武将</b></td>
					<td><b>喂养后级别</b></td>
					<td><b>喂养后星级</b></td>
					<td><b>被吃武将</b></td>
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
					<td>{{t.material_table_ids}}</td>
					<td>{{t.created_at}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>
<script>
    var autoComplete=new AutoComplete('yysgwj_id','auto',[<?php 
    	foreach ($yysgwj as $k => $v) {
    		echo "'".$k.':'.$v."',";
    	} ?>
    ]);
</script>