<script>
	function PokerDailyData($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.singleday = 0;
			$scope.formData.partdata=3;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.allitems = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}

		$scope.singleday = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.singleday = 1;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}

		$scope.switch = function(){
			$scope.formData.partdata = ($scope.formData.partdata)%3 +1;
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerDailyData">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process('/slave-api/joyspade/daily/data')" onsubmit="return false;">
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
				
				<div class="col-md-3" style="padding: 0">
						<div class="input-group">
							<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" />
						</div>
				</div>
				<div class="col-md-3" style="padding: 0">
						<div class="input-group">
							<input type="button" class="btn btn-default" value="切换显示" ng-click="switch()" />
						</div>
				</div>
				<div class="col-md-3" style="padding: 0">
						<div class="input-group">
							<input type="button" class="btn btn-default" value="查询单日详细数据" ng-click = "singleday('/slave-api/joyspade/daily/data')" />
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
	<div class="col-xs-12" ng-if="formData.singleday == 1">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>操作名称</b></td>
					<td><b>筹码变动</b></td>
					<td><b>操作名称</b></td>
					<td><b>筹码变动</b></td>
					<td><b>结余值</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="o in items">
					<td>{{o.in.action_name}}---{{o.in.type}}</td>
					<td>{{o.in.diff_chip}}</td>
					<td>{{o.out.action_name}}---{{o.out.type}}</td>
					<td>{{o.out.diff_chip}}</td>
					<td>{{o.sum}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
	<div class="col-xs-12" ng-if="formData.singleday == 0">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><?php echo "日期";?></td>
					<td colspan="1" ng-if="formData.partdata!=3">牌局回收</td>
					<td colspan="1" ng-if="formData.partdata!=3"><?php echo Lang::get("pokerData.playSlot") ?></td>
					<td colspan="1" ng-if="formData.partdata!=3"><?php echo Lang::get("pokerData.betRedBlackCard") ?></td>
					<td colspan="1" ng-if="formData.partdata!=3"><?php echo Lang::get("pokerData.betLuckyCard") ?></td>
					<td colspan="1" ng-if="formData.partdata!=3"><?php echo Lang::get("pokerData.betLuckyPool") ?></td>
					<td colspan="1" ng-if="formData.partdata!=3"><?php echo Lang::get("pokerData.startSpinAndGo") ?></td>
					<td colspan="1" ng-if="formData.partdata!=3"><?php echo Lang::get("pokerData.startSitAndGo") ?></td>
					<td colspan="1" ng-if="formData.partdata!=3"><?php echo Lang::get("pokerData.regMU") ?></td>
					<td colspan="1" ng-if="formData.partdata!=3">其他</td>

					<td colspan="2" ng-if="formData.partdata==3">牌局回收</td>
					<td colspan="2" ng-if="formData.partdata==3"><?php echo Lang::get("pokerData.playSlot") ?></td>
					<td colspan="2" ng-if="formData.partdata==3"><?php echo Lang::get("pokerData.betRedBlackCard") ?></td>
					<td colspan="2" ng-if="formData.partdata==3"><?php echo Lang::get("pokerData.betLuckyCard") ?></td>
					<td colspan="2" ng-if="formData.partdata==3"><?php echo Lang::get("pokerData.betLuckyPool") ?></td>
					<td colspan="2" ng-if="formData.partdata==3"><?php echo Lang::get("pokerData.startSpinAndGo") ?></td>
					<td colspan="2" ng-if="formData.partdata==3"><?php echo Lang::get("pokerData.startSitAndGo") ?></td>
					<td colspan="2" ng-if="formData.partdata==3"><?php echo Lang::get("pokerData.regMU") ?></td>
					<td colspan="2" ng-if="formData.partdata==3">>其他</td>
				</tr>
				<tr>
					<td><?php echo " ";?></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>回收</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>发放</nobr></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="a in allitems">
					<td><nobr>{{a.date}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.getback.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.getback.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.playSlot.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.playSlot.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.betRedBlackCard.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.betRedBlackCard.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.betLuckyCard.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.betLuckyCard.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.betLuckyPool.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.betLuckyPool.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.startSpinAndGo.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.startSpinAndGo.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.startSitAndGo.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.startSitAndGo.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.regMU.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.regMU.out.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==2||formData.partdata==3"><nobr>{{a.other.in.diff_chip}}</nobr></td>
					<td ng-if="formData.partdata==1||formData.partdata==3"><nobr>{{a.other.out.diff_chip}}</nobr></td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>