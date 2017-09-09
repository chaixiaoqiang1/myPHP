<script> 
function getPaymentOrderListController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.file='';
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
	$scope.items = [];
	$scope.pagination = {};
	//pagination
	/*$scope.pagination.totalItems = 0;
	$scope.pagination.currentPage = 1;
	$scope.pagination.perPage= 1;

	$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
		if ($scope.end_time > 0) {
			$scope.processFrom(newPage);
		}
	});
    $scope.processFrom = function(newPage) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/payment/order/list?page=' + newPage,
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.pagination.currentPage = data.current_page;
			$scope.pagination.perPage= data.per_page;
			$scope.pagination.totalItems = data.count;
			$scope.items = data.items;
			location.hash = '#top';
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
    };*/
    $scope.download = function(url) {
		alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
	     $http({
        'method': 'post',
        'url': url,
        'data': $.param($scope.formData),
        'headers': {
            'Content-Type': 'application/x-www-form-urlencoded'
        }
    }).success(function(data) {
    	alertService.add('success', 'OK');
    	console.log(data.now);
        window.location.replace("/slave-api/economy/server-consume/download?now=" + data.now);
	}).error(function(data) {
		alertService.add('danger', data.error);
	});
};
} 
</script>
<div class="col-xs-12" ng-controller="getPaymentOrderListController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/payment/order/list" method="get" role="form"
				ng-submit="processFrom(1)" onsubmit="return false;">

				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
					ng-multiple="true" size=10>
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($server as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="col-md-6" style="padding: 2">
					<select class="form-control" name="action_type" ng-model="formData.action_type"
					ng-init="formData.action_type=0" style="margin-left:-15px">
						<option value="0"><?php echo Lang::get('serverapi.select_consume_type')?></option>
						<?php foreach($type as $v){ ?>
							<option value="<?php echo $v->action_type?>" > <?php echo $v->action_type .'--'. $v->action_name?></option>
						<?php }?>
					</select>
				</div>
				
				
				<br/><br/><br/>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				
				
				<div class="col-md-4" style="padding: 30">
					<input type='button' class="btn btn-warning"
						value="<?php echo Lang::get('serverapi.download_csv') ?>"
						ng-click="download('/slave-api/economy/server-consume/download')"  style ="margin-left:-15px"/>
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