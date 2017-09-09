<script>
	function getMobileDeviceController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.show = 0;
		$scope.processFrom = function(check) {
			$scope.show = 0;
			$scope.alerts = [];
			$scope.items = [];
			$scope.formData.check = check;
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/platform-api/user/mobile_push',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if(data.error==0)
					alertService.add('success', 'Success!');
				else
					alertService.add('danger', data);
			}).error(function(data) {
				alertService.add('danger', data);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getMobileDeviceController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/platform-api/user/mobile_push" method="get" role="form"
				ng-submit="processFrom(0)" onsubmit="return false;">
				<div class="col-xs-10" style="margin-top:5px;">
					<div class="form-group " style="padding-left:0;">
						<textarea name="title" class="form-control" ng-model="formData.title" rows = "1" placeholder="<?php echo Lang::get('slave.title')?>" required></textarea>
					</div>
					<div class="form-group " style="padding-left:0;">
						<textarea name="content" class="form-control" ng-model="formData.content" cols="112" rows = "10" placeholder="<?php echo Lang::get('slave.content')?>" required></textarea>
					</div>
					<div class="form-group " style="padding-left: 0;">
						<select class="form-control" name="except_os_type"
						id="except_os_type" ng-model="formData.except_os_type"
						multiple="multiple" ng-multiple="true" size=5>
							<option value="Android">排除Android设备</option>
							<option value="iOS">排除iOS设备</option>
						</select>
					</div>
				</div>
				<p><font color=red>标题填写游戏名称(如：夜夜三國)</font></p>
					<div class="col-xs-10" style="margin-top:5px;">
					<div>
						<input type="submit" class="btn btn-default" 
								value="<?php echo Lang::get('basic.btn_submit') ?>" />
					</div>
				</div>
			</form>
				<div class="clearfix"></div>
				<br>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="form-group">
		<table class="table table-striped"> 
			<thead>
				<tr>
					<td><b>推送记录</b></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach($mobiledata as $value){?>
				<tr>
					<td>
						<b><?php echo $value->desc; ?></b>
					</td>
					<td>
						<b><?php echo $value->new_value; ?></b>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
</div>