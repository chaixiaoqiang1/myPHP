<script>
	function delayOrder($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.doneIndex = [];
		$scope.isExist = function(order_sn){
			var i = 0;
			for(i; i < $scope.doneIndex.length; i++)
				if(order_sn == $scope.doneIndex[i])
					return 0;
			return 1;
		};
		$scope.process = function(url,order_sn,user_id,yesORno,code) {
			if(order_sn)
				$scope.doneIndex.push(order_sn);
			$scope.formData.order_sn = order_sn;
			$scope.formData.user_id = user_id;
			$scope.formData.yesORno = yesORno;
			$scope.formData.code = code;
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if(!yesORno)
					$scope.items = data;
				
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};

		$scope.processCheck = function(url, is_check) {
			$scope.formData.is_check = is_check;
			alertService.alerts = $scope.alerts;
			$scope.items = {};
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
		};
		
		
	}
</script>
<div id='query' class="col-xs-12" ng-controller="delayOrder">
	<div class="row">
		<div class="col-xs-12">
			<div class="clearfix">
				<br />
			</div>
			<br>

			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-primary"
					value="<?php echo '查询' ?>"
					ng-click="processCheck('/game-server-api/poker/delayOrder', 0)" />
			</div>
			<div class="col-md-4">
				<input type='button' class="btn btn-primary"
					value="<?php echo '查询7天内操作记录' ?>"
					ng-click="processCheck('/game-server-api/poker/delayOrder', 1)" />
			</div>

			<div class="col-xs-12" style="margin-top:10px">
				<table class="table table-striped">
					<thead>
						<tr class="info">
							<td>订单号</td>
							<td>用户ID</td>
							<td>游戏次数</td>
							<td>成功充值量</td>
							<td>退款数额</td>
							<td>今天充值量</td>
							<td>首次充值量</td>
							<td>订单时间</td>
							<td>处理状态</td>
							<td>code</td>
						</tr>
					</thead>
					<tbody>
						
						<tr ng-repeat="t in items" ng-if = "isExist(t.order_sn)">
							<td>{{t.order_sn}}</td>
							<td>{{t.user_id}}</td>
							<td>{{t.play_times}}</td>
							<td>{{t.success_amount}}</td>
							<td>{{t.dispute_amount}}</td>
							<td>{{t.today_amount}}</td>
							<td>{{t.first_amount}}</td>
							<td>{{t.delay_time*1000 | date: 'yyyy-MM-dd HH:mm:ss'}}</td>
							<td ng-if="t.deal_status==0">未处理</td>
							<td ng-if="t.deal_status==1">未通过</td>
							<td ng-if="t.deal_status==2">通过</td>
							<td>{{t.code}}</td>
							<td ng-if="t.deal_status==0"><input type='button' class="btn btn-primary"
									   value="<?php echo '通过' ?>"
								       ng-click="process('/platform-api/payment/delayOrder',t.order_sn,t.user_id,2,t.code)" /></td>
							<td ng-if="t.deal_status==0"><input type='button' class="btn btn-primary"
								       value="<?php echo '不通过' ?>"
									   ng-click="process('/platform-api/payment/delayOrder',t.order_sn,t.user_id,1,t.code)" /></td>

						</tr>
					<tbody>
					
				</table>
			</div>

		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>
