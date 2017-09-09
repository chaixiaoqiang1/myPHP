<script> 
function inputsqlcontroller($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
    $scope.items = {};
    $scope.sum = {};
    $scope.downloadData = {};

    $scope.processFrom = function() {
    	$scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.sqls = [];
        $scope.formData.dealsqls = 0;
        $http({
            'method': 'post',
            'url': '/slave-api/input/sql',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	alertService.add('success', data.info);
        	$scope.keywords = {};
    		$scope.sqlresult = {};
            $scope.keywords = data.keywords;
            $scope.sqlresult = data.sqlresult;
        }).error(function(data) {
        	$scope.keywords = {};
    		$scope.sqlresult = {};
            alertService.add('danger', data.error);
        });
    };

    $scope.processFromAndDownload = function() {
        $scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.sqls = [];
        $scope.formData.dealsqls = 0;
        $scope.formData.ifdownload = 1;
        if($scope.start_value && $scope.end_value){
            $scope.processFromAndDownloadSql($scope.start_value, $scope.formData.sql);
        }else{
            $http({
                'method': 'post',
                'url': '/slave-api/input/sql',
                'data': $.param($scope.formData),
                'headers': {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            }).success(function(data) {
                alertService.add('success', data.info);
                $scope.formData.ifdownload = 0;
                $scope.keywords = {};
                $scope.sqlresult = {};
                $scope.keywords = data.keywords;
                $scope.sqlresult = data.sqlresult;
                window.location.replace("/slave-api/input/sql/download?now=" + data.now);
            }).error(function(data) {
                $scope.formData.ifdownload = 0;
                $scope.keywords = {};
                $scope.sqlresult = {};
                alertService.add('danger', data.error);
            });
        }
    };

    $scope.processFromAndDownloadSql = function(init, sql) {
        $scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.sqls = [];
        $scope.formData.sql = sql;
        while($scope.formData.sql.indexOf('[value]') >= 0){
            $scope.formData.sql = $scope.formData.sql.replace('[value]', init);
        }
        $scope.formData.dealsqls = 0;
        $scope.formData.ifdownload = 1;
        $http({
            'method': 'post',
            'url': '/slave-api/input/sql',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            alertService.add('success', data.info);
            $scope.formData.ifdownload = 0;
            $scope.keywords = {};
            $scope.sqlresult = {};
            $scope.keywords = data.keywords;
            $scope.sqlresult = data.sqlresult;
            window.location.replace("/slave-api/input/sql/download?now=" + data.now);
            if(init < $scope.end_value){
                init += 1;
                $scope.processFromAndDownloadSql(init, sql);
            }
        }).error(function(data) {
            $scope.formData.ifdownload = 0;
            $scope.keywords = {};
            $scope.sqlresult = {};
            alertService.add('danger', data.error);
            if(init < $scope.end_value){
                init += 1;
                $scope.processFromAndDownloadSql(init, sql);
            }
        });
    };

    $scope.processgetsqls = function() {
    	$scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.formData.dealsqls = 1;
        $http({
            'method': 'post',
            'url': '/slave-api/input/sql',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.keywords = {};
    		$scope.sqlresult = {};
    		$scope.sqls = data.sqls;
        }).error(function(data) {
        	$scope.keywords = {};
    		$scope.sqlresult = {};
            alertService.add('danger', data.error);
        });
    }

    $scope.processaddsqls = function() {
    	$scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.formData.dealsqls = 2;
        $http({
            'method': 'post',
            'url': '/slave-api/input/sql',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	$scope.keywords = {};
    		$scope.sqlresult = {};
    		alertService.add('success', data.msg);
        }).error(function(data) {
        	$scope.keywords = {};
    		$scope.sqlresult = {};
            alertService.add('danger', data.error);
        });
    }

    $scope.download = function() {
        $scope.alerts = [];
        alertService.alerts = $scope.alerts;
        $scope.formData.dealsqls = 0;
        $scope.downloadData.tobedownloadkey = $scope.keywords;
        $scope.downloadData.tobedownloadvalue = $scope.sqlresult;
        $http({
            'method': 'post',
            'url': '/slave-api/input/sql/download',
            'data': $.param($scope.downloadData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            window.location.replace("/slave-api/input/sql/download?now=" + data.now);
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    }
} 
</script>
<div class="col-xs-12" ng-controller="inputsqlcontroller" style="overflow:auto">
	<div class="row">
		<div class="eb-content">
                 <b>当前游戏的game_id为 <?php echo Session::get('game_id');?> ,platform_id为 <?php echo Session::get('platform_id');?> </b>
			<form action="" method="get" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
                <select class="col-xs-4" name="database" ng-model="formData.database" ng-init="formData.database = 'qiqiwu'">
                    <option value="qiqiwu">qiqiwu</option>
                    <option value="payment">payment</option>
                    <option value="eastblue">eastblue</option>
                </select>
				<textarea id="sql" name="sql" ng-model="formData.sql" class="col-xs-12" rows="5"></textarea>

				<input type="submit" class="btn btn-success"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
				<input type="button" class="btn btn-success"
					value="可用语句" ng-click="processgetsqls()" />	
				<input type="button" class="btn btn-success"
					value="新增可用语句" ng-click="processaddsqls()" />
                <input type="button" class="btn btn-success"
                    value="查询并下载结果" ng-click="processFromAndDownload()" />
                    <br>
                    <br>
                <input type="number" class="col-xs-3" 
                    placeholder="start_value" ng-model="start_value" />
                <input type="number" class="col-xs-3" 
                    placeholder="end_value" ng-model="end_value" />
                    <br>
                    <br>
				<b>&nbsp;&nbsp;新增语句时，在输入框中输入描述和语句，以|分割，例如 "查询|select"</b>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-6">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="col-xs-8">
		<table class="table table-striped">
			<tbody>
				<tr ng-repeat="sql in sqls">
					<td>{{sql.name}}</td>
					<td>{{sql.value}}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="eb-content">
		<table class="table table-striped">
			<thead>
				<tr class="info" ng-repeat="t in keywords">
				<?php
					for($i=0;$i<50;$i++){
					?><td>{{t.key<?php echo $i;?>}}</td><?php
					} 
				?>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="s in sqlresult">
					<?php
					for($i=0;$i<50;$i++){
					?><td>{{s.key<?php echo $i;?>}}</td><?php
					} 
				?>
				</tr>
			</tbody>
		</table>
	</div>
</div>
