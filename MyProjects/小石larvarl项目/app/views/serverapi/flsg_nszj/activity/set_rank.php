<script src="/js/auto_input.js"></script>
<script>
	function setRankAwardController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.set_from = function(url) {
			$scope.formData.is_look=0;
			<?php for($i = 1; $i <= 30; $i++) {?>
				$scope.formData.item_id<?php echo $i?> = document.getElementById("item_id<?php echo $i?>").value;
			<?php }?>
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data;
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
		};
		$scope.look_from = function(url) {
			$scope.formData.is_look=1; 
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if(data.status == 'error'){
					alertService.add('danger', data.msg);
				}else{
					$scope.items = data;
				}
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>
<div class="col-xs-12" ng-controller="setRankAwardController">
	<div class="row">
		<div class="col-xs-8">
				<div class="form-group">
					<div class="col-md-6" style="padding-left: 0;">
						<select class="form-control" name="activity_type"
							ng-model="formData.activity_type" ng-init="formData.activity_type=0">
							<option value="0"><?php echo Lang::get('serverapi.select_game_item') ?></option>
					 		<?php if('flsg' == $game_code){ ?>
							<option value="71"><?php echo Lang::get('serverapi.fruit_rank') ?></option>
							<option value="83"><?php echo Lang::get('serverapi.water_day') ?></option>	
					 		<?php }elseif('nszj' == $game_code){?>
					 		<option value="117"><?php echo Lang::get('serverapi.consume_give_gift') ?></option>
					 		<?php }?>
					 	</select>
					</div>
					<div class="col-md-6" style="padding-right: 0;">
						<select class="form-control" name="enter_type"
							ng-model="formData.enter_type" ng-init="formData.enter_type=1" disabled="false">
							<option value="1"><?php echo Lang::get('serverapi.set_rank_by_select') ?></option>
							<option value="2"><?php echo Lang::get('serverapi.set_rank_by_enter') ?></option>	
						</select>
					</div>
				</div>
				<div class="form-group" style="padding-top: 30px;">
	                <select class="form-control" name="server_id"
	                        id="select_game_server" ng-model="formData.server_id"
	                        ng-init="formData.server_id=0">
	                        <option value="0"><?php echo Lang::get('serverapi.select_main_game_server')?></option>
	                        <?php foreach ($servers as $k => $v) { ?>
	                            <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
	                        <?php } ?>
	                </select>
	            </div>
				<div class="form-group" ng-if ="formData.enter_type == 2">
					<textarea name="text_data" ng-model="formData.text_data"
						placeholder="<?php echo Lang::get('serverapi.set_rank_tip') ?>"
						rows="12" required class="form-control"></textarea>
				</div>
				<div class="clearfix"></div>
				<div class="panel panel-primary" ng-show="formData.enter_type == 1">
					<div class="panel-heading">
						<h3 class="panel-title">
						<?php echo Lang::get('serverapi.select_item_num');?>
						</h3>
					</div>
					<div class="panel-body">
					<?php for($i = 1; $i <= 30; $i++) {?>
						<div class="form-group" style="height: 30px;">
							<div class="col-md-4" style="padding: 2">
								<input type="text" class="form-control" name="item_id<?php echo $i?>" ng-model="formData.item_id<?php echo $i?>" 
								  style="overflow-y:auto;" id="item_id<?php echo $i?>" onkeyup="autoComplete<?php echo $i?>.start(event)" 
								  autocomplete="off" placeholder="<?php echo Lang::get('serverapi.goods_id') ?>">
								<div class="auto_hidden" style="overflow-y:auto;max-height:400px;" id="auto<?php echo $i?>"><!--自动完成 DIV--></div>
							</div>
							<div class="col-md-4" style="padding: 2">
								<input type="text" class="form-control"
									ng-model="formData.amount<?php echo $i?>"
									name="amount<?php echo $i?>"
									placeholder="<?php echo Lang::get('serverapi.enter_amount') ?>"/>
							</div>
						</div>
					<?php }?>
					</div>
				</div>
				<div class="form-group" style="height: 30px;">
					<br/>
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.set_rank_tip2') ?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<br/>
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.set_rank_tip3') ?></span>
				</div>
				<br>
				<br>
				<div style="padding-top:5px;">
				<label>
				<input type="checkbox" name="is_clean" ng-model="formData.is_clean" ng-init="formData.is_clean=0" ng-true-value="1" ng-false-value="0"/>
				  设置并清除原来设置的奖励
				</label>
				<input type="button" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_set') ?>"
				ng-click="set_from('/game-server-api/rank/award/set')"/>
				<input type="button" class="btn btn-info" value="<?php echo Lang::get('basic.btn_check') ?>"
				ng-click="look_from('/game-server-api/rank/award/set')"/>
				</div>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<div class="col-md-4">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><b>item</b></td>
						<td><b>number</b></td>
						<td><b>rank</b></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in items">
						<td>{{t.item_name}}</td>
						<td>{{t.item_num}}</td>
						<td>{{t.rank}}</td>
						</td>
					</tr>
				</tbody>
			
			</table>
		</div>
	</div>

</div>
<script>
<?php for($i = 1; $i <= 30; $i++) {?>
    var autoComplete<?php echo $i?>=new AutoComplete(<?php echo "'item_id$i'" ?> ,<?php echo "'auto$i'" ?>,[<?php 
    	foreach ($item as $value) {
    		echo "'".$value."',";
    	} ?>
    ]);
 <?php }?>
</script>