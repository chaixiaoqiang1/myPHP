<script type="text/javascript">
function sendAllServerGiftBagController1($scope, $http, alertService)
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
    $scope.processFrom = function(url) {
        alertService.alerts = $scope.alerts;
    	$http({
            'method' : 'post',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alertService.add('success', data);
        }).error(function(data) {
            alertService.add('danger', JSON.stringify(data));
        });
        
        
    };

    $scope.getSource = function() {
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/gift-bag/all-server-gift-bag/getsource',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.source = data;
			}).error(function(data) {
				alertService.add('danger', data.error, 2000);
			});
		};
}
</script>
<div class="col-xs-12" ng-controller="sendAllServerGiftBagController1">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/gift-bag/all-server-gift-bag1"
				method="post" role="form"
				ng-submit="isConfirm('/game-server-api/gift-bag/all-server-gift-bag1')"
				onsubmit="return false;">

				<div class="form-group">
					<select class="form-control" name="server_id1"
						id="select_game_server" ng-model="formData.server_id1"
						ng-init="formData.server_id1=0"  style="width: 50%; float:left" ng-change="getSource()">
						<option value = "0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name.'----'.$v->server_id.'--'.$v->server_internal_id;?></option>
						<?php } ?>		
						</optgroup>
					</select>
					
					
					<select name="source" ng-model="formData.server_id2"  name = 'server_id2' id="source" class="form-control" ng-init="formData.server_id2=0"  style="width: 50%;">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
						<option ng-repeat="source in source" value="{{source.server_id}}">{{source.server_id}}    {{source.server_name}}</option>
					</select>
				
				</div>
				<div class="form-group">
					<select class="form-control" name="gift_bag_id" id="gift_bag_id"
						ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
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
				<div class="form-group">
					<label><input type="checkbox" name="type" value=""
						ng-model="formData.type" /><?php echo Lang::get('serverapi.which_server') ?></label>
				</div>

				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.union_gift1')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.union_gift2')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.union_gift3')?></span>
				</div>
				
				<br>
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