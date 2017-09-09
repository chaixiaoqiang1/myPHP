<script src="/js/auto_input.js"></script>
<script>
	function backpackController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			$scope.formData.item_id = document.getElementById("o").value;
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/backpack',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});	
		}
	}
</script>
<div class="col-xs-12" ng-controller="backpackController">
	<div class="row">
		<div class="eb-content">
					<div class="form-group col-md-6" style="padding:0">
						<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
							<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
							<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
							<?php } ?>		
						</select>
					</div>
					<div class="form-group col-md-6">
						<input type="text" name="item_id" ng-model="formData.item_id" class="form-control" style="overflow-y:auto;" id="o" onkeyup="autoComplete.start(event)"
					     autocomplete="off" placeholder="输入要发放的物品(输入0或者1可以查看全部礼包)">
						<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>
					</div>
	
				<div class="clearfix"> </div>
				<div class="form-group">
					<div class="col-md-6" style="padding: 0">
						<input type="text" name="player_name" ng-model="formData.player_name" class="form-control" required="required" placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"/>
					</div>
					<div class="col-md-6">
						<input type="text" name="item_num" ng-model="formData.item_num" class="form-control" required="required" placeholder="请输入数量(0-10000)"/>
					</div>
				</div>
				<div><br/></div>
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit') ?>" 
				ng-click="process()"/>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>
<script>
    var autoComplete=new AutoComplete('o','auto',[<?php 
    	foreach ($items as $v) {
    		echo "'".$v->id.':' .$v->name."',";
    	} ?>
    ]);
</script>