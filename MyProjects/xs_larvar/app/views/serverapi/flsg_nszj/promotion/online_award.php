<script>
	function onlineAwardController($scope, $http, alertService,$filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.begin_time = null;
		$scope.end_time = null;
		$scope.process = function (url) {
           alertService.alerts = $scope.alerts;
           $scope.formData.is_clean = 0;
           <?php for($i = 1;$i <= 6; $i++){?>
           	$scope.formData.begin_time<?php echo $i ?> = $filter('date')($scope.begin_time<?php echo $i ?>, 'yyyy-MM-dd HH:mm:ss');
		   	$scope.formData.end_time<?php echo $i ?> = $filter('date')($scope.end_time<?php echo $i ?>, 'yyyy-MM-dd HH:mm:ss');
		   <?php }?>
           $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };
		$scope.look = function(url){
			alertService.alerts = $scope.alerts;
            $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
		};
		$scope.processClean = function (url) {
           alertService.alerts = $scope.alerts;
           $scope.formData.is_clean = 1;
           <?php for($i = 1;$i <= 6; $i++){?>
           	$scope.formData.begin_time<?php echo $i ?> = $filter('date')($scope.begin_time<?php echo $i ?>, 'yyyy-MM-dd HH:mm:ss');
		   	$scope.formData.end_time<?php echo $i ?> = $filter('date')($scope.end_time<?php echo $i ?>, 'yyyy-MM-dd HH:mm:ss');
		   <?php }?>
           $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };
	}
</script>
<div class="col-xs-12" ng-controller="onlineAwardController">
	<div class="row">
		<div class="col-xs-8" style="padding: 2;width:90%;">
				<div class="form-group" style="padding: 2;width:50%;float:left;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=20>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div ><font color="red"><?php echo Lang::get('serverapi.online_reward_tip');?></font>
				</div>
				
				<div class="panel panel-primary"style="float:left;clear:both;">
					<div class="panel-heading">
						<h3 class="panel-title">
				<?php echo Lang::get('serverapi.select_activity_award');?>
			</h3>
					</div>
					<div class="panel-body" >
				<?php for($i = 1; $i <= 6; $i++) {?>
					<div class="form-group" style="height: 30%;">
							<div class="col-md-4" style="padding: 2;width: 7%; float:left;">
								<label><input type="checkbox" name="is_remove<?php echo $i?>" value="0"
                              		ng-model="formData.is_remove<?php echo $i?>"/><?php echo Lang::get('serverapi.is_remove') ?>
                				</label>
							</div>
							<!--2-->
							<div class="col-md-4" style="padding: 2;width: 18%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<select class="form-control" style="padding: 2;width: 100%;" name="item_id<?php echo $i?>"
										id="select_item_id<?php echo $i?>"
										ng-model="formData.item_id<?php echo $i?>"
										ng-init="formData.item_id<?php echo $i?>=0">
										<option value="0"><?php echo Lang::get('serverapi.select_googs') ?></option>
							<?php foreach ($item as $k => $v) { ?>
								<option value="<?php echo $v->id?>"><?php echo $v->id.' : '.$v->name;?></option>
							<?php } ?>			
						</select>
								</div>

							</div>
							<!--3-->
							<div class="col-md-4" style="padding: 2;width: 19%;height:30px;float:left;">
																	
								<div class="col-md-4" style="padding: 0;width: 100%;">
									<quick-datepicker ng-model="begin_time<?php echo $i ?>" name="begin_time"
									 init-value="00:00:00"></quick-datepicker> 
									 <i class="glyphicon glyphicon-calendar"></i>
								</div>
							</div>
							<!--4-->
							<div class="col-md-4" style="padding: 0;width: 19%;height:30px; float:left;">
																	
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<quick-datepicker ng-model="end_time<?php echo $i ?>" name="end_time"
								     init-value="23:59:59"></quick-datepicker> 
								   <i class="glyphicon glyphicon-calendar"></i>
								</div>
							</div>
							<!--5-->
							<div class="col-md-4" style="padding: 2;width: 19%; float:left;">
										
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.price<?php echo $i?>"
										name="price<?php echo $i?>"
										placeholder="现价"/></br>
								</div>
							</div>
							<!--6-->
							<div class="col-md-4" style="padding: 2;width: 18%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.vip<?php echo $i?>"
										name="vip<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.vip') ?>"/></br>
								</div>
							</div>

					</div>
				<?php }?>
				</div>
				</div>
				<div style="float:left;clear:both;">
				<input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.set_remove') ?>"
                           ng-click="process('/game-server-api/promotion/online/award/set')"/>
				 <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.look_set') ?>"
                           ng-click="look('/game-server-api/promotion/online/award/look')"/>
				 <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.set_clean') ?>"
                           ng-click="processClean('/game-server-api/promotion/online/award/set')"/>
                </div>
				<br>
				<br>
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