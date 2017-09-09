<script>
	function groupBuyController($scope, $http, alertService,$filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function (url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.is_clean = 0;
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
<div class="col-xs-12" ng-controller="groupBuyController">
	<div class="row">
		<div class="eb-content" style="padding: 2;width:90%;">
			<!-- <form action="/game-server-api/promotion/award/set" method="post"
				role="form"
				ng-submit="processFrom(/game-server-api/promotion/award/set)"
				onsubmit="return false;"> -->
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
				<div ><span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.group_buy_remind1')?></span><br/>
					<span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.group_buy_remind2')?></span><br/>
					<span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.group_buy_remind3')?></span><br>
					<span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.group_buy_remind4')?></span>
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
							<div class="col-md-4" style="padding: 2;width: 9%; float:left;">
								<label><input type="checkbox" name="is_remove<?php echo $i?>" value="0"
                              		ng-model="formData.is_remove<?php echo $i?>"/><?php echo Lang::get('serverapi.is_remove') ?>
                				</label>
							</div>
							<!--2-->
							<div class="col-md-4" style="padding: 2;width: 30%; float:left;">
								
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
							<!-- <div class="col-md-4" style="padding: 2;width: 11%; float:left;">
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.current_price<?php echo $i?>"
										name="current_price<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.current_price') ?>"/></br>
								</div>
							</div> -->
							<!--4-->
							<div class="col-md-4" style="padding: 2;width: 30%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.price<?php echo $i?>"
										name="price<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.price') ?>"/></br>
								</div>
							</div>
							<!--5-->
							<!-- <div class="col-md-4" style="padding: 2;width: 11%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.current_step<?php echo $i?>"
										name="current_step<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.current_step') ?>"/></br>
								</div>
							</div> -->
							<!--6-->
							<!-- <div class="col-md-4" style="padding: 2;width: 11%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.final_step<?php echo $i?>"
										name="final_step<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.final_step') ?>"/></br>
								</div>
							</div> -->
							<!--7-->
							<div class="col-md-4" style="padding: 2;width: 30%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.steps<?php echo $i?>"
										name="steps<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.steps') ?>"/></br>
								</div>
							</div>
							<!--8-->
							<!-- <div class="col-md-4" style="padding: 2;width: 11%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.real_num<?php echo $i?>"
										name="real_num<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.real_num') ?>"/></br>
								</div>
							</div> -->
							<!--9-->
							<!-- <div class="col-md-4" style="padding: 2;width: 11%; float:left;">
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.virtual_num<?php echo $i?>"
										name="virtual_num<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.virtual_num') ?>"/></br>
								</div>
							</div> -->

					</div>
				<?php }?>

					<div class="col-md-4" style="padding: 0;width: 15%; float:left;">
							
							<div class="col-md-4" style="padding: 2;width: 100%;">
								<select class="form-control" style="padding: 2;width: 100%;" name="item_id_change"
									id="select_item_id_change"
									ng-model="formData.item_id_change"
									ng-init="formData.item_id_change=0">
									<option value="0"><?php echo Lang::get('serverapi.select_googs') ?></option>
						<?php foreach ($item as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' : '.$v->name;?></option>
						<?php } ?>			
					</select>
							</div>

						</div>
						<div class="col-md-4" style="padding: 2;width: 15%; float:left;">
							
							<div class="col-md-4" style="padding: 2;width: 100%;">
								<input type="text" class="form-control" style="padding: 2;width: 100%;"
									ng-model="formData.delta"
									name="delta"
									placeholder="<?php echo Lang::get('serverapi.delta') ?>"/></br>
							</div>
						</div>

				</div>
				</div>

				

				<div style="float:left;clear:both;">
					<input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.set_remove') ?>"
                           ng-click="process('/game-server-api/promotion/group/buy/set')"/>
				 	<input type='button' class="btn btn-primary" style="padding: 2;"
                           value="<?php echo Lang::get('serverapi.look_set') ?>"
                           ng-click="look('/game-server-api/promotion/group/buy/look')"/>
                    <input type='button' class="btn btn-info" style="padding: 2;"
                           value="<?php echo Lang::get('serverapi.change_virtual') ?>"
                           ng-click="look('/game-server-api/promotion/group/buy/change')"/>
                	<input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.set_clean') ?>"
                           ng-click="processClean('/game-server-api/promotion/group/buy/set')"/>
                </div>
				<br>
				<br>
			<!-- </form> -->
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