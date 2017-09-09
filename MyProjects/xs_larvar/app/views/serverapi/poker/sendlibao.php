<script>
	function setBusinessman($scope,$http,alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.checkData = {};
		$scope.formData.giftbag_id = 0;
		$scope.check_statu = 'unsend';
		$scope.records = [];
		$scope.process = function(url){
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.giftbag_pass = 0;
			$scope.formData.start_time = $filter('date')($scope.formData.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.formData.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.formData.giftbag_id = 0;
				$scope.items = data;
				if($scope.items.is_ok == true){
					display_alert();
					alertService.add('success',$scope.items.player);
				}
				setTimeout('myrefresh()',100);
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};

		$scope.check_record = function(){
			$scope.records = [];
			$scope.checkData.check_record = 1;
			$scope.checkData.check_start_time = $filter('date')($scope.check_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.checkData.check_end_time = $filter('date')($scope.check_end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/poker/sendLibao',
				'data'   : $.param($scope.checkData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.records = data.records;
			}).error(function(data) {
				alert(data.error);
	        });			
		}

		$scope.show_content = function(record){	//用来解析礼包内容
			var items_string = record.items_string.split("+");
			var player_ids = record.player_ids.replace(/,/g, "\n");
			if(items_string.length != 5){
				alert('bad structure, can not read it!');
			}else{
				var title = items_string[0];
				var gold = items_string[1];
				var chips = items_string[2];
				var items = items_string[3];
				items = items.split("|");
				if(items.length > 0){
					var tmp = items[0].split("#");
					$scope.formData.item_id0 = tmp[0];
					$scope.formData.item_num0 = tmp[1];
					if(items.length > 1){
						var tmp = items[1].split("#");
						$scope.formData.item_id1 = tmp[0];
						$scope.formData.item_num1 = tmp[1];
					}
					if(items.length > 2){
						var tmp = items[2].split("#");
						$scope.formData.item_id2 = tmp[0];
						$scope.formData.item_num2 = tmp[1];
					}
					if(items.length > 3){
						var tmp = items[3].split("#");
						$scope.formData.item_id3 = tmp[0];
						$scope.formData.item_num3 = tmp[1];
					}
					if(items.length > 4){
						var tmp = items[4].split("#");
						$scope.formData.item_id4 = tmp[0];
						$scope.formData.item_num4 = tmp[1];
					}
				}
				var content = items_string[4];
				$scope.formData.diytitle = title;
				$scope.formData.diycontent = content;
				$scope.formData.gold = gold;
				$scope.formData.chips = chips;
				$scope.formData.players = player_ids;
			}
		}

		$scope.sendgiftbag = function(id, statu){
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.giftbag_pass = 1;
			$scope.formData.giftbag_id = id;
			$scope.formData.statu = statu;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/poker/sendLibao',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.formData.giftbag_id = 0;
				$scope.items = data;
				if($scope.items.is_ok == true){
					display_alert();
					alertService.add('success',$scope.items.player);
				}else{
					display_alert();
					alertService.add('success', data.msg);
				}
				setTimeout('myrefresh()',500);
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });			
		}

		$scope.switch = function (statu){
			$scope.check_statu = statu;
		}

		$scope.check = function(id, player_ids,gold,chips,goods_str,email1_str,email2_str){
			$scope.formData.giftbag_id = id;
			var title = ['Congratulations','Gift','Reimbursement'];
        	var content = ['Thank you for participate in our Facebook event, here is your rewards:',
        				'Here is your rewards for joining our event. Thank you for your participation.',
        				'Thank you for your support, here is a little gift from the house.',
        				'Here is reimbursement for you. Thank you for playing.'];
			var player_ids = player_ids;
			var pids = player_ids.join("\n");
			var gold = gold;
			var chips = chips;
			var goods_str = goods_str;

			if(goods_str.length == 2){
				$scope.formData.item_id0 = goods_str[0];
				$scope.formData.item_num0 = goods_str[1];
				$scope.formData.item_id1 = 0;
				$scope.formData.item_num1 = "";
				$scope.formData.item_id2 = 0;
				$scope.formData.item_num2 = "";
				$scope.formData.item_id3 = 0;
				$scope.formData.item_num3 = "";
				$scope.formData.item_id4 = 0;
				$scope.formData.item_num4 = "";
			}else if(goods_str.length == 4){
				$scope.formData.item_id0 = goods_str[0];
				$scope.formData.item_num0 = goods_str[1];
				$scope.formData.item_id1 = goods_str[2];
				$scope.formData.item_num1 = goods_str[3];
				$scope.formData.item_id2 = 0;
				$scope.formData.item_num2 = "";
				$scope.formData.item_id3 = 0;
				$scope.formData.item_num3 = "";
				$scope.formData.item_id4 = 0;
				$scope.formData.item_num4 = "";
			}else if(goods_str.length == 6){
				$scope.formData.item_id0 = goods_str[0];
				$scope.formData.item_num0 = goods_str[1];
				$scope.formData.item_id1 = goods_str[2];
				$scope.formData.item_num1 = goods_str[3];
				$scope.formData.item_id2 = goods_str[4];
				$scope.formData.item_num2 = goods_str[5];
				$scope.formData.item_id3 = 0;
				$scope.formData.item_num3 = "";
				$scope.formData.item_id4 = 0;
				$scope.formData.item_num4 = "";
			}else if(goods_str.length == 8){
				$scope.formData.item_id0 = goods_str[0];
				$scope.formData.item_num0 = goods_str[1];
				$scope.formData.item_id1 = goods_str[2];
				$scope.formData.item_num1 = goods_str[3];
				$scope.formData.item_id2 = goods_str[4];
				$scope.formData.item_num2 = goods_str[5];
				$scope.formData.item_id3 = goods_str[6];
				$scope.formData.item_num3 = goods_str[7];
				$scope.formData.item_id3 = goods_str[6];
				$scope.formData.item_num3 = goods_str[7];
				$scope.formData.item_id4 = 0;
				$scope.formData.item_num4 = "";
			}else if(goods_str.length == 10){
				$scope.formData.item_id0 = goods_str[0];
				$scope.formData.item_num0 = goods_str[1];
				$scope.formData.item_id1 = goods_str[2];
				$scope.formData.item_num1 = goods_str[3];
				$scope.formData.item_id2 = goods_str[4];
				$scope.formData.item_num2 = goods_str[5];
				$scope.formData.item_id3 = goods_str[6];
				$scope.formData.item_num3 = goods_str[7];
				$scope.formData.item_id4 = goods_str[8];
				$scope.formData.item_num4 = goods_str[9];
			}else{
				$scope.formData.item_id0 = 0;
				$scope.formData.item_num0 = "";
				$scope.formData.item_id1 = 0;
				$scope.formData.item_num1 = "";
				$scope.formData.item_id2 = 0;
				$scope.formData.item_num2 = "";
				$scope.formData.item_id3 = 0;
				$scope.formData.item_num3 = "";
				$scope.formData.item_id4 = 0;
				$scope.formData.item_num4 = "";
			}

			var email1_id =3;var email2_id =4;
			for (var i = 0; i < title.length; i++) {
				if(email1_str == title[i]){
					email1_id = i;
				}
			}
			if(email1_id != 3){
				$scope.formData.title = email1_id+1;
				$scope.formData.diytitle = '';
			}else{
				$scope.formData.title = 0;
				$scope.formData.diytitle = email1_str;
			}
			for (var j = 0; j < content.length; j++) {
				if(email2_str == content[j]){
					email2_id = j;
				}
			}
			if(email2_id != 4){
				$scope.formData.content = email2_id+1;
				$scope.formData.diycontent = '';
			}else{
				$scope.formData.content = 0;
				$scope.formData.diycontent = email2_str;
			}
			$scope.formData.players = pids;
			$scope.formData.gold = gold;
			$scope.formData.chips = chips;
		}
	}
	function display_alert()
	{
		alert('操作成功');
	}
</script>	
<div id='query' class="col-xs-12" ng-controller="setBusinessman">
	<div class="form-group">
		<div class="form-group" style="width:300px">
			<select class="form-control" name="sendtotype" ng-model="formData.sendtotype" ng-init="formData.sendtotype=0">
					<option value="0">发送给玩家</option>
					<option value="1">发送全服礼包</option>
			</select>
		</div>
	</div>
	<div class="form-group" ng-if="formData.sendtotype == 0">
			<textarea name="player" ng-model="formData.players" cols="112" rows = "10" placeholder="<?php echo Lang::get('serverapi.enter_players')?>"></textarea>
	</div>
	<div class="form-group" ng-if="formData.sendtotype == 1">
		<p><b>选择礼包有效期(同一时间只能有一种礼包，如果要取消补偿，把开始时间和结束时间都调整到当前时间之前即可)</b></p>
		<div class="col-md-8" style="padding: 0 0 0 0">
			<div class="col-md-4">
				<div class="input-group">
					<quick-datepicker ng-model="formData.start_time" init-value="00:00:00"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
			</div>
			<div class="col-md-4">
				<div class="input-group">
					<quick-datepicker ng-model="formData.end_time" init-value="23:59:59"></quick-datepicker>
					<i class="glyphicon glyphicon-calendar"></i>
				</div>
			</div>
		</div></br></br>
	</div>
	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:560px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.reward_money');?>" name="chips" ng-model="formData.chips">
		</div>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:560px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_golds');?>" name="gold" ng-model="formData.gold">
		</div>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<select class="form-control" name="item_id" ng-model="formData.item_id0" ng-init="formData.item_id0=0">
					<option value="0"><?php echo Lang::get('serverapi.select_item')?></option>
					<?php foreach ($items as $key => $value) {?>
					<option value="<?php echo $value->Id?>"><?php echo $value->Id .'--'.$value->Name?></option>
					<?php }?>
			</select>
		</div>
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.enter_item_num');?>" name="item_num0" ng-model="formData.item_num0">
		</div>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<select class="form-control" name="item_id" ng-model="formData.item_id1" ng-init="formData.item_id1=0">
					<option value="0"><?php echo Lang::get('serverapi.select_item')?></option>
					<?php foreach ($items as $key => $value) {?>
					<option value="<?php echo $value->Id?>"><?php echo $value->Id .'--'.$value->Name?></option>
					<?php }?>
			</select>
		</div>
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.enter_item_num');?>" name="item_num1" ng-model="formData.item_num1">
		</div>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<select class="form-control" name="item_id" ng-model="formData.item_id2" ng-init="formData.item_id2=0">
					<option value="0"><?php echo Lang::get('serverapi.select_item')?></option>
					<?php foreach ($items as $key => $value) {?>
					<option value="<?php echo $value->Id?>"><?php echo $value->Id .'--'.$value->Name?></option>
					<?php }?>
			</select>
		</div>
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.enter_item_num');?>" name="item_num2" ng-model="formData.item_num2">
		</div>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<select class="form-control" name="item_id" ng-model="formData.item_id3" ng-init="formData.item_id3=0">
					<option value="0"><?php echo Lang::get('serverapi.select_item')?></option>
					<?php foreach ($items as $key => $value) {?>
					<option value="<?php echo $value->Id?>"><?php echo $value->Id .'--'.$value->Name?></option>
					<?php }?>
			</select>
		</div>
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.enter_item_num');?>" name="item_num3" ng-model="formData.item_num3">
		</div>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<select class="form-control" name="item_id" ng-model="formData.item_id4" ng-init="formData.item_id4=0">
					<option value="0"><?php echo Lang::get('serverapi.select_item')?></option>
					<?php foreach ($items as $key => $value) {?>
					<option value="<?php echo $value->Id?>"><?php echo $value->Id .'--'.$value->Name?></option>
					<?php }?>
			</select>
		</div>
		<div class="col-md-6" style="padding: 0 ;width:160px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.enter_item_num');?>" name="item_num4" ng-model="formData.item_num4">
		</div>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:800px">
			<select class="form-control" name="title" ng-model="formData.title" ng-init="formData.title=0">
					<option value="0"><?php echo Lang::get('pokerData.defaultTitle')?></option>
					<?php foreach ($title as $key => $value) {?>
					<option value="<?php echo $key+1; ?>"><?php echo $value;?></option>
					<?php }?>
			</select>
		</div>
	</div>

	<div class="form-group">
			<textarea name="player" ng-model="formData.diytitle" cols="112" rows = "1" placeholder="<?php echo Lang::get('pokerData.diyTitle')?>"></textarea>
	</div>

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:800px">
			<select class="form-control" name="content" ng-model="formData.content" ng-init="formData.content=0">
					<option value="0"><?php echo Lang::get('pokerData.defaultContent')?></option>
					<?php foreach ($content as $key => $value) {?>
					<option value="<?php echo $key+1; ?>"><?php echo $value ?></option>
					<?php }?>
			</select>
		</div>
	</div>

	<div class="form-group">
			<textarea name="player" ng-model="formData.diycontent" cols="112" rows = "10" placeholder="<?php echo Lang::get('pokerData.diyContent')?>"></textarea>
	</div>


	<input type='button' class="btn btn-primary"
			value="<?php echo '提交' ?>"
	ng-click="process('/game-server-api/poker/sendLibao')" />
	<input type='button' class="btn btn-primary"
			value="<?php echo '审核未处理礼包'; ?>"
	ng-click="switch('unsend')" />
	<input type='button' class="btn btn-primary"
			value="<?php echo '查询已处理礼包'; ?>"
	ng-click="switch('sent')" />

	<div class='row margin-top-10'>
		<div class='col-xs-6'>
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<br/>
	<div class="col-xs-12" ng-show="check_statu=='unsend'" style="min-height:500px">
		<b>审核由GM创建但尚未发送的礼包</b>
		<table class="table table-striped">	
				<thead>
					<tr class="info">
						<td><?php echo Lang::get('slave.creater'); ?></td>
						<td>创建时间</td>
						<td>发放时间</td>
						<td>发送玩家ID</td>
						<td>礼包内容</td>
						<td></td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($giftbags as $giftbag) { ?>
						<tr>
							<td><?php echo $giftbag->operator; ?></td>
							<td><?php echo date('Y-m-d H:i:s', $giftbag->created_time); ?></td>
							<td ng-if="<?php echo ($giftbag->send_time>0 ? 1 : 0); ?>"><?php echo date('Y-m-d H:i:s', $giftbag->send_time); ?></td>
							<td ng-if="<?php echo ($giftbag->send_time>0 ? 0 : 1); ?>">--</td>
							<td><?php echo $giftbag->player_ids; ?></td>
							<td><?php echo $giftbag->items_string; ?></td>
							<!--<td><input type="button" value="通过" ng-click="sendgiftbag(<?php echo $giftbag->id; ?>, 1)" /></td> !-->
							<td><input type="button" value="不通过" ng-click="sendgiftbag(<?php echo $giftbag->id; ?>, 9)" /></td>
							<td><input type="button" value="查看" ng-click="check(<?php echo $giftbag->id;?>,[<?php echo $giftbag->player_ids;?>],<?php echo $gold[$giftbag->id];?>,<?php echo $chips[$giftbag->id];?>,[<?php echo $goods_str[$giftbag->id];?>],'<?php echo $email1_str[$giftbag->id];?>','<?php echo $email2_str[$giftbag->id];?>')"/></td>
						</tr>
					<?php } ?>
				</tbody>
		</table>
	</div>
	<div class="col-xs-12" ng-show="check_statu=='sent'" style="min-height:500px">
		<b>查询发送记录</b>
		<div class="form-group">
			<div class="form-group">
				<div class="col-md-3">
					<div class="input-group">
						<quick-datepicker ng-model="check_start_time" init-value="00:00:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-3">
					<div class="input-group">
						<quick-datepicker ng-model="check_end_time" init-value="23:59:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
			</div>
			<div class="col-md-6" style="padding: 0 ;width:160px">
				<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('slave.creater');?>" name="creater" ng-model="checkData.creater">
			</div>
			<div class="form-group">
				<input type='button' class="btn btn-primary"
						value="<?php echo '查询' ?>"
				ng-click="check_record()" />
			</div>
		</div>
		<table class="table table-striped">	
				<thead>
					<tr class="info">
						<td><?php echo Lang::get('slave.creater'); ?></td>
						<td>创建时间</td>
						<td>发放时间</td>
						<td>发送玩家ID</td>
						<td>礼包内容</td>
						<td>Statu</td>
						<td></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="r in records">
						<td>{{r.operator}}</td>
						<td>{{r.created_time}}</td>
						<td>{{r.send_time}}</td>
						<td>{{r.player_ids}}</td>
						<td>{{r.items_string}}</td>
						<td>{{r.is_send}}</td>
						<td><input type="button" value="查看" ng-click="show_content(r)" /></td>
					</tr>
				</tbody>
		</table>
	</div>
</div>