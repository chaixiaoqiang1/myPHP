<script>
	function PokerChipsRangeController($scope, $http, alertService, $filter){
		$scope.alerts = [];
	    $scope.file='';
	    $scope.start_time = null;
	    $scope.end_time = null;
	    $scope.formData = {};
		$scope.items = [];
		$scope.pagination = {};
		$scope.pagination.totalItems = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.perPage= 1;
		$scope.title_type = 'single';

		$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
			if (newPage != oldPage) {
			$scope.process(newPage);
		}
		});
		$scope.process = function(newPage){
			$scope.alerts = [];
			var tmp = $scope.formData.group_by;
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time,'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url' : '/game-server-api/poker/chips-range?page=' + newPage,
				'data' :$.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.perPage= data.per_page;
				$scope.pagination.totalItems = data.count;
				$scope.items = data.items;
				location.hash = '#top';
				if(tmp){
					$scope.title_type = 'group';
				}else{
					$scope.title_type = 'single';
				}
			}).error(function(data){
				alertService.add('danger', data.error);
			});		
		};
	}
</script>
<div class="col-xs-12" ng-controller="PokerChipsRangeController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/poker/chips-range" method="get" role="form"
				ng-submit="process(1)" onsubmit="return false;">
				<div class="form-group">
					<div class="col-md-4" style="padding-left:0px">
						<input type="text" class="form-control" ng-model="formData.player_name" name="player_name" placeholder="<?php echo Lang::get('serverapi.enter_player_name')?>">
					</div>
					<div class="col-md-4">
						<input type="text" class="form-control" ng-model="formData.player_id" name="player_id" placeholder="<?php echo Lang::get('serverapi.enter_player_id')?>">
					</div>
				</div>
				<div class="clearfix"><br/><br/></div>
				<div class="form-group">
					<div class="col-md-4" style="padding-left:0px">
						<select class="form-control" name="sort" id="select_sort"
							ng-model="formData.sort" ng-init="formData.sort=0">
							<option value="0">选择排序</option>
							<option value="asc">筹码变动升序</option>
							<option value="desc">筹码变动降序</option>
							<option value="sortaction">协议排序</option>
						</select>
					</div>
					<div class="col-md-4">
						<select class="form-control" name="mid" id="select_mid"
							ng-model="formData.mid" ng-init="formData.mid=0">
							<option value="0">选择协议</option>
							<?php foreach ($mid as $k => $v) { ?>
                            	<option value="<?php echo $v->mid ?>"><?php echo $v->mid; ?></option>
                        	<?php } ?>
						</select>
					</div>
					<div class="col-md-4" ng-show="(formData.mid != 0) && !(formData.player_name || formData.player_id)">
						<select class="form-control" name="group_by" id="group_by"
							ng-model="formData.group_by" ng-init="formData.group_by=0">
							<option value="0"><?php echo Lang::get('slave.not_group_by'); ?></option>
							<option value="1"><?php echo Lang::get('slave.group_by_player'); ?></option>
							<option value="2"><?php echo Lang::get('slave.group_by_all'); ?></option>
						</select>
					</div>
				</div>
				<div class="clearfix"><br/></div>
				<div class="form-group">
					<div class="col-md-5" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix"><br/></div>
				<div class="form-group">
					<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
				</div>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" style="padding: 0;">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get("slave.player_name");?></b></td>
					<td><b><?php echo Lang::get("slave.player_id");?></b></td>
					<td><b><?php echo Lang::get("slave.yuanbao_poker");?></b></td>
					<td><b><?php echo Lang::get("slave.diff_yuanbao_poker");?></b></td>
					<td><b><?php echo Lang::get("slave.tongqian_poker");?></b></td>
					<td><b><?php echo Lang::get("slave.diff_tongqian_poker");?></b></td>
					<td ng-show="'single'==title_type"><b><?php echo Lang::get("slave.action_type");?></b></td>
					<td ng-show="'single'==title_type"><b><?php echo Lang::get("slave.action_time");?></b></td>

					<td ng-show="'group'==title_type"><b><?php echo Lang::get("slave.player_nums");?></b></td>
					<td ng-show="'group'==title_type"><b><?php echo Lang::get("slave.times");?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.yuanbao}}</td>
					<td>{{t.diff_yuanbao}}</td>
					<td>{{t.tongqian}}</td>
					<td>{{t.diff_tongqian}}</td>
					<td>{{t.action_type}}</td>
					<td>{{t.action_time}}</td>
				</tr>
				</body>
		
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>