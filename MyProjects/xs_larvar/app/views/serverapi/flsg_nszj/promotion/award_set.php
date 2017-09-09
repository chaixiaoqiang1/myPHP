<script src="/js/auto_input.js"></script>
<script>
	function awardSetController($scope, $http, alertService,$filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function (url) {
			if ($scope.formData.is_timing == 1 && !confirm('确保所选时间和假日活动中对应活动的开始时间一样?')) {
			    return;
			}
			<?php for($i = 1; $i <= 12; $i++) {
					for($j=65;$j<70;$j++){
						$temp=strtolower(chr($j));?>
				    	$scope.formData.item_id_<?php echo $temp.$i?> = document.getElementById("item_id_<?php echo $temp.$i?>").value;
			 <?php 
					}
			}?>
            alertService.alerts = $scope.alerts;
            $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                var result = data.result;
                if(result == undefined){
                	var result = data;
                	if (result.status == 'ok') {
                	    alertService.add('success', result.msg);
                	} else if (result['status'] == 'error') {
                	    alertService.add('danger', result.msg);
                	}
                }else{
                	var len = result.length;
                	for (var i = 0; i < len; i++) {
                	    if (result[i].status == 'ok') {
                	        alertService.add('success', result[i].msg);
                	    } else if (result[i]['status'] == 'error') {
                	        alertService.add('danger', result[i].msg);
                	    }
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
		}
	}
</script>
<div class="col-xs-12" ng-controller="awardSetController">
	<div class="row">
		<div class="col-xs-10" style="padding: 2;width:90%;">
				<div ng-show="formData.is_timing == 1">
				    <div class="input-group">
				        <quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
				        <i class="glyphicon glyphicon-calendar"></i>
				    </div>
				</div></br>
				<div class="form-group col-md-12">
					<div class="col-md-3">
						<select class="form-control" ng-model="formData.is_timing" ng-init="formData.is_timing=0">
						    <option value="0"><?php echo Lang::get('serverapi.common_set') ?></option>
						    <option value="1"><?php echo Lang::get('serverapi.timing_set') ?></option>
						</select>
					</div>
					<div class="col-md-3">
						<select class="form-control" ng-model="formData.award_type" ng-init="formData.award_type=0">
							<option value="0"> <?php echo Lang::get('serverapi.select_set_award') ?></option>
						<?php foreach ($award_types as $key => $value) { ?> 
							<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
						<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group col-md-12" ng-show="34 == formData.award_type">
					<p><b><?php echo Lang::get('serverapi.refill_big_rate_note'); ?></b></p>
					<div class="col-md-3">
						<input type="number" class="form-control"
							ng-model="formData.spring_recharge_goal"
							name="spring_recharge_goal" required
							placeholder="<?php echo Lang::get('serverapi.spring_recharge_goal') ?>"/>
					</div>
					<div class="col-md-3">
						<input type="number" class="form-control"
							ng-model="formData.spring_recharge_rebate"
							name="spring_recharge_rebate" required
							placeholder="<?php echo Lang::get('serverapi.spring_recharge_rebate') ?>"/>
					</div>
				</div>
				<div class="clearfix"><br/></div>
				<div class="form-group" style="padding: 2;width:55%;float:left;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=25>
						<optgroup
							label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div ><span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.activity_set_remind1')?></span><br/>
					<span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.activity_set_remind2')?></span><br/>
					<span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.activity_set_remind3')?></span><br>
				</div>
				
				<?php if(59 == $game_id){ ?>
				 <div class="form-group col-md-4">
					<select style="width:300;" name="area_id"
						id="area_id" ng-model="formData.area_id"
						ng-init="formData.area_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
						<option value="59"><?php echo Lang::get('serverapi.tw_area')?></option>
						<option value="65"><?php echo Lang::get('serverapi.hk_area')?></option>
					</select>
				</div>
				<?php } ?>
				<?php if(63 == $game_id){ ?>
				 <div class="form-group col-md-4">
					<select style="width:300;" name="area_id"
						id="area_id" ng-model="formData.area_id"
						ng-init="formData.area_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
						<option value="63"><?php echo Lang::get('serverapi.uk_area')?></option>
						<option value="64"><?php echo Lang::get('serverapi.sg_area')?></option>
					</select>
				</div>
				<?php } ?>
				<div class="clearfix"></div>
				
				<div class="panel panel-primary"style="float:left;clear:both;">
					<div class="panel-heading">
						<h3 class="panel-title">
				<?php echo Lang::get('serverapi.select_activity_award');?>
			</h3>
					</div>
					
					<div class="panel-body" >
				<?php for($i = 1; $i <= 12; $i++) {?>
					<div class="form-group" style="height: 30px;">
							<div class="col-md-4" style="padding: 2;width: 15%; float:left;">
								<input type="text" class="form-control"
									ng-model="formData.file_id<?php echo $i?>"
									name="file_id<?php echo $i?>"
									placeholder="<?php echo Lang::get('serverapi.file_number') ?>"/>
							</div>
							<!--2-->
							<div class="col-md-4" style="padding: 2;width: 15%; float:left;">
								<select class="form-control" name="award_id_a<?php echo $i?>"
									id="select_award_id_a<?php echo $i?>"
									ng-model="formData.award_id_a<?php echo $i?>"
									ng-init="formData.award_id_a<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-show="formData.award_id_a<?php echo $i?> == 9">

									<input type="text" class="form-control" name="item_id_a<?php echo $i?>" ng-model="formData.item_id_a<?php echo $i?>" style="overflow-y:auto;" id="item_id_a<?php echo $i?>" onkeyup="autoComplete_a<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_a<?php echo $i?>"><!--自动完成 DIV--></div>			
								
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_a<?php echo $i?>"
										name="award_value_a<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-if="formData.award_id_a<?php echo $i?> != 0 && formData.award_id_a<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_a<?php echo $i?>"
										name="award_value_a<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--3-->
							<div class="col-md-4" style="padding: 2;width: 15%; float:left;">
								<select class="form-control" name="award_id_b<?php echo $i?>"
									id="select_award_id_b<?php echo $i?>"
									ng-model="formData.award_id_b<?php echo $i?>"
									ng-init="formData.award_id_b<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
							<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-show="formData.award_id_b<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_b<?php echo $i?>" ng-model="formData.item_id_b<?php echo $i?>" style="overflow-y:auto;" id="item_id_b<?php echo $i?>" onkeyup="autoComplete_b<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_b<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_b<?php echo $i?>"
										name="award_value_b<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-if="formData.award_id_b<?php echo $i?> != 0 && formData.award_id_b<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_b<?php echo $i?>"
										name="award_value_b<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--4-->
							<div class="col-md-4" style="padding: 2;width: 15%; float:left;">
								<select class="form-control" name="award_id_c<?php echo $i?>"
									id="select_award_id_c<?php echo $i?>"
									ng-model="formData.award_id_c<?php echo $i?>"
									ng-init="formData.award_id_c<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-show="formData.award_id_c<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_c<?php echo $i?>" ng-model="formData.item_id_c<?php echo $i?>" style="overflow-y:auto;" id="item_id_c<?php echo $i?>" onkeyup="autoComplete_c<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_c<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_c<?php echo $i?>"
										name="award_value_c<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-if="formData.award_id_c<?php echo $i?> != 0 && formData.award_id_c<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_c<?php echo $i?>"
										name="award_value_c<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--5-->
							<div class="col-md-4" style="padding: 2;width: 15%; float:left;">
								<select class="form-control" name="award_id_d<?php echo $i?>"
									id="select_award_id_d<?php echo $i?>"
									ng-model="formData.award_id_d<?php echo $i?>"
									ng-init="formData.award_id_d<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-show="formData.award_id_d<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_d<?php echo $i?>" ng-model="formData.item_id_d<?php echo $i?>" style="overflow-y:auto;" id="item_id_d<?php echo $i?>" onkeyup="autoComplete_d<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_d<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_d<?php echo $i?>"
										name="award_value_d<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-if="formData.award_id_d<?php echo $i?> != 0 && formData.award_id_d<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_d<?php echo $i?>"
										name="award_value_d<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--6-->
							<div class="col-md-4" style="padding: 2;width: 15%; float:left;">
								<select class="form-control" name="award_id_e<?php echo $i?>"
									id="select_award_id_e<?php echo $i?>"
									ng-model="formData.award_id_e<?php echo $i?>"
									ng-init="formData.award_id_e<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-show="formData.award_id_e<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_e<?php echo $i?>" ng-model="formData.item_id_e<?php echo $i?>" style="overflow-y:auto;" id="item_id_e<?php echo $i?>" onkeyup="autoComplete_e<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_e<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="padding: 2;width: 100%;">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_e<?php echo $i?>"
										name="award_value_e<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-if="formData.award_id_e<?php echo $i?> != 0 && formData.award_id_e<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_e<?php echo $i?>"
										name="award_value_e<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
						<!--为了结构-->
						<div style="padding: 2;width: 0.1%; float:left;overflow: hidden;">
							<div class="col-md-4" style="padding: 2;width: 100%; float:left;">
								<select class="form-control" name="award_id_a<?php echo $i?>"
									id="select_award_id_a<?php echo $i?>"
									ng-model="formData.award_id_a<?php echo $i?>"
									ng-init="formData.award_id_a<?php echo $i?>=0">
									<option value="0"></option>
								</select>
								<div class="col-md-4" style="padding: 2;width: 100%;"
										ng-show="formData.award_id_a<?php echo $i?> == 9"></br>
										<input type="text" class="form-control" name="item_id_a<?php echo $i?>" ng-model="formData.item_id_a<?php echo $i?>" style="overflow-y:auto;" id="item_id_a<?php echo $i?>" onkeyup="autoComplete_a<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_a<?php echo $i?>"><!--自动完成 DIV--></div>			
									<div class="col-md-4" style="padding: 2;width: 100%;">
										<input type="text" class="form-control" style="padding: 2;width: 100%;"
											ng-model="formData.award_value_a<?php echo $i?>"
											name="award_value_a<?php echo $i?>"
											placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
									</div>
								</div>
								<div class="col-md-4" style="padding: 2;width: 100%;"
									ng-if="formData.award_id_a<?php echo $i?> != 0 && formData.award_id_a<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="padding: 2;width: 100%;"
										ng-model="formData.award_value_a<?php echo $i?>"
										name="award_value_a<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
						</div>

					</div>
				<?php }?>
				</div>
				</div>
				<div style="float:left;clear:both;">
				<input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('basic.btn_set') ?>"
                           ng-click="processFrom('/game-server-api/promotion/award/set')"/>&nbsp&nbsp&nbsp
				<input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.look_set') ?>"
                           ng-click="look('/game-server-api/promotion/award/set/look')"/>
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
<script>
<?php for($i = 1; $i <= 12; $i++) {
		for($j=65;$j<70;$j++){
			$temp=strtolower(chr($j));?>
	    	var autoComplete_<?php echo $temp.$i?>=new AutoComplete(<?php echo "'item_id_$temp$i'" ?> ,<?php echo "'auto_$temp$i'" ?>,[<?php 
	    	foreach ($item as $value) {
	    		echo "'".$value."',";
	    	} ?>
	    ]);
 <?php 
		}
}?>
</script>