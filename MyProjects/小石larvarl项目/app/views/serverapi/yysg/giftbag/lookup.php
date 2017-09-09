<script>
	function getPlayerEconomyController1($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.formData = {};
		$scope.items = [];
		$scope.pagination = {};
		//pagination
		$scope.pagination.totalItems = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.perPage= 1;

		$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
			if ($scope.formData.server_id > 0) {
				$scope.processFrom(newPage);
			}
		});
		$scope.processFrom = function(newPage) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/giftbag/lookuppage?page=' + newPage,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.perPage= data.per_page;
				$scope.pagination.totalItems = data.count;
				$scope.items = data.items;
				location.hash = '#top';
				refresh();
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}

	function timestamptostr(timestamp) {
	 var unixTimestamp = new Date(timestamp * 1000) ;
	  // return unixTimestamp.toLocaleString();
	 var d = new Date(timestamp * 1000);
	 var jstimestamp = (d.getFullYear())+"-"+(d.getMonth()+1)+"-"+
	(d.getDate())+" "+(d.getHours()-8)+":"+(d.getMinutes())+":"
	+(d.getSeconds());
	     return jstimestamp;
	 }
</script>
<div class="col-xs-12" ng-controller="getPlayerEconomyController1">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/game-server-api/giftbag/lookuppage" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">
				<?php if('yysg' != $game_code){?>
					<?php if('mnsg' == $game_code){?>
					<input type="text" ng-model="formData.app_id" name="app_id" ng-init="formData.app_id=146"
					style="display:none;" />
					<?php }else{?>
						<div class="form-group">
							<select class="form-control" name="app_id" id="app_id"
									ng-model="formData.app_id" ng-init="formData.app_id=<?php echo $init_app_id; ?>">
									<option value="0"><?php echo Lang::get('serverapi.select_gift_by_appname') ?></option>
								<?php foreach ($app_names as $k => $v) { ?>
								<option value="<?php echo $k?>"><?php echo $k . ':' . $v;?></option>
								<?php } ?>		
							</select>
						</div>
					<?php }?>
					<div class="form-group" ng-if="formData.app_id==33 || formData.app_id==34">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
								ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
								<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
								<option value="-1">所有礼包</option>
							<?php foreach ($items as $k => $v) { ?>
							<option value="<?php echo $v['id']?>"><?php echo $v['id'] . ':' . $v['name'];?></option>
							<?php } ?>		
						</select>
					</div>
					<div class="form-group" ng-if="formData.app_id==64 || formData.app_id==193">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
								ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
								<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
								<option value="-1">所有礼包</option>
							<?php foreach ($gifts as $k => $v) { ?>
							<option value="<?php echo $v['id']?>"><?php echo $v['id'] . ':' . $v['name'];?></option>
							<?php } ?>		
						</select>
					</div>
					<div class="form-group" ng-if="formData.app_id==146">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
								ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
								<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
								<option value="-1">所有礼包</option>
							<?php foreach ($awards as $k => $v) { ?>
							<option value="<?php echo $v['id']?>"><?php echo $v['id'] . ':' . $v['cname'];?></option>
							<?php } ?>
							<?php foreach ($items as $k => $v) { ?>
							<option value="<?php echo $v['id']?>"><?php echo $v['id'] . ':' . $v['name'];?></option>
							<?php } ?>
							<?php if('nszj' == $game_code) foreach ($marks as $k => $v) { ?>
							<option value="<?php echo $v['markid']?>"><?php echo $v['markid'] . ':' . $v['markname'];?></option>
							<?php } ?>		
						</select>
					</div>
				<?php }else{?>
					<div class="form-group">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
								ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
								<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
								<option value="-1">所有礼包</option>
							<?php foreach ($items as $k => $v) { ?>
							<option value="<?php echo $v['id']?>"><?php echo $v['id'] . ':' . $v['name'];?></option>
							<?php } ?>		
						</select>
					</div>
				<?php }?>
				<div class="col-md-7" style="padding-left: 0;">
					<input type="text" class="form-control" id="operator"
						placeholder="操作人，不输入默认为所有人(勿输入多个人名)"
						 ng-model="formData.operator" name="operator" />
				</div>
				<div class="col-md-5">
					<input type="text" class="form-control" id="player_id"
						placeholder="player_id(不输则查询所有玩家)"
						 ng-model="formData.player_id" name="player_id" />
				</div>
				<div class="clearfix"></div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px; padding-top: 10px;">
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

				<div class="form-group">
					<input type="radio" name="look_type"
						ng-model="formData.look_type" value="1" ng-value="1"
						ng-init="formData.look_type=1" />
					详细查询
					<input type="radio" name="look_type"
						ng-model="formData.look_type" value="2" ng-value="2" />
					汇总查询
				</div>

				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px">说明：请准确选定查询条件，汇总查询时操作时间以及玩家名称无意义，操作时间请以选定时间段为准。<br>注意：汇总查询时发送状态无意义。</span>
				</div>

				<input type="submit" class="btn btn-default" style="width:150px;background:#faa"
					value="查询" />

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
					<td ng-if="formData.look_type==1"><b>操作时间</b></td>
					<td><b>礼包ID</b></td>
					<td ng-if="formData.look_type==1"><b><?php echo Lang::get('slave.server_name'); ?></b></td>
					<td ng-if="formData.look_type==1"><b>玩家名称</b></td>
					<td ng-if="formData.look_type==1"><b>玩家ID</b></td>
					<td><b>操作人</b></td>
					<td ng-if="formData.app_id!=146 && formData.look_type==2"><b>数量</b></td>
					<td ng-if="formData.app_id==146 && formData.look_type==2"><b>发送次数</b></td>
					<td ng-if="formData.look_type==1"><b>发送状态|发送数量</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td class="operate_time" ng-if="formData.look_type==1">{{t.operate_time}}</td>
					<td>{{t.giftbag_id}}</td>
					<td ng-if="formData.look_type==1">{{t.server_name}}</td>
					<td ng-if="formData.look_type==1">{{t.player_name}}</td>
					<td ng-if="formData.look_type==1">{{t.player_id}}</td>
					<td>{{t.operator}}</td>
					<td ng-if="formData.look_type==2">{{t.count}}</td>
					<td ng-if="formData.look_type==1">{{t.extra_msg}}</td>
				</tr>
			</tbody>
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>