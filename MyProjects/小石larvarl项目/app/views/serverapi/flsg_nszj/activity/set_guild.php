<script src="/js/auto_input.js"></script>
<script>
	function guildAwardSetController($scope, $http, alertService,$filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function (url) {
			$scope.formData.title = 0;
			if ($scope.formData.is_timing == 1 && !confirm('确保所选时间和假日活动中对应活动的开始时间一样?')) {
			    return;
			}
			<?php for($i = 1; $i <= 30; $i++) {
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
		$scope.set_title = function(url){
			alertService.alerts = $scope.alerts;
			$scope.formData.title = 1;
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
<div class="col-xs-12" ng-controller="guildAwardSetController">
	<div class="row">
		<div class="col-xs-10" style="padding: 2;width:90%;">
				<div ng-show="formData.is_timing == 1">
				    <div class="input-group">
				        <quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
				        <i class="glyphicon glyphicon-calendar"></i>
				    </div>
				</div></br>
				<div class="form-group">
					<div class="col-md-3" style="padding-left:0">
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
				<div >
					<span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.guild_remind1')?></span><br/>
					<span style = "color:red; font-size:16px;width:50%"><?php echo Lang::get('serverapi.guild_remind2')?></span><br/>
				</div>
				<div class="clearfix"></div>
				<div class="panel panel-primary"style="float:left;clear:both;">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo Lang::get('serverapi.select_activity_award');?></h3>
					</div>
					
					<div class="panel-body" >
				<?php for($i = 1; $i <= 30; $i++) {?>
					<div class="form-group" style="height: 30px;">
							<div class="col-md-1" style="width: 6%; float:left;">
								<input type="text" class="form-control"
									ng-model="formData.day<?php echo $i?>"
									name="day<?php echo $i?>"
									placeholder="<?php echo Lang::get('serverapi.day') ?>"/>
							</div>
							<div class="col-md-1" style="width: 7%; float:left;">
								<input type="text" class="form-control"
									ng-model="formData.left_rank<?php echo $i?>"
									name="left_rank<?php echo $i?>"
									placeholder="<?php echo Lang::get('serverapi.rank') ?>"/>
							</div>
							<div class="col-md-1" style="width: 7%; float:left;">
								<input type="text" class="form-control"
									ng-model="formData.right_rank<?php echo $i?>"
									name="right_rank<?php echo $i?>"
									placeholder="<?php echo Lang::get('serverapi.rank') ?>"/>
							</div>
							<!--2-->
							<div class="col-md-4" style="width: 15%; float:left;">
								<select class="form-control" name="award_id_a<?php echo $i?>"
									id="select_award_id_a<?php echo $i?>"
									ng-model="formData.award_id_a<?php echo $i?>"
									ng-init="formData.award_id_a<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
									<?php foreach ($award as $k => $v) { ?>
									<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
									<?php } ?>	
								</select>
								<div class="col-md-4" style="width: 100%;"
									ng-show="formData.award_id_a<?php echo $i?> == 9">

									<input type="text" class="form-control" name="item_id_a<?php echo $i?>" ng-model="formData.item_id_a<?php echo $i?>" style="overflow-y:auto;" id="item_id_a<?php echo $i?>" onkeyup="autoComplete_a<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
									<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_a<?php echo $i?>"><!--自动完成 DIV--></div>			
								
									<div class="col-md-4" style="width: 100%;">
										<input type="text" class="form-control" style="width: 100%;"
											ng-model="formData.award_value_a<?php echo $i?>"
											name="award_value_a<?php echo $i?>"
											placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
									</div>
								</div>
								<div class="col-md-4" style="width: 100%;"
									ng-if="formData.award_id_a<?php echo $i?> != 0 && formData.award_id_a<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_a<?php echo $i?>"
										name="award_value_a<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--3-->
							<div class="col-md-4" style="width: 15%; float:left;">
								<select class="form-control" name="award_id_b<?php echo $i?>"
									id="select_award_id_b<?php echo $i?>"
									ng-model="formData.award_id_b<?php echo $i?>"
									ng-init="formData.award_id_b<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
							<div class="col-md-4" style="width: 100%;"
									ng-show="formData.award_id_b<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_b<?php echo $i?>" ng-model="formData.item_id_b<?php echo $i?>" style="overflow-y:auto;" id="item_id_b<?php echo $i?>" onkeyup="autoComplete_b<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_b<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="width: 100%;">
									<input type="text" class="form-control" style="padding: 1;width: 100%;"
										ng-model="formData.award_value_b<?php echo $i?>"
										name="award_value_b<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="width: 100%;"
									ng-if="formData.award_id_b<?php echo $i?> != 0 && formData.award_id_b<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_b<?php echo $i?>"
										name="award_value_b<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--4-->
							<div class="col-md-4" style="width: 15%; float:left;">
								<select class="form-control" name="award_id_c<?php echo $i?>"
									id="select_award_id_c<?php echo $i?>"
									ng-model="formData.award_id_c<?php echo $i?>"
									ng-init="formData.award_id_c<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
								<div class="col-md-4" style="width: 100%;"
									ng-show="formData.award_id_c<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_c<?php echo $i?>" ng-model="formData.item_id_c<?php echo $i?>" style="overflow-y:auto;" id="item_id_c<?php echo $i?>" onkeyup="autoComplete_c<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_c<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="width: 100%;">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_c<?php echo $i?>"
										name="award_value_c<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="width: 100%;"
									ng-if="formData.award_id_c<?php echo $i?> != 0 && formData.award_id_c<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_c<?php echo $i?>"
										name="award_value_c<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--5-->
							<div class="col-md-4" style="width: 15%; float:left;">
								<select class="form-control" name="award_id_d<?php echo $i?>"
									id="select_award_id_d<?php echo $i?>"
									ng-model="formData.award_id_d<?php echo $i?>"
									ng-init="formData.award_id_d<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
								<div class="col-md-4" style="width: 100%;"
									ng-show="formData.award_id_d<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_d<?php echo $i?>" ng-model="formData.item_id_d<?php echo $i?>" style="overflow-y:auto;" id="item_id_d<?php echo $i?>" onkeyup="autoComplete_d<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_d<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="width: 100%;">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_d<?php echo $i?>"
										name="award_value_d<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="width: 100%;"
									ng-if="formData.award_id_d<?php echo $i?> != 0 && formData.award_id_d<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_d<?php echo $i?>"
										name="award_value_d<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
							<!--6-->
							<div class="col-md-4" style="width: 15%; float:left;">
								<select class="form-control" name="award_id_e<?php echo $i?>"
									id="select_award_id_e<?php echo $i?>"
									ng-model="formData.award_id_e<?php echo $i?>"
									ng-init="formData.award_id_e<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.award_type') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
								<div class="col-md-4" style="width: 100%;"
									ng-show="formData.award_id_e<?php echo $i?> == 9">
									<input type="text" class="form-control" name="item_id_e<?php echo $i?>" ng-model="formData.item_id_e<?php echo $i?>" style="overflow-y:auto;" id="item_id_e<?php echo $i?>" onkeyup="autoComplete_e<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_e<?php echo $i?>"><!--自动完成 DIV--></div>			
								<div class="col-md-4" style="width: 100%;">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_e<?php echo $i?>"
										name="award_value_e<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
								</div>
								</div>
								<div class="col-md-4" style="width: 100%;"
									ng-if="formData.award_id_e<?php echo $i?> != 0 && formData.award_id_e<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="width: 100%;"
										ng-model="formData.award_value_e<?php echo $i?>"
										name="award_value_e<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/></br>
								</div>
							</div>
						<!--为了结构-->
						<div style="width: 0.1%; float:left;overflow: hidden;">
							<div class="col-md-4" style="width: 100%; float:left;">
								<select class="form-control" name="award_id_a<?php echo $i?>"
									id="select_award_id_a<?php echo $i?>"
									ng-model="formData.award_id_a<?php echo $i?>"
									ng-init="formData.award_id_a<?php echo $i?>=0">
									<option value="0"></option>
								</select>
								<div class="col-md-4" style="width: 100%;"
										ng-show="formData.award_id_a<?php echo $i?> == 9"></br>
										<input type="text" class="form-control" name="item_id_a<?php echo $i?>" ng-model="formData.item_id_a<?php echo $i?>" style="overflow-y:auto;" id="item_id_a<?php echo $i?>" onkeyup="autoComplete_a<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto_a<?php echo $i?>"><!--自动完成 DIV--></div>			
									<div class="col-md-4" style="width: 100%;">
										<input type="text" class="form-control" style="width: 100%;"
											ng-model="formData.award_value_a<?php echo $i?>"
											name="award_value_a<?php echo $i?>"
											placeholder="<?php echo Lang::get('serverapi.award_value') ?>"/>
									</div>
								</div>
								<div class="col-md-4" style="width: 100%;"
									ng-if="formData.award_id_a<?php echo $i?> != 0 && formData.award_id_a<?php echo $i?> != 9 ">
									<input type="text" class="form-control" style="width: 100%;"
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
				<div class="col-md-4" style="padding-left: 0">
					<input type='button' class="btn btn-warning"
                        value="<?php echo Lang::get('basic.btn_set') ?>"
                        ng-click="processFrom('/game-server-api/guild/award/set')"/>
                </div>
				<div class="col-md-6" style="padding-left: 0">
					<select class="form-control" name="title_area"
						ng-model="formData.title_area" ng-init="formData.title_area=0">
						<option value="0">台北</option>
						<option value="1">台中</option>
						<option value="2">台南</option>
					</select>
                </div>
                <div class="col-md-2">
					<input type='button' class="btn btn-warning"
                        value="<?php echo Lang::get('serverapi.set_guild_title') ?>"
                        ng-click="set_title('/game-server-api/guild/award/set')"/>
                </div>
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
<?php for($i = 1; $i <= 30; $i++) {
		for($j=65;$j<68;$j++){
			$temp=strtolower(chr($j));?>
	    	var autoComplete_<?php echo $temp.$i?>=new AutoComplete(<?php echo "'item_id_$temp$i'" ?> ,<?php echo "'auto_$temp$i'" ?>,[<?php 
	    	echo $item; ?>
	    ]);
 <?php 
		}
}?>
</script>