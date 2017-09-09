<script>
	function ScoreRankController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.is_show = 0;
		$scope.process = function() {
			$scope.items = {};
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/score/rank/log',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.is_show = 1;
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.show = function(){
			$scope.is_show = 0;
		};
	}
</script>
<div class="col-xs-12" ng-controller="ScoreRankController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group col-md-8">
					<select class="form-control" name="activity_type"
						id="select_activity_type" ng-model="formData.activity_type"
						ng-init="formData.activity_type=0">
						<option value="0" ng-click="show()"><?php echo Lang::get('slave.shenshu') ?></option>
						<option value="1" ng-click="show()"><?php echo Lang::get('slave.daluandou') ?></option>
						<option value="2" ng-click="show()"><?php echo Lang::get('slave.give_flower') ?></option>
						<option value="3" ng-click="show()"><?php echo Lang::get('slave.mazaitai') ?></option>
						<option value="4" ng-click="show()"><?php echo Lang::get('slave.fruit_machine') ?></option>	
						<option value="5" ng-click="show()"><?php echo Lang::get('slave.water_day') ?></option>	
						<option value="6" ng-click="show()"><?php echo Lang::get('slave.guess_rank') ?></option>	
					</select>
				</div>
				<div class="form-group col-md-8">
	                <select class="form-control" name="server_id"
	                        id="select_game_server" ng-model="formData.server_id"
	                        ng-init="formData.server_id=0" multiple="multiple"
	                        ng-multiple="true" size=12>
	                    <optgroup
	                        label="<?php echo Lang::get('serverapi.select_main_game_server') ?>">
	                        <?php foreach ($servers as $k => $v) { ?>
	                            <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
	                        <?php } ?>
	                    </optgroup>
	                </select>
	            </div>
				<div class="clearfix">
                	<br/>
            	</div>
				<div class="form-group" style="height:35px;">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:10"></quick-datepicker> 
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
			<p><font color=red>说明：查询时请选择当时开活动的主服,开活动期间每天0点会记录前一天最终的数据</font></p>	
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
	<div class="col-xs-12" ng-repeat="t in items">
		<div class="panel panel-success" ng-if="is_show==1">
			<div class="panel-heading" ng-if="is_show==1">{{t.title}}</div>
			<table class="table table-striped" ng-if="(formData.activity_type==0 || formData.activity_type==2) && is_show==1">
				<thead>
					<tr class="info">
						<td>serverName</td>
						<td>OperatorID</td>
						<td>PlayerID</td>
						<td>playerName</td>
						<td>Rank</td>
						<td>Contribute</td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="s in t.res">
						<td>{{s.ServerID}}</td>
						<td>{{s.OperatorID}}</td>
						<td>{{s.PlayerID}}</td>
						<td>{{s.Name}}</td>	
						<td>{{s.Rank}}</td>
						<td>{{s.Contribute}}</td>						
					</tr>
				</tbody>
			</table>
			<table class="table table-striped" ng-if="formData.activity_type==1 && is_show==1">
				<thead>
					<tr class="info">
						<td>serverName</td>
						<td>OperatorID</td>
						<td>PlayerID</td>
						<td>GroupID</td>
						<td>Score</td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="s in t.res">
						<td>{{s.ServerID}}</td>
						<td>{{s.OperatorID}}</td>
						<td>{{s.PlayerID}}</td>
						<td>{{s.GroupID}}</td>	
						<td>{{s.Score}}</td>						
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>