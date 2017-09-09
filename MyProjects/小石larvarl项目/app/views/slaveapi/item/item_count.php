<script src="/js/auto_input.js"></script>
<script>
	function ItemCountController($scope, $http, alertService, $filter){
		$scope.alerts = [];
	    $scope.file='';
	    $scope.start_time = null;
	    $scope.end_time = null;
	    $scope.formData = {};
	    $scope.servers = [];
		$scope.players = [];

		$scope.SubmitForm = function(){
			$scope.servers = [];
			$scope.players = [];
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time,'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.item_name = document.getElementById("item_name").value;
			$http({
				'method' : 'post',
				'url' : '/slave-api/mg/item/count',
				'data' :$.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				$scope.servers = data.server;
				$scope.players = data.player;
			}).error(function(data){
				alertService.add('danger', data.error);
			});		
		};
	}
</script>
<div class="col-xs-12" ng-controller="ItemCountController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="SubmitForm()" onsubmit="return false;">
				<div class="form-group">
					<div class="col-md-12">
						<select class="form-control" name="servers" id="servers"
							ng-model="formData.servers" multiple="true" size="10" required>
							<optgroup label="<?php echo Lang::get('slave.select_server') ?>">
							<?php foreach ($servers as $v) { ?>
                            	<option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
                        	<?php } ?>
                        	</optgroup>
						</select>
					</div>
				</div>
				<div class="clearfix"><br/><br/></div>
				<div class="form-group">
					<div class="col-md-5">
						<select class="form-control" name="serach_type" id="serach_type"
							ng-model="formData.serach_type" ng-init="formData.serach_type=0">
							<option value="0"><?php echo Lang::get('slave.server_info'); ?></option>
							<option value="1"><?php echo Lang::get('slave.server_player_info'); ?></option>
						</select>
					</div>
					<div class="col-md-5">
						<select class="form-control" name="change_type" id="change_type"
							ng-model="formData.change_type" ng-init="formData.change_type=1">
							<option value="1"><?php echo Lang::get('slave.gain'); ?></option>
							<option value="-1"><?php echo Lang::get('slave.consume'); ?></option>
						</select>
					</div>
					<div class="col-md-5">
						<select class="form-control" name="item_id" id="item_id"
							ng-model="formData.item_id" ng-init="formData.item_id=0">
								<option value="0"><?php echo Lang::get('slave.select_item'); ?></option>
							<?php foreach ($items as $key => $value) { ?>
								<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="col-md-5">
						<input type="text" class="form-control" name="item_name" id="item_name" onkeyup="autoComplete.start(event)"
				     	autocomplete="off" placeholder="输入物品(选择物品后不需要输入)">
						<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>	
					</div>
				</div>
				<div class="form-group col-md-12" ng-if="1 == formData.serach_type">
					<span><b>Note:<?php echo Lang::get('slave.item_count_note'); ?></b></span>
				</div>
				<div class="clearfix"><br/></div>
				<div class="form-group">
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-5">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix"><br/><br/></div>
				<div class="form-group col-md-5">
					<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-primary">
				</div>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-9" style="padding: 0;">
		<table class="table table-striped" ng-show="servers">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get("slave.server_name");?></b></td>
					<td><b><?php echo Lang::get("slave.player_num");?></b></td>
					<td><b><?php echo Lang::get("slave.item_num");?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in servers">
					<td>{{t.server_name}}</td>
					<td>{{t.player_num}}</td>
					<td>{{t.item_num}}</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-striped" ng-show="players">
			<thead>
				<tr class="info">
					<td><b><?php echo Lang::get("slave.server_name");?></b></td>
					<td><b><?php echo Lang::get("slave.player_id");?></b></td>
					<td><b><?php echo Lang::get("slave.player_name");?></b></td>
					<td><b><?php echo Lang::get("slave.item_num");?></b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="p in players">
					<td>{{p.server_name}}</td>
					<td>{{p.player_id}}</td>
					<td>{{p.player_name}}</td>
					<td>{{p.item_num}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script>
    var autoComplete=new AutoComplete('item_name','auto',[<?php 
    	foreach ($items as $k => $v) {
    		echo "'".$k.':'.$v."',";
    	} ?>
    ]);
</script>