<script type="text/javascript">
function sendAllServerGiftBagController($scope, $http, alertService)
{
    $scope.alerts = [];
    $scope.formData = {};
    $scope.isConfirm = function(url){
        var t1 = confirm('亲，你确定要发送全服礼包吗？');
        if(t1 == true)
        {
            var t2 = confirm('真的确定吗？');
            if(t2 == true)
            {
                this.processFrom(url);
            }
        }
    };
    $scope.formData.btn = 0;
    $scope.processFrom = function(url) {
    	$scope.formData.btn ++;
        alertService.alerts = $scope.alerts;
        if ($scope.formData.btn < 2) {
        	$http({
	            'method' : 'post',
	                'url'    : url,
	            'data'   : $.param($scope.formData),
	            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
	        }).success(function(data) {
	            alertService.add('success', data.result);
    			$scope.formData.btn = 0;
	        }).error(function(data) {
	            alertService.add('danger', JSON.stringify(data));
    			$scope.formData.btn = 0;
	        });
        } else{
        	alertService.add('danger', 'Waiting...');
        }
    };
}
</script>
<div class="col-xs-12" ng-controller="sendAllServerGiftBagController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/gift-bag/all-server-gift-bag"
				method="post" role="form"
				ng-submit="isConfirm('/game-server-api/gift-bag/all-server-gift-bag')"
				onsubmit="return false;">

				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=20>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="gift_bag_id" id="gift_bag_id"
						ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->itemid?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('serverapi.gift_bag_days')?>"
						ng-model="formData.days" name="days"?>
				</div>
				<div class="form-group">
					<textarea name="remark" ng-model="formData.remark"
						placeholder="<?php echo Lang::get('serverapi.gift_bag_remark') ?>"
						rows="5" class="form-control"></textarea>
				</div>

				<input type="submit" class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<div class="col-md-4">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
			</div>
			<div class="col-md-4">
				<a href="/game-server-api/giftbag/lookuppage?app_id=64" target="_blank"><?php echo Lang::get('serverapi.search_gift_record')?></a>
			</div>
		</div>
	</div>
</div>