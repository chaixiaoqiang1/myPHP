<script> 
function serversqlcontroller($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};

    $scope.processFromAndDownload = function(type) {
        $scope.alerts = [];
        $scope.formData.type = type;
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/game-server/sql',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            window.location.replace("/game-server/sql?filename=" + data.filename);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="serversqlcontroller" style="overflow:auto">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFromAndDownload('submit')"
				onsubmit="return false;">
				<div class="form-group">
					<p><b><?php echo Lang::get('slave.select_server'); ?></b></p>
	                <select class="form-control" name="server_ids"
							id="server_ids" ng-model="formData.server_ids" required multiple="true" size="10">
							<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
							<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<p><b><?php echo Lang::get('slave.input_sql'); ?></b></p>
					<textarea class="form-control" id="sql" name="sql" required ng-model="formData.sql" style="width:800px;height:120px"></textarea>
				</div>

				<input type="submit" class="btn btn-success" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
                <input type="button" class="btn btn-success" style="margin-left:20px;"
                    value="<?php echo Lang::get('slave.last_time_result'); ?>" ng-click="processFromAndDownload('download')" />
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
