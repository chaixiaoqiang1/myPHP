<script>
	function changeYuanbaoController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			// if($scope.formData.operate_type==1 && $scope.formData.change_type==1 && $scope.formData.amount >= 10000){	//这里是判断如果增加10000元宝则需要确认
			// 	if(!confirm("<?php echo Lang::get('slave.please_confirm'); ?>")){
			// 		return;
			// 	}
			// }
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="changeYuanbaoController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/change-yuanbao" method="post"
				role="form"
				ng-submit="processFrom('/game-server-api/change-yuanbao')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control"
							ng-model="formData.player_id" name="player_id"	required
							placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
					</div>
					<div class="col-md-6" style="padding: 2">
						<input type="text" class="form-control" ng-model="formData.amount"
							name="amount"	required
							placeholder="<?php echo Lang::get('serverapi.enter_amount') ?>" />
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<select class="form-control" name="operate_type"
							ng-model="formData.operate_type"
							ng-init="formData.operate_type=0">
							<option value="0"><?php echo Lang::get('serverapi.operate_type') ?></option>
							<option value="1"><?php echo Lang::get('serverapi.yuanbao_add') ?></option>
							<option value="2"><?php echo Lang::get('serverapi.yuanbao_sub') ?></option>
						</select>
					</div>
					<div class="col-md-6" style="padding: 2">
						<select class="form-control" name="change_type"
							id="select_change_type" ng-model="formData.change_type"
							ng-init="formData.change_type=0">
							<?php if('yysg' == $game_code){?>
							<option value="0"><?php echo Lang::get('serverapi.change_type') ?></option>
							<option value="1"><?php echo Lang::get('serverapi.yuanbao') ?></option>
							<option value="3"><?php echo Lang::get('serverapi.tongqian') ?></option>
							<option value="13">体力</option>
							<?php }else{ ?>
							<option value="0"><?php echo Lang::get('serverapi.change_type') ?></option>
							<option value="1"><?php echo Lang::get('serverapi.yuanbao') ?></option>
							<option value="2"><?php echo Lang::get('serverapi.yuanbao_vip') ?></option>
							<option value="3"><?php echo Lang::get('serverapi.tongqian') ?></option>
							<option value="4"><?php echo Lang::get('serverapi.yueli') ?></option>
							<option value="5"><?php echo Lang::get('serverapi.gongxuan') ?></option>
							<option value="6"><?php echo Lang::get('serverapi.jingyan') ?></option>
							<option value="7"><?php echo Lang::get('serverapi.tianfudian') ?></option>
							<option value="8"><?php echo Lang::get('serverapi.jitianling') ?></option>
							<option value="9"><?php echo Lang::get('serverapi.chongwu_shilian') ?></option>
							<option value="10"><?php echo Lang::get('serverapi.chongwu_yuanshi') ?></option>
							<option value="11"><?php echo Lang::get('serverapi.chongwu_jinengjinghua') ?></option>
							<option value="12"><?php echo Lang::get('serverapi.jiezhijingyan') ?></option>
							<option value="14"><?php echo Lang::get('serverapi.power') ?></option>
							<?php if('flsg' == $game_code){?>
								<option value="15"><?php echo Lang::get('serverapi.battle_spirits') ?></option>
								<option value="17"><?php echo Lang::get('serverapi.yuanshen_jingpo') ?></option>
								<option value="18"><?php echo Lang::get('serverapi.jinengshu') ?></option>
							<?php }elseif ('nszj' == $game_code) {?>
								<option value="16"><?php echo Lang::get('serverapi.start_fragment') ?></option>
							<?php }?>
							
							<?php } ?>
						</select>
					</div>
				</div>
				<?php if('flsg' == $game_code){?>
					<div class="form-group" style="padding: 0" ng-if="formData.change_type == 1 && formData.operate_type == 2">
						<select class="form-control" name="sub_yuanbao_type"
							ng-model="formData.sub_yuanbao_type"
							ng-init="formData.sub_yuanbao_type=1">
							<option value="0"><?php echo Lang::get('serverapi.sub_yuanbao_type0') ?></option>
							<option value="1"><?php echo Lang::get('serverapi.sub_yuanbao_type1') ?></option>
						</select>
					</div>
				<?php }?>
				<b style="color:red">注意：夜夜三国体力和铜钱只能扣除不能增加。</b>
				<div class="clearfix">
					<br />
				</div>
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_change') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td><?php echo Lang::get('serverapi.player_id')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_server')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_amount')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_operate_type')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_type')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_operator')?></td>
						<td><?php echo Lang::get('serverapi.yuanbao_operate_time')?></td>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($yuanbao_logs as $k => $v) { ?>
		             <tr>
						<td><?php echo $v->player_id?></td>
						<td><?php echo $v->server_name?></td>
						<td><?php echo $v->amount?></td>
						<td><?php echo $v->operate_type?></td>
						<td><?php echo $v->change_type?></td>
						<td><?php echo $v->user_id?></td>
						<td><?php echo $v->created_at?></td>
					</tr>
	                   <?php } ?>	
					</tbody>

			</table>
		</div>
	</div>
</div>