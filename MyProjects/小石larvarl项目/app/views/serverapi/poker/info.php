<script>
	function getPokerInfoController($scope, $http, alertService, $filter) {
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
				'url'	 : '/slave-api/poker/info',
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
<div class="col-xs-12" ng-controller="getPokerInfoController">
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
				<!--<div class="form-group" style="margin-left:15px">
				    <label>
						<input type="radio" name="send_type" value="1"  ng-model="formData.send_type" ng-init="formData.send_type=1"  ng-checked="true"/>
						<?php echo Lang::get('serverapi.search_by_blind_type')?>
					</label>
					<label>
						<input type="radio" ng-model="formData.send_type" name="send_type" value="2"/>
						<?php echo Lang::get('serverapi.search_by_uid')?>
					</label>
				</div>-->
				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="height: 30px;width:75%" >
					<select class="form-control" name="blind_type" ng-model="formData.blind_type"
						ng-init="formData.blind_type=0" >
						<option value="0"><?php echo Lang::get('slave.choose_blind');?></option>
						<?php    foreach($blinds as $blind){?>
						<option value="<?php echo $blind->id?>"><?php echo $blind->blind?></option>
						<?php } ?>
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
		<table class="table table-striped" >
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.poker_rounds_date')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num1');?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num2')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num3')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num4')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num5')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num6')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_num7')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.date}}</td>
					<td>{{t.num1}}</td>
					<td>{{t.num2}}</td>
					<td>{{t.num3}}</td>
					<td>{{t.num4}}</td>
					<td>{{t.num5}}</td>
					<td>{{t.num6}}</td>
					<td>{{t.num7}}</td>
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

				</tr>
			</tbody>
		</table>
		
	</div>
</div>