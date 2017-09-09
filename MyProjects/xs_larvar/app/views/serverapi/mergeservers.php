<script>
	function MergeServers($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/merge/servers',
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
<div class="col-xs-12" ng-controller="MergeServers">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
				<b>选定主服</b>
					<select class="form-control" name="master_server_id"
						id="master_server_id" ng-model="formData.master_server_id" ng-init="formData.master_server_id=0">
						<option value="0">请选择合并后的主服</option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				<b>选定从服,按住鼠标左键滑动,或按住Ctrl键可多选</b>
					<select class="form-control" name="slave_server_ids"
						id="slave_server_ids" ng-model="formData.slave_server_ids" multiple="true" size="15">
						<option value="0">请选择合并后的从服(不需要再选主服)</option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<p style="font-size:15px">功能说明：此功能的合服<b style="color:red">只是在eastblue的一些功能里不再显示从服</b>，例如全服邮件等，具体的合服流程<b style="color:red">需要找奇修进行服务器机器上的合服操作之后进行此操作</b>，
				减少从服的显示，保证全服邮件等不会给从服发两次，此功能的逆操作比较复杂，因此请<b style="color:red">谨慎操作，如不清楚，请联系技术。</b></p>
				<p style="font-size:15px;color:red"><b>合服需要先找奇修然后在此处修改eastblue的显示！</b></p>
				<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<input type="submit" class="btn btn-default" value="提交" />
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