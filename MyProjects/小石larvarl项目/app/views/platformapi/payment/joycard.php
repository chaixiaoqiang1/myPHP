<script type = 'text/javascript'>
function joyCardController($scope, $http, alertService, $filter){
	$scope.alert = [];
	$scope.formData = {};
	$scope.start_time=null;
	$scope.end_time=null;
	$scope.downloadData = {};
	$scope.joyCardCreate = function(url) {
		$scope.alert = [];
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
        	$scope.formData.type = 0;
        	$scope.joyCardQuery('/platform-api/payment/joycard/query');
        }).error(function(data){
        	alert(data.error);
        });
    };

    $scope.joyCardQuery = function(url) {
    	$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : url,
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
        	$scope.joycard = data;
        }).error(function(data){
        	alert(data.error);
        });
    };

    $scope.joyCardChangeOwner = function(target) {
    	$scope.formData.tar_card_number = target.getAttribute('data');
        alertService.alerts = $scope.alerts;
        $http({
            'method' : 'post',
            'url'    : '/platform-api/payment/joycard/changeowner',
            'data'   : $.param($scope.formData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
        	$scope.joyCardQuery('/platform-api/payment/joycard/query');
        }).error(function(data){
        	alert(data.error);
        });
    };

    $scope.download = function() {
    	//$scope.downloadData.tobedownload = $scope.joycard;
    	$scope.formData.titles = 
    		['创建者',
            '卡号',
            '密码',
            '币商ID',
            '使用该点卡的玩家ID',
            '创建时间',
            '使用时间',
            '点数'];
        /*$scope.formData.keys = 
    		['creator',
            'card_number',
            'card_secret',
            'owner',
            'uid',
            'create_time',
            'use_time',
            'point'];*/
    	alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/platform-api/payment/joycard/download',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            window.location.replace("/platform-api/payment/joycard/download?now=" + data.now);
        }).error(function(data) {
        	alert(data.error);
        });
    };
}

</script>

<div class="col-xs-12" ng-controller="joyCardController">

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:180px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo '币商ID(也是查询条件)';?>" name="player_id" ng-model="formData.player_id">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.enter_amount');?>" name="num" ng-model="formData.num">
		</div>
		<div class="col-md-6" style="padding: 0 ;width:180px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo '每张点卡的价格';?>" name="money" ng-model="formData.money" disabled="true" ng-init="formData.money=1000000">
			<select class="form-control" name="point" ng-model="formData.points" ng-init="formData.points=2000" disabled="true">
            <option value="0"><?php echo Lang::get('serverapi.shop_back') ?></option>
                <?php foreach ($point as  $k => $v) { ?>
                <option value="<?php echo $v?>"><?php echo $v;?></option>
                <?php } ?>      
        	</select>
		</div>
		<div class="form-group" style="height: 35px;">
			<div class="col-md-6" style="padding: 0">
				<div class="input-group">
					<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
			</div>
			<div class="col-md-6" style="padding: 0">
				<div class="input-group">
					<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="form-group" style="margin-top:10px">
		<div class="col-md-1" style="padding: 0">
			<input type='button' class="btn btn-primary"
				value="<?php echo '创建点卡' ?>"
			ng-click="joyCardCreate('/platform-api/payment/joycard/create')" />
		</div>
		<div class="col-md-3">	
			<select class="form-control" name="type" ng-model="formData.type" ng-init="formData.type=1">
				<option value="0">查询未使用的点卡</option>
				<option value="1">查询已使用的点卡</option>
				<option value="2">查询所有的点卡</option>
			</select>
		</div>
		<div class="col-md-2">
		<input type='button' class="btn btn-primary"
			value="<?php echo '查询点卡' ?>"
		ng-click="joyCardQuery('/platform-api/payment/joycard/query')" />	
		</div>
	</div>
	<div class>
		<input type='button' class="btn btn-warning" style="margin-left:200px;"
				value="<?php echo Lang::get('slave.download') ?>"
				ng-click="download()" />
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12" style="padding: 0;">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td>创建者</td>
					<td>卡号</td>
					<td>密码</td>
					<td>币商ID</td>
					<td>使用该点卡的玩家ID</td>
					<td>创建时间</td>
					<td>使用时间</td>
					<td>点数</td>
					<td>修改拥有者</td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in joycard">
					<td>{{t.creator}}</td>
					<td>{{t.card_number}}</td>
					<td>{{t.card_secret}}</td>
					<td>{{t.owner}}</td>
					<td>{{t.uid}}</td>
					<td>{{t.create_time}}</td>
					<td>{{t.use_time}}</td>
					<td>{{t.point}}</td>
					<td><input type="text" name="new_player_id" value=""  style="width:120px"ng-model="formData.new_player_id" class="form-control"/></td>
					<td><input type='button' class="btn btn-primary"
						value="<?php echo '修改所有者' ?>"
						ng-click="joyCardChangeOwner($event.target)"  data={{t.card_number}} >
					</td>
				</tr>
			</tbody>
		</table>
<!-- 	<div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div> -->
</div>