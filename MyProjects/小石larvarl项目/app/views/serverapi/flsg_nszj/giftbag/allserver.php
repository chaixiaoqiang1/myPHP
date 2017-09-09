<script src="/js/auto_input.js"></script>
<script>
	function sendGiftBagController($scope, $http, alertService, $filter)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			/*var gift_bag = document.getElementById("o").value.split(":");
			$scope.formData.gift_bag_id = gift_bag[1];*/
			$scope.formData.gift_bag_id = document.getElementById("o").value;
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				//alertService.add('success', data.result);
				var result = data.result;
				var len = result.length;
				var i;
				for (i=0; i < len; i++) {
					if (result[i].status === 'ok') {
						alertService.add('success', result[i].msg);
					} else if (result[i].status === 'error') {
						alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data) {
				alertService.add('danger', JSON.stringify(data));
			});
		};
		
	}
</script>
<div class="col-xs-12" ng-controller="sendGiftBagController">
	<div class="row" >
		<div class="eb-content">
			<!-- <form action="/game-server-api/gift-bag/all-server" method="post" role="form" ng-submit="processFrom('/game-server-api/gift-bag/all-server')" onsubmit="return false;"> -->
				<div class="form-group">
					<input type="text" name="gift_bag_id" ng-model="formData.gift_bag_id" class="form-control" style="overflow-y:auto;" id="o" onkeyup="autoComplete.start(event)"
				     autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
					<div class="auto_hidden" style="overflow-y:auto;max-height:500px;" id="auto"><!--自动完成 DIV--></div>
				</div>	
				<div class="form-group">
					<label>
						<input type="radio" ng-model="formData.send_type" name="send_type" ng-init="formData.send_type='name'" ng-checked="true" value="name"/>
						<?php echo Lang::get('serverapi.gift_for_name')?>
					</label>
					<label>
						<input type="radio" name="send_type" value="id" ng-model="formData.send_type"/>
						<?php echo Lang::get('serverapi.gift_for_id')?>
					</label>	
				</div>
				<div class="form-group">
					<textarea name="gift_data" ng-model="formData.gift_data"
						placeholder="<?php echo Lang::get('serverapi.all_server_tip') ?>"
						rows="15" required class="form-control"></textarea>
				</div>
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit') ?>"
				ng-click="processFrom('/game-server-api/gift-bag/all-server')"/>	
			<!-- </form>	  -->
		</div><!-- /.col -->
		<div class="form-group col-md-6">
			<p><font color=red size=4>注意：输入礼包输入框，输入礼包名或者礼包id的一部风即可匹配出相关礼包，对于不知道的礼包输入0则可匹配出全部礼包</font></p>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<div class="col-md-4">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
			</div>
			<div class="col-md-4">
				<a href="/game-server-api/giftbag/lookuppage?app_id=34" target="_blank"><?php echo Lang::get('serverapi.search_gift_record')?></a>
			</div>
		</div>
	</div>
</div>
<script>
    var autoComplete=new AutoComplete('o','auto',[<?php 
    	foreach ($gifts as $value) {
    		echo "'".$value."',";
    	} ?>
    ]);
</script>