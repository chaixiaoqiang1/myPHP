<script src="/js/auto_input.js"></script>
<script>
	function sendGiftBagController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.gift_bag_name = document.getElementById("gift_bag_name").value;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
                alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="sendGiftBagController">
	<div class="row">
		<div class="col-xs-10">
			<form action="" method="" role="form"
				ng-submit="processFrom('/game-server-api/yysg/mail-gift')"
				onsubmit="return false;">

				<div class="form-group col-md-4">
					<label>
						<input type = "radio" ng-model = "formData.action_type" name = "action_type" ng-init = "formData.action_type=2" value="1"/>
						<?php echo Lang::get('serverapi.base_on_name')?>
					</label>
					<label>
						<input type = "radio" readonly ng-model = "formData.action_type" name = "action_type"  value="2"/>
						<?php echo Lang::get('serverapi.base_on_player_id')?>
					</label>
				</div></br>
				<div class="form-group" style="width:80%;">
					<textarea name="players" ng-model="formData.players" required
						placeholder="<?php echo Lang::get('serverapi.gift_bag_batch') ?>"
						rows="15" class="form-control"></textarea>
				</div>

				<div class="form-group" style="width:80%;">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.enter_mail_title')?>"
							required ng-model="formData.mail_title" name="mail_title"?>
				</div>

				<div class="clearfix"></div>
				<div class="form-group" style="width:80%;">
					<textarea type="text" class="form-control" id="mail_body"
						placeholder="<?php echo Lang::get('serverapi.enter_mail_body') ?>"
						required ng-model="formData.mail_body" name="mail_body" rows="8"></textarea>
				</div>

				<div class="clearfix"></div>
				<div class="form-group">
					<div class="form-group col-md-3" style="padding: 0 0 0 0">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
							ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
							<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>
					</div>
					<div class="form-group col-md-3">
						<input type="text" class="form-control" name="gift_bag_name" ng-model="formData.gift_bag_name" id="gift_bag_name" onkeyup="autoComplete.start(event)"
				     	autocomplete="off" placeholder="输入礼包(选择礼包后不需要输入)">
						<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>	
					</div>
					<div class="form-group col-md-3">
						<input type="number" class="form-control" required name="available_time" ng-model="formData.available_time" id="available_time" placeholder="有效可领取时间(天)">
					</div>
				</div>

				<div class="clearfix"></div>
				<div class="form-group" style="width:80%;">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.enter_writer')?>"
							required ng-model="formData.writer" name="writer"?>
				</div>

				<input type="submit" class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
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
    var autoComplete=new AutoComplete('gift_bag_name','auto',[<?php 
    	foreach ($gifts as $k => $v) {
    		echo "'".$v->id.':'.$v->name."',";
    	} ?>
    ]);
</script>