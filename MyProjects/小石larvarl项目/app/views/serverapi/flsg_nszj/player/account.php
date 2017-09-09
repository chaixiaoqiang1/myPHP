<div class="col-xs-12" ng-controller="freezePlayerAccountController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/player/account" method="post"
				role="form"
				ng-submit="processFrom1('/game-server-api/player/account')"
				onsubmit="return false;">
				<div class="form-group">
					<span><?php echo Lang::get('serverapi.operate_nickname');?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData1.server_id"
						ng-init="formData1.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>

				<div class="form-group" style="height: 30px;">
					<select class="form-control" name="choice1" id="select_choice1"
						ng-model="formData1.choice1" ng-init="formData1.choice1=0">
						<option value="0"><?php echo Lang::get('serverapi.select_account_type') ?></option>
						<option value="1"><?php echo Lang::get('player.freeze_account') ?></option>
						<option value="2"><?php echo Lang::get('player.ban_chat') ?></option>
					</select>
				</div>

				<div class="form-group" style="height: 30px;">
					<div class="col-md-4" style="padding: 0">
						<input type="text" class="form-control" id="select_player_name"
							placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>"
							 ng-model="formData1.player_name" name="player_name" />
					</div>
					<div class="col-md-4" style="padding: 4">
						<input type="text" class="form-control" id="select_player_id"
							placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>"
							 ng-model="formData1.player_id" name="player_id" />
					</div>
				</div>

				<div class="form-group" style="height: 30px;">
					<input type="text" class="form-control" id="ban_days1"
						placeholder="<?php echo Lang::get('serverapi.enter_ban_days') ?>"
						required ng-model="formData1.ban_days1" name="ban_days1" />
				</div>

				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts1" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<!-- add -->

	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/player/account" method="post"
				role="form"
				ng-submit="processFrom2('/game-server-api/player/account')"
				onsubmit="return false;">
				<div class="form-group">
					<span><?php echo Lang::get('serverapi.operate_account');?></span>
				</div>
				<div class="form-group" style="height: 30px;">
					<select class="form-control" name="choice2" id="select_choice2"
						ng-model="formData2.choice2" ng-init="formData2.choice2=0">
						<option value="0"><?php echo Lang::get('serverapi.select_account_type') ?></option>
						<option value="1"><?php echo Lang::get('player.freeze_account') ?></option>
						<option value="2"><?php echo Lang::get('player.ban_chat') ?></option>
					</select>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-4" style="padding: 0">
						<input type="text" class="form-control" id="select_email"
							placeholder="<?php echo Lang::get('platformapi.enter_email') ?>"
							 ng-model="formData2.email" name="email" />
					</div>
					<div class="col-md-4 " style="padding: 4">
						<input type="text" class="form-control" id="select_user_uid"
							placeholder="<?php echo Lang::get('platformapi.enter_user_id') ?>"
							 ng-model="formData2.user_uid" name="user_uid" />
					</div>
				</div>

				<div class="form-group" style="height: 30px;">
					<input type="text" class="form-control" id="ban_days2"
						placeholder="<?php echo Lang::get('serverapi.enter_ban_days') ?>"
						required ng-model="formData2.ban_days2" name="ban_days2" />
				</div>

				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts2" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<!-- add -->
</div>