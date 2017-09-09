<script>

	function getChipsInfoController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		$scope.itemsPerPage = 30;
		$scope.currentPage = 0; 

		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/poker/chip-info',
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
<div ng-app = "yApp">
<div class="col-xs-12" ng-controller="getChipsInfoController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/poker/cash" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<br/>
				<div class="clearfix"></div>

				<div class="clearfix"></div>
				<div class="form-group col-md-6" style="height: 30px;width:100%" >
					<input type="text" class="form-control" id="chips"  placeholder="<?php echo Lang::get('serverapi.write_chips_10000')?>" required ng-model="formData.chips" name="chips" /> 
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
					<td><b><?php echo Lang::get('slave.poker_uid')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_nickname');?></b></td>
					<td><b><?php echo Lang::get('slave.poker_chips_num')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_fb_id')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_created_time')?></b></td>
					<td><b><?php echo Lang::get('slave.poker_is_recharge')?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.uid}}</td>
					<td>{{t.nickname}}</td>
					<td>{{t.chips}}</td>
					<td>{{t.fb_id}}</td>
					<td>{{t.create_time}}</td>
					<td>{{t.recharge_num}}</td>
					
				</tr>
			</tbody>
			<!--<tfoot>
				<div class="pagebar" style="margin-bottom:0px;">
			        <button class="btn btn-info" type="button" ng-disabled="currentPage == 0" ng-click="currentPage = currentPage - 1">上一页</button>
			        <button class="btn btn-info" type="button" ng-disabled="currentPage == pageNum() - 1" ng-click="currentPage = currentPage + 1">下一页</button>
			    </div>
			</tfoot>-->
		</table>	
	</div>
</div>
</div>