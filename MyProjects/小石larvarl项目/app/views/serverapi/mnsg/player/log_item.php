<script src="/js/auto_input.js"></script>
<script>
	function LogSearchController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.process = function(url) {
			$scope.items = [];
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.table_id = document.getElementById("table_id").value;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
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
	}
</script>
<div class="col-xs-12" ng-controller="LogSearchController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process('/game-server-api/mnsg/log/item')" onsubmit="return false;">
				<div class="form-group col-md-8">
					<input type="text" class="form-control" id="player_id"
						required placeholder="<?php echo Lang::get('slave.enter_player_id') ?>"
						 ng-model="formData.player_id" name="player_id" 
						 <?php if($player_id){ ?>ng-init="formData.player_id=<?php echo $player_id; ?>"<?php } ?>
						 />
				</div>
				<div class="form-group col-md-8">
					<input type="text" class="form-control" id="table_id" onkeyup="autoComplete.start(event)"
						autocomplete="off" placeholder="<?php echo Lang::get('slave.enter_table_id') ?>"
						 name="table_id" />
					<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>
				</div>
				<div class="form-group col-md-8" style="height:35px;">
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
				<div class="form-group col-md-8">
					<div class="input-group">
						<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" />
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
					<td><b><?php echo Lang::get('slave.player_id')?></b></td>
					<td><b><?php echo Lang::get('slave.oper_time');?></b></td>
					<td><b><?php echo Lang::get('slave.user_operator');?></b></td>
					<td><b><?php echo Lang::get('slave.item_name');?></b></td>
					<td><b><?php echo ('yysg' == $game_code ? Lang::get('slave.item_left_num') : Lang::get('slave.item_num'));?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.created_at}}</td>
					<td>{{t.mid}}</td>
					<td>{{t.table_id}}</td>
					<td>{{t.num}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>
<script>
    var autoComplete=new AutoComplete('table_id','auto',[<?php 
    	foreach ($items_data as $k => $v) {
    		echo "'".$k.':'.$v."',";
    	} ?>
    ]);
</script>