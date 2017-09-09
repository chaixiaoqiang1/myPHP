<script src="/js/auto_input.js"></script>
<script>
	function setMountController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url) {
			$scope.formData.mount = document.getElementById("o").value;
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/players/mount',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
		};
	}
</script>
<div class="col-xs-12" ng-controller="setMountController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group col-md-6" style="padding:0;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0">请选择服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group col-md-6">
					<select class="form-control" name="choice"
						id="select_game_server" ng-model="formData.choice"
						ng-init="formData.choice=1">
						<option value="0"><?php echo Lang::get('serverapi.playerName_set')?></option>
						<option value="1"><?php echo Lang::get('serverapi.playerID_set')?></option>	
					</select>
				</div>
				<div class="form-group col-md-6" style="padding:0;">
					<input type="text" class="form-control" id="name_or_id"
						placeholder="<?php echo Lang::get('player.enter_id_or_name') ?>"
						required ng-model="formData.name_or_id" name="name_or_id" />
				</div>
				<div class="form-group col-md-6">
					<input type="text" name="mount" ng-model="formData.mount" class="form-control" style="overflow-y:auto;" id="o" onkeyup="autoComplete.start(event)"
					autocomplete="off" placeholder="请输入坐骑">
					<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>
				</div>
				<b>选择添加或删除</b>
				<div class="form-group">
					<select class="form-control" name="is_mount"
							id="is_mount" ng-model="formData.is_mount"
							ng-init="formData.is_mount=0">
							<option value="0">添加坐骑</option>
							<option value="1">删除坐骑</option>
					</select>
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="button" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" 
						ng-click="process()"/>
					</div>
			</div>
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
    	foreach ($mount as $value) {
    		echo "'".$value['name']. ':'.$value['mountid']."',";
    	} ?>
    ]);
</script>