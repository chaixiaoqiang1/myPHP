<script>
	function getPokerCashController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		
		$scope.processFrom = function(newPage) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/cash',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.update = function(t) {
			//
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/cash-update?id='+t.id,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', "修改成功==="+data.res);		
			}).error(function(data) {
				alertService.add('danger', "修改失败==="+data.error_description +"=="+data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getPokerCashController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/poker/cash" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				<div class="form-group col-md-6" style="width:50%">
					<input type="text" class="form-control" id="player_name"
						placeholder="<?php echo Lang::get('slave.enter_player_name') ?>"
						 ng-model="formData.player_name" name="player_name" />
				</div>
				<div class="form-group col-md-6" style="width:50%">
					<input type="text" class="form-control" id="player_id"
						placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" />
				</div>
				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="height: 30px; width:50%">
					<select class="form-control" name="type1" ng-model="formData.type1"
						ng-init="formData.type1=0" >
						<option value="0"><?php echo Lang::get('slave.choose_type');?></option>
						<?php foreach($award as $v){?>
							<option value="<?php echo $v->Id?>"><?php echo $v->Id."---".$v->Name;?></option>
						<?php }?>
					</select>
				</div>
				<div class="form-group col-md-6" style="width:50%">
					<select class="form-control" name="type2" ng-model="formData.type2"
						ng-init="formData.type2=0">
						<option value="0"><?php echo Lang::get('slave.cash_select')?></option>
						<option value="1"><?php echo Lang::get('slave.cash_undone')?></option>
						<option value="2"><?php echo Lang::get('slave.cash_done')?></option>
					</select>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding-left: 15px">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding-left:15px">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
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
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get('slave.poker_id')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_user_id')?></b></td>
					<td><b><?php echo Lang::get('slave.player_name');?></b></td>
					<td><b><?php echo Lang::get('slave.cash_award')?></b></td>
					<td><b><?php echo Lang::get('slave.cash_award_amount')?></b></td>
					<td><b><?php echo Lang::get('slave.cash_create_time');?></b></td>
					<td><b><?php echo Lang::get('slave.cash_award_time');?></b></td>
					
					<td><b><?php echo Lang::get('slave.cash_award_status');?></b></td>
					<td><b><?php echo Lang::get('slave.cash_goods_id');?></b></td>
					<td><b><?php echo Lang::get('slave.cash_domain_name');?></b></td>
					<td><b><?php echo Lang::get('slave.poker_user_name');?></b></td>
					<td><b><?php echo Lang::get('slave.cash_contact_name');?></b></td>
					<td><b><?php echo "province";?></b></td>
					<td><b><?php echo "city";?></b></td>
					<td><b><?php echo "county";?></b></td>
					<td><b><?php echo "village";?></b></td>
					<td><b><?php echo Lang::get('slave.cash_address');?></b></td>
					<td><b><?php echo Lang::get('slave.cash_mobile');?></b></td>
					<td><b><?php echo Lang::get('slave.cash_operate');?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.id}}</td>	
					<td>{{t.uid}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.award_name}}</td>
					<td>{{t.award_amount}}</td>
					<td>{{t.create_time}}</td>
					<td>{{t.get_time}}</td>
					
					<td>{{t.status}}</td>
					<td>{{t.goods_id}}</td>
					<td>{{t.domain_name}}</td>
					<td>{{t.name}}</td>
					<td>{{t.contact_email}}</td>
					<td>{{t.province}}</td>
					<td>{{t.city}}</td>
					<td>{{t.county}}</td>
					<td>{{t.village}}</td>
					<td>{{t.address}}</td>
					<td>{{t.mobile}}</td>
					<td><button ng-click="update(t)" class="btn btn-default"><?php echo Lang::get('serverapi.poker_update_status')?></button></td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>