<script>
	function getPokerRoundsController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/rounds',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getPokerRoundsController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/poker/cash" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding-left: 15px ;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding-left:15px;width:50%">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<br/>
				<div class="form-group">
				    <label>
						<input type="radio" name="send_type" value="1"  ng-model="formData.send_type" ng-init="formData.send_type=1"  ng-checked="true"/>
						<?php echo Lang::get('serverapi.search_by_game_name')?>
					</label>
					<label>
						<input type="radio" ng-model="formData.send_type" name="send_type" value="2"/>
						<?php echo Lang::get('serverapi.search_by_uid')?>
					</label>
				</div>
				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="height: 30px;width:75%" ng-if="formData.send_type == 1">
					<select class="form-control" name="click_id" ng-model="formData.click_id"
						ng-init="formData.click_id=0" >
						<option value="0"><?php echo Lang::get('slave.choose_rounds');?></option>
						<?php foreach($rounds as $v){?>
							<option value="<?php echo $v->Id?>"><?php echo $v->Id."---".$v->Name.'----'.$v->Time;?></option>
						<?php }?>
					</select>
				</div>

				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="height: 30px;width:75%" ng-if="formData.send_type == 2">
					<input type="text" class="form-control" id="uid"  placeholder="<?php echo Lang::get('slave.input_uid')?>" required ng-model="formData.uid" name="uid" /> 
				</div>

				<div class="clearfix"></div>
				
				<div class="col-md-6" style="padding-left:15px">
						<div class="input-group">
							<input type="submit" class="btn btn-default" style="" value="<?php echo Lang::get('basic.btn_submit') ?>" />
						</div>
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

	<div class="col-xs-12">
		<table class="table table-striped" ng-if="formData.send_type == 1" >
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.poker_rounds_date')?></b></td>
					<td><b><?php echo Lang::get('slave.round_id')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_round_name');?></b></td>
					<td><b><?php echo Lang::get('slave.round_click')?></b></td>
					<td><b><?php echo Lang::get('slave.round_num')?></b></td>
				</tr>
			</thead>
			<tbody ng-repeat = "t in items">
				<tr ng-repeat="tt in t">
					<td>{{tt.date}}</td>
					<td>{{tt.mu_id}}</td>
					<td>{{tt.round_name}}</td>
					<td>{{tt.click_num}}</td>
					<td>{{tt.reg_num}}</td>

				</tr>
			</tbody>
		</table>	
	</div>
	<div class="col-xs-12">
		<table class="table table-striped" ng-if="formData.send_type == 2">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.poker_rounds_date')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_round_name');?></b></td>
					<td><b><?php echo Lang::get('slave.party_num')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.date}}</td>
					<td>{{t.round_name}}</td>
					<td>{{t.game_num}}</td>
					<!--<td>{{t.award_name}}</td>-->

				</tr>
			</tbody>
		</table>
		
	</div>
</div>