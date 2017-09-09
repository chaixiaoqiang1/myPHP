<script src="/js/auto_input.js"></script>
<script>
	function sendGiftBagController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.is_return = 1;
		$scope.processFrom = function(url) {
			if(new Date().getTime()/1000 - $scope.click_time < 5){
				alert('两次操作请间隔5秒以上！');
				return;
			}
			if($scope.is_return == 0){
				alert('请等待游戏后端返回本次操作结果后再操作！');
				return;
			}
			$scope.click_time = new Date().getTime()/1000;
			$scope.is_return = 0;
			if ($scope.formData.giftbag_num>=10) {
				if(!confirm('确定要发送'+$scope.formData.giftbag_num+'个数量的礼包?')){
					return;
				}
			}

			alertService.alerts = $scope.alerts;
			$scope.formData.gift_bag_name = document.getElementById("o").value;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.is_return = 1;
                var success = data.ok;
                var len = success.length;
                var i;
                for (i=0; i < len; i++) {
                    alertService.add('success', success[i]);
                }
				var fail = data.fail;
				var len = fail.length;
				var i;
				for (i=0; i < len; i++) {
					alertService.add('danger', fail[i]);
				}
			}).error(function(data) {
				$scope.is_return = 1;
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="sendGiftBagController">
	<div class="row">
		<div class="col-xs-8">
				<div class="form-group" style="padding-left: 0px;width:80%;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="row" style="height: 30px;">
					<div class="form-group col-md-4">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
							ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
							<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>
					</div>
					<div class="form-group col-md-8">
						<input type="text" name="gift_bag_name" ng-model="formData.gift_bag_name" style="height:35px;font-size:12pt;overflow-y:auto;width:50%;" id="o" onkeyup="autoComplete.start(event)"
				     	autocomplete="off" placeholder="输入礼包(选择礼包后不需要输入)">
						<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>	
					</div>
					<div class="form-group col-md-4">
						<input type="number" name="giftbag_num" ng-model="formData.giftbag_num" required placeholder="发送数量">
					</div>
				</div>

				<div>
					</br>
					<label>
						<input type = "radio" ng-model = "formData.action_type" name = "action_type" ng-init = "formData.action_type=1" value="1"/>
						<?php echo Lang::get('serverapi.base_on_name')?>
					</label>

					<label>
						<input type = "radio" ng-model = "formData.action_type" name = "action_type"  value="2"/>
						<?php echo Lang::get('serverapi.base_on_player_id')?>
					</label>
				</div></br>
				<div class="form-group" style="width:80%;">
					<textarea name="player_names" ng-model="formData.player_names" required
						placeholder="<?php echo Lang::get('serverapi.gift_bag_batch') ?>"
						rows="15" class="form-control"></textarea>
				</div>
				<input type='button' class="btn btn-danger"
				       value="<?php echo Lang::get('basic.btn_submit') ?>"
				       ng-click="processFrom('/game-server-api/yysg/gift-bag')"/>
		</div>
		<!-- /.col -->
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
    	foreach ($gifts as $k => $v) {
    		echo "'".$v->id.':'.$v->name."',";
    	} ?>
    ]);
</script>