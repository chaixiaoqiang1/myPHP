<script>
	function setBusinessman($scope,$http,alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
	function display_alert()
	{
		alert('操作成功');
	}
</script>	
<div id='query' class="col-xs-12" ng-controller="setBusinessman">
	<div class="form-group">
		<div class="form-group" style="width:300px">
			<select class="form-control" name="sendtotype" ng-model="formData.sendtotype" ng-init="formData.sendtotype=0" disabled="true">
					<option value="0">发送给玩家</option>
			</select>
		</div>
	</div>
	<div class="form-group" ng-if="formData.sendtotype == 0">
			<textarea name="player" ng-model="formData.players" cols="112" rows = "10" placeholder="<?php echo Lang::get('serverapi.enter_players')?>"></textarea>
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
	ng-click="process('/game-server-api/poker/createLibao')" />

	<div class='row margin-top-10'>
		<div class='col-xs-6'>
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>