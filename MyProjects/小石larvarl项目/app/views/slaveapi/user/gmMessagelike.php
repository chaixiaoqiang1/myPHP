<script> 
function getGmMessage($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.items = {};
    $scope.pagination = {};
	//pagination
	$scope.pagination.totalItems = 0;
	$scope.pagination.currentPage = 1;
	$scope.pagination.perPage= 1;
	$scope.formData.sign = 0;

	$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
		if ($scope.formData.sign > 0) {
			$scope.processFrom(newPage);
		}
	});

    $scope.processFrom = function(newPage) {
    	$scope.items = {};
    	$scope.alerts = [];
    	$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
    	$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/slave-api/gm/message?page=' + newPage,
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.formData.sign = 1;
        	$scope.pagination.currentPage = data.current_page;
			$scope.pagination.perPage= data.per_page;
			$scope.pagination.totalItems = data.count;
			$scope.items = data.items;
			location.hash = '#top';
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getGmMessage">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group">
					<div class="col-md-4" style="padding-left: 0">
						<select class="form-control" name="limit_time" ng-model="formData.limit_time" ng-init="formData.limit_time=1">
							<option value="1">使用时间限制</option>
							<option value="0">不使用时间限制</option>
						</select>
					</div>
					<div class="col-md-4">
					    <div class="input-group">
					        <quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
					        <i class="glyphicon glyphicon-calendar"></i>
					    </div>
					</div>
					<div class="col-md-4">
					    <div class="input-group">
					        <quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
					        <i class="glyphicon glyphicon-calendar"></i>
					    </div>
					</div>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="partofmessage" 
						placeholder="请输入你想查询的关键词，如果查询不到请切换繁简体输入尝试(非必须)"
						ng-trim="false" ng-model="formData.partofmessage" name="partofmessage" />
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="gm_name"
						placeholder="请输入GM名字(非必须)"
						ng-trim="true" ng-model="formData.gm_name" name="gm_name" />
				</div>
				<div class="col-md-6" style="padding: 0">
					<select class="form-control" name="type"
							id="type" ng-model="formData.type"
							ng-init="formData.type='reply_message'">
							<option value="reply_message">根据GM回复查询</option>	
							<option value="message">根据玩家问题查询</option>
					</select>
				</div>
				<div class="col-md-6" style="padding: 0">
					<input type="submit" class="btn btn-default" style=""
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
				</div>
				<div class="col-md-6" style="padding: 0; margin-top: 10px">
					<b style="color:blue">手游的问题和回答对应关系可能不是很准确</b>
				</div>
				<div class="col-md-12" style="padding: 0;">
					<b style="color:red">注意：</b>
					<b style="color:red">1、针对某个功能或问题，GM不知道在怎么回答的时候，可以通过（GM回复或者玩家提问）关键词来搜索历史GM是怎么回答的（所以历史GM回复一定要尽量准确）。通过提问玩家ID在GM回复功能中可以详细查询上下文。
					</b><br>
					<b style="color:red">2、统计某个功能上线后，来问咨询的玩家到底有多少，考虑写攻略之类，优化日常运营。
					</b></br>
					<b style="color:red">3，请输入GM名字（非必填）指的是gm用来登录eastblue的账号，一般为gm的邮箱前缀，如果不是邮箱前缀的话则问对应gm登录eastblue的账号。
					</b>
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
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>提问玩家ID</b></td>
					<td><b>提问内容</b></td>
					<td><b>提问时间</b></td>
					<td><b>回复人</b></td>
					<td><b>回复内容</b></td>
					<td><b>回复时间</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.player_id}}</td>
					<td>{{t.message}}</td>
					<td>{{t.send_time}}</td>
					<td>{{t.username}}</td>
					<td>{{t.reply_message}}</td>
					<td>{{t.reply_time}}</td>
				</tr>
			</tbody>
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>