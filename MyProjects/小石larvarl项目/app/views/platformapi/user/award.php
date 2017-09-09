<script>
	function YYSGAwardController($scope, $http, alertService, $modal, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.is_show = 0;
		$scope.processFrom = function(url) {
			$scope.formData.url_type = 1;
			alertService.alerts = $scope.alerts;
			$scope.formData.end_time = $filter('date')($scope.formData.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data;
				if(result.status == 'ok'){
					alertService.add('success', result.msg);
				}else if(result.status == 'error'){
					alertService.add('danger', result.msg);
				}
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.searchFrom = function(url) {
			$scope.formData.url_type = 2;
			alertService.alerts = $scope.alerts;
			$scope.items = [];
			$scope.is_show = 0;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.is_show = 1;
				$scope.items = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.itemUpdate= function (list) {
		    var modalInstance = $modal.open({
		        templateUrl: 'update_item.html',
		        controller: updateItemController,
		        resolve: {
		            list : function () {
		                return list;
		            }
		        },
		        backdrop : false,
		        keyboard : false
		    });
		    modalInstance.result.then(function() {
		        $scope.item_update();   
		    });
		};
		$scope.recordUpdate= function (list) {
		    var modalInstance = $modal.open({
		        templateUrl: 'update_record.html',
		        controller: updateRecordController,
		        resolve: {
		            list : function () {
		                return list;
		            }
		        },
		        backdrop : false,
		        keyboard : false
		    });
		    modalInstance.result.then(function() {
		        $scope.record_update();   
		    });
		};
	}
	function updateItemController($scope, $modalInstance, list, $http, alertService, $filter) {
	    $scope.list = list;
	    $scope.listData = {};
	    $scope.listData.url_type = 1;
	    $scope.listData.operation_type = 1;
	    $scope.cancel = function() {
	        $modalInstance.dismiss('cancel');
	    }
	    $scope.item_update = function (url) {
	        alertService.alerts = $scope.alerts;
	        $http({
	            'method': 'post',
	            'url': url,
	            'data': $.param($scope.listData),
	            'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
	        }).success(function (data) {
	            var result = data;
	            var select = $scope.listData;
	            var id = select.id;
                if(result.status == 'ok'){
                   alert('success');
                   $modalInstance.close();
                   window.location.href = '/platform-api/user/award/set/?url_type=2&operation_type=1&gift_id='+id;
                } else if(result.status == 'error'){
                    alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
                }
	        }).error(function (data) {
	            alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
	        }); 
	    };
	}
	function updateRecordController($scope, $modalInstance, list, $http, alertService, $filter) {
	    $scope.list = list;
	    $scope.listData = {};
	    $scope.listData.url_type = 1;
	    $scope.listData.operation_type = 2;
	    $scope.cancel = function() {
	        $modalInstance.dismiss('cancel');
	    }
	    $scope.record_update = function (url) {
	    	$scope.listData.end_time = $filter('date')($scope.listData.end_time1, 'yyyy-MM-dd HH:mm:ss');
	        alertService.alerts = $scope.alerts;
	        $http({
	            'method': 'post',
	            'url': url,
	            'data': $.param($scope.listData),
	            'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
	        }).success(function (data) {
	            var result = data;
                if(result.status == 'ok'){
                	alert('success');
                   $modalInstance.close();
                } else if(result.status == 'error'){
                    alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
                }
	        }).error(function (data) {
	            alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
	        });
	    };

	}
</script>
<div class="col-xs-12" ng-controller="YYSGAwardController">
	<div class="row">
		<div class="eb-content">
				<div class="form-group">
					<select class="form-control" name="operation_type" ng-model="formData.operation_type" ng-init="formData.operation_type=1">
						<option value="1">添加修改奖品礼包信息</option>
						<option value="2">添加修改抽奖用户信息</option>
					</select>
				</div>
				<div class="form-group" ng-if="formData.operation_type == 1">
					<div class="col-md-3" style="padding-left: 0;">
						<select class="form-control" name="gift_id"
							ng-model="formData.gift_id" ng-init="formData.gift_id=0">
							<option value="0">请选择礼包</option>
							<?php foreach($gifts as $k => $v){?>
								<option value="<?php echo $k; ?>"><?php echo $v;?></option>
							<?php }?>	
						</select>
					</div>
					<div class="col-md-4">
						<input type="text" class="form-control" name="img_url"
							ng-model="formData.img_url" placeholder="官网对应的图片地址"/>
					</div>
					<div class="col-md-3">
						<input type="text" class="form-control" name="number"
							ng-model="formData.number" placeholder="请设置礼包数量"/>
					</div>
					<div class="col-md-2">
					    <label>
					        <input type="checkbox" ng-init="formData.is_used=0" ng-true-value="1" ng-false-value="0" ng-model="formData.is_used"/>
					        使用该奖励
					    </label>
					</div>
				</div>
				<div class="form-group" ng-if="formData.operation_type == 2">
					<div class="col-md-3" style="padding-left: 0;">
						<input type="text" class="form-control" name="uid"
							ng-model="formData.uid" placeholder="uid"/>
					</div>
					<div class="col-md-3">
						<input type="text" class="form-control" name="player_id"
							ng-model="formData.player_id" placeholder="player_id"/>
					</div>
					<div class="col-md-3">
						<input type="text" class="form-control" name="total_chance"
							ng-model="formData.total_chance" placeholder="抽奖总次数"/>
					</div>
					<div class="col-md-3">
						<div class="input-group">
							<quick-datepicker ng-model="formData.end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
                <div class="col-md-2">
                	<input type="button" class="btn btn-warning"
                	ng-click="processFrom('/platform-api/user/award/set')"
                		value="<?php echo Lang::get('basic.btn_submit') ?>" />
                </div>
                <div class="col-md-2">
                	<input type="button" class="btn btn-info"
                	ng-click="searchFrom('/platform-api/user/award/set')"
                		value="<?php echo Lang::get('basic.btn_show') ?>" />
                </div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped" ng-if="is_show == 1 && formData.operation_type == 1">
			<thead>
				<tr class="info">
					<td><b>礼包</b></td>
					<td><b></b></td>
					<td><b>图片地址</b></td>
					<td><b>礼包数量</b></td>
					<td><b>已领取数量</b></td>
					<td><b>是否使用</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.gift_name}}</td>
					<td><input type="button" class="btn btn-danger"
                		ng-click="itemUpdate(t)"
                		value="修改"/>
					</td>
					<td>{{t.img_url}}</td>
					<td>{{t.num}}</td>
					<td>{{t.received_num}}</td>
					<td>{{t.is_used}}</td>
				</tr>
			</tbody>
		</table>
		<table class="table table-striped" ng-if="is_show == 1 && formData.operation_type == 2">
			<thead>
				<tr class="info">
					<td><b>uid</b></td>
					<td><b></b></td>
					<td><b>player_id</b></td>
					<td><b>总抽奖次数</b></td>
					<td><b>已使用抽奖次数</b></td>
					<td><b>抽奖最后截止时间</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.uid}}</td>
					<td><input type="button" class="btn btn-danger"
                		ng-click="recordUpdate(t)"
                		value="修改"/>
					</td>
					<td>{{t.player_id}}</td>
					<td>{{t.total_chance}}</td>
					<td>{{t.used_chance}}</td>
					<td>{{t.end_time}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<script type="text/ng-template" id="update_item.html">
        <div class="modal-header">
        </div>
        <form action="/platform-api/user/award/set" method="post" role="form" ng-submit="item_update('/platform-api/user/award/set')" onsubmit="return false;">
        <div class="modal-body">
			<div class="form-group ">
				<input type="hidden" ng-model="listData.id" ng-init="listData.id = list.id" name="id"/>
				<div class="col-md-4">
					<input type="text" class="form-control" name="img_url" ng-init="listData.img_url=list.img_url"
						ng-model="listData.img_url" placeholder="官网对应的图片地址"/>
				</div>
				<div class="col-md-2">
					<input type="text" class="form-control" name="number" ng-init="listData.number=list.num"
						ng-model="listData.number" placeholder="礼包数量"/>
				</div>
				<div class="col-md-4">
					<input type="text" class="form-control" name="received_num" ng-init="listData.received_num=list.received_num"
						ng-model="listData.received_num" placeholder="当前奖励已领取数量"/>
				</div>
				<div class="col-md-2">
				    <label>
				        <input type="checkbox" ng-init="listData.is_used=0" ng-true-value="1" ng-false-value="0" ng-model="listData.is_used"/>
				        使用该奖励
				    </label>
				</div>
			</div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-warning">修改</button>
            <a class="btn btn-info" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>
<script type="text/ng-template" id="update_record.html">
        <div class="modal-header">
        </div>
        <form action="/platform-api/user/award/set" method="post" role="form" ng-submit="record_update('/platform-api/user/award/set')" onsubmit="return false;">
        <div class="modal-body">
			<div class="form-group">
				<input type="hidden" ng-model="listData.id" ng-init="listData.id = list.id" name="id"/>
				<div class="col-md-6">
					<input type="text" class="form-control" name="total_chance" ng-init="listData.total_chance=list.total_chance"
						ng-model="listData.total_chance" placeholder="抽奖总次数"/>
				</div>
				<div class="col-md-6">
					<div class="input-group">
						<quick-datepicker ng-model="listData.end_time1" init-value={{list.end_time}}></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
						<span><i>抽奖截止时间</i></span>
					</div>
				</div>
			</div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-warning">修改</button>
            <a class="btn btn-info" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>