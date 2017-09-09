<script src="/js/auto_input.js"></script>
<script>
	function sendMailController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.formData = {};
		$scope.click_time = new Date().getTime()/1000;
		$scope.processFrom = function(url) {
			if (!confirm('确定要发吗?')) {
			    return;
			}
			if(new Date().getTime()/1000 - $scope.click_time < 5){
				alert('请勿频繁点击，两次操作之间需要间隔5秒');
				return;
			}else{
				$scope.click_time = new Date().getTime()/1000;
				<?php for($i = 1; $i <= 6; $i++) {?>
					$scope.formData.item_id<?php echo $i?> = document.getElementById("item_id<?php echo $i?>").value;
				<?php }?>
				<?php if('nszj' == $game_code){
				for($i = 1; $i <= 6; $i++) {?>
					$scope.formData.mark_id<?php echo $i?> = document.getElementById("mark_id<?php echo $i?>").value;
				<?php }}?>
				alertService.alerts = $scope.alerts;
				$http({
					'method' : 'post',
					'url'	 : url,
					'data'   : $.param($scope.formData),
					'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
				}).success(function(data) {
					var result = data.result;
					var len = result.length;
					for (var i=0; i < len; i++) {
						if (result[i].status == 'ok') {
							alertService.add('success', result[i].msg);
						} else if (result[i]['status'] == 'error') {
		            		alertService.add('danger', result[i].msg);
						}
					}
				}).error(function(data) {
		            alertService.add('danger', data.error);
		        });
	        }
		};
	}
</script>
<div class="col-xs-12" ng-controller="sendMailController">
	<div class="row">
		<div class="col-xs-8">
				<div class="form-group">
					<select class="form-control" name="send_type"
						ng-model="formData.send_type" ng-init="formData.send_type=1">
						<option value="1"><?php echo Lang::get('serverapi.send_player_mail') ?></option>
						<option value="2"><?php echo Lang::get('serverapi.send_server_mail') ?></option>	
					</select>
				</div>
				<div class="form-group" ng-if="formData.send_type==1">
				    <label>
						<input type="radio" name="name_or_id" value="1"  ng-model="formData.name_or_id" ng-init="formData.name_or_id=1"  ng-checked="true"/>
						使用玩家palyer_id发
					</label>
					<label>
						<input type="radio" ng-model="formData.name_or_id" name="name_or_id" value="2"/>
						使用玩家player_name发
					</label>
                    <p class="text-danger"><?php echo Lang::get('serverapi.email_tip')?></p>
				</div>
				<div class="form-group" ng-if ="formData.send_type == 2">
					<select class="form-control" name="server_id" required
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0" multiple="multiple"
						ng-multiple="true" size=10 >
						<optgroup
							label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>
				<div class="form-group" ng-if ="formData.send_type == 1">
					<textarea name="gift_data" ng-model="formData.gift_data"
						placeholder="<?php echo Lang::get('serverapi.all_server_tip') ?>"
						rows="10" required class="form-control"></textarea>
				</div>
				<?php if('flsg' == $game_code){?>
					<div class="form-group col-md-8" style="padding: 0">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.enter_mail_title')?>"
							required ng-model="formData.mail_title" name="mail_title"?>
					</div>
					<div class="form-group col-md-4" ng-if ="formData.send_type == 2">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.min_level')?>"
							required ng-model="formData.need_level" name="need_level"?>
					</div>
				<?php }else{?>
					<div class="form-group" style="padding: 0">
						<input type="text" class="form-control"
							placeholder="<?php echo Lang::get('serverapi.enter_mail_title')?>"
							required ng-model="formData.mail_title" name="mail_title"?>
					</div>
				<?php }?>
				<?php if(59 == $game_id){ ?>
				 <div class="form-group col-md-3">
					<select class="form-control" name="area_id"
						id="area_id" ng-model="formData.area_id"
						ng-init="formData.area_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
						<option value="59"><?php echo Lang::get('serverapi.tw_area')?></option>
						<option value="65"><?php echo Lang::get('serverapi.hk_area')?></option>
					</select>
				</div>
				<?php } ?>
				<?php if(63 == $game_id){ ?>
				 <div class="form-group col-md-3">
					<select class="form-control" name="area_id"
						id="area_id" ng-model="formData.area_id"
						ng-init="formData.area_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
						<option value="63"><?php echo Lang::get('serverapi.uk_area')?></option>
						<option value="64"><?php echo Lang::get('serverapi.sg_area')?></option>
					</select>
				</div>
				<?php } ?>
				<div class="clearfix"></div>
				<div class="form-group">
					<textarea type="text" class="form-control" id="mail_body"
						placeholder="<?php echo Lang::get('serverapi.enter_mail_body') ?>"
						required ng-model="formData.mail_body" name="mail_body" rows="8"></textarea>
				</div>
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">
				<?php echo Lang::get('serverapi.select_gift_mail');?>
			</h3>
					</div>
					<div class="panel-body">
				<?php for($i = 1; $i <= 6; $i++) {?>
					<div class="form-group" style="height: 30px;">
							<div class="col-md-4" style="padding: 0">
								<select class="form-control" name="award_id<?php echo $i?>"
									id="select_award_id<?php echo $i?>"
									ng-model="formData.award_id<?php echo $i?>"
									ng-init="formData.award_id<?php echo $i?>=0">
									<option value="0"><?php echo Lang::get('serverapi.select_item') ?></option>
							<?php foreach ($award as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->id.' :　'.$v->cname;?></option>
						<?php } ?>	
					</select>
							</div>
							<div class="col-md-4" style="padding: 2"
								ng-show="formData.award_id<?php echo $i?> == 9">
								<input type="text" class="form-control" name="item_id<?php echo $i?>" ng-model="formData.item_id<?php echo $i?>" style="overflow-y:auto;" id="item_id<?php echo $i?>" onkeyup="autoComplete<?php echo $i?>.start(event)" 
									autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_gift_bag') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto<?php echo $i?>"><!--自动完成 DIV--></div>			
					
							</div>
							<?php if('nszj' == $game_code){?>
								<div class="col-md-4" style="padding: 2"
									ng-show="formData.award_id<?php echo $i?> == 8">
									<input type="text" class="form-control" name="mark_id<?php echo $i?>" ng-model="formData.mark_id<?php echo $i?>" style="overflow-y:auto;" id="mark_id<?php echo $i?>" onkeyup="markautoComplete<?php echo $i?>.start(event)" 
										autocomplete="off" placeholder="<?php echo Lang::get('serverapi.enter_mark') ?>">
									<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="mark_auto<?php echo $i?>"><!--自动完成 DIV--></div>			
								</div>
								<div class="col-md-4" style="padding: 2" ng-if="formData.award_id<?php echo $i?> != 8">
									<input type="text" class="form-control"
										ng-model="formData.amount<?php echo $i?>"
										name="amount<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.enter_amount') ?>"/>
								</div>
							<?php }else{?>
								<div class="col-md-4" style="padding: 2">
									<input type="text" class="form-control"
										ng-model="formData.amount<?php echo $i?>"
										name="amount<?php echo $i?>"
										placeholder="<?php echo Lang::get('serverapi.enter_amount') ?>"/>
								</div>
							<?php }?>
						</div>
				<?php }?>
				</div>


				</div>
				<div class="form-group" style="height: 30px;">
					<br/>
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.gift_mail')?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<br/>
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.gift_remind')?></span>
				</div>
				<br>
				<br>
				<div class="col-md-10">
					<div class="col-md-3">
						<input type="button" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit') ?>"
						ng-click="processFrom('/game-server-api/mail/gift-mail')"/>
					</div>
					<div class="col-md-3">
						<a href="/game-server-api/giftbag/lookuppage?app_id=146" target="_blank">
							<div class="btn btn-primary">
							<?php echo Lang::get('serverapi.search_gift_record')?>
							</div>
						</a>
					</div>
				</div>
				<div class="form-group col-md-12" ng-if ="formData.send_type == 2">
					<br/>
					<span style = "color:red; font-size:16px;"><b><?php echo Lang::get('serverapi.fail_gift_remind')?></b></span>
				</div>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<div class="col-md-6">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
			</div>
		</div>
	</div>

</div>
<script>
<?php for($i = 1; $i <= 6; $i++) {?>
    var autoComplete<?php echo $i?>=new AutoComplete(<?php echo "'item_id$i'" ?> ,<?php echo "'auto$i'" ?>,[<?php 
    	foreach ($item as $value) {
    		echo "'".$value."',";
    	} ?>
    ]);
 <?php }?>
 <?php if('nszj' == $game_code){ 
 	for($i = 1; $i <= 6; $i++){?>
 		var markautoComplete<?php echo $i?>=new AutoComplete(<?php echo "'mark_id$i'" ?> ,<?php echo "'mark_auto$i'" ?>,[<?php 
 			foreach ($marks as $value) {
 				echo "'".$value->markname. ':' .$value->markid ."',";
 			} ?>
 		]);
 <?php }}?>
</script>