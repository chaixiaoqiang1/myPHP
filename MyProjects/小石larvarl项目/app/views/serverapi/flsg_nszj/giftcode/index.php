<div class="col-xs-12" ng-controller="searchGiftCodeController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/gift-code" method="post" role="form"
				ng-submit="processFrom('/game-server-api/gift-code')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="gift_type" id="form_type"
						ng-model="formData.type" ng-init="formData.type=0">
						<option value="0"><?php echo Lang::get('serverapi.select_gift_type') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('serverapi.enter_gift_code')?>"
						ng-model="formData.gift_code" name="gift_code"?>
				</div>
				<div class="form-group">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('serverapi.enter_player_id')?>"
						ng-model="formData.player_id" name="player_id"?>
				</div>
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
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
						<td><?php echo Lang::get('serverapi.gift_code')?></td>
						<td><?php echo Lang::get('serverapi.code_name')?></td>
						<td><?php echo Lang::get('serverapi.gift_used_player_id')?></td>
						<td><?php echo Lang::get('serverapi.gift_used_player_name')?></td>
						<td><?php echo Lang::get('serverapi.gift_used_server_name')?></td>
						<td><?php echo Lang::get('serverapi.gift_used_time')?></td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="code in codes">
						<td ng-if="code.is_used==1">{{code.Code}}</td>
						<td ng-if="code.is_used==1">{{code.code_name}}</td>
						<td ng-if="code.is_used==1">{{code.Used_PlayerID}}</td>
						<td ng-if="code.is_used==1">{{code.player_name}}</td>
						<td ng-if="code.is_used==1">{{code.server_name}}</td>
						<td ng-if="code.is_used==1">{{code.UsedTime}}</td>
					</tr>
					</body>
			
			</table>
		</div>
	</div>

</div>