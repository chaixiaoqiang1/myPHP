<script>
	function ItemController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data;
				for (var i = 0; i < result.length; i++) {
					if (result[i].status == "OK") {
						alertService.add('success', result[i].msg);
					}else{
						alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="ItemController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple"
					ng-multiple="true" size=10>
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<!--<select class="form-control" name="item_id" ng-model="formData.item_id" ng-init="formData.item_id=0">
						<option value="0"><?php echo Lang::get('serverapi.enter_item_name') ?></option>
						<?php foreach ($items as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>-->
					<div class="form-group">
					<label>
						<input type="radio" ng-model="formData.item_id" name="item_id" ng-init="formData.item_id=30300894" value='30300894'/>
						<?php echo Lang::get('serverapi.xiaonangua');?>
					</label>

					<label>
						<input type="radio" ng-model="formData.item_id" name="item_id" value="30300895"/>
						<?php echo Lang::get('serverapi.xiaohuoji')?>
					</label>

					<label>
						<input type="radio" ng-model="formData.item_id" name="item_id" value="30300175"/>
						<?php echo Lang::get('serverapi.xinnianhongbao') ?>
					</label>
					<label>
						<input type="radio" ng-model="formData.item_id" name="item_id" value="30301260"/>
						<?php echo Lang::get('serverapi.zongzi') ?>
					</label>
				</div>
				</div>
				<div class="form-group">
					<input type="text" name="item_num" ng-model="formData.item_num" class="form-control" placeholder="<?php echo Lang::get('serverapi.enter_item_num') ?>"/>
				</div>
	
				<div class="form-group" style="height:35px;">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:50:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
			</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.xiaohuoji1')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.xiaohuoji2')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.xiaohuoji3')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.xiaohuoji4')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.xiaohuoji5')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.xiaohuoji6')?></span>
				</div>
			<br>
			<br>
			<div class="col-md-6" style="padding: 0">
					<div class="form-group" style="height: 40px;">
						<div class="col-md-4" style="padding: 0">
							<input type='button' class="btn btn-primary"
								value="<?php echo Lang::get('serverapi.promotion_set') ?>"
								ng-click="process('/game-server-api/activity/item?action=open')" />
						</div>
						<div class="col-md-4" style="padding: 0">
							<input type='button' class="btn btn-primary"
								value="<?php echo Lang::get('serverapi.promotion_lookup') ?>"
								ng-click="process('/game-server-api/activity/item?action=look')" />
						</div>
						<div class="col-md-4" style="padding: 0">
							<input type='button' class="btn btn-danger"
								value="<?php echo Lang::get('serverapi.promotion_close') ?>"
								ng-click="process('/game-server-api/activity/item?action=close')" />
						</div>
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
	
</div>