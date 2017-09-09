<div class="col-xs-12" ng-controller="createGameController">
	<div class="row">
		<div class="eb-content">
			<form action="/games" method="post" role="form"
				ng-submit="processFrom('/games')" onsubmit="return false;">
				<div class="form-group">
					<label for="game_name"></label> <input type="text"
						class="form-control" id="game_name" autofocus="autofocus" required
						ng-model="formData.game_name" name="game_name"
						placeholder="<?php echo Lang::get('system.enter_game_name') ?>" />
				</div>
				<div class="form-group">
					<select class="form-control" name="game_code" ng-model="formData.game_code" ng-init="formData.game_code=0">
					<option value="0"><?php echo Lang::get('system.select_game_code') ?></option>	
					<?php foreach (GameCode::all() as $v) { ?>		
					<option value="<?php echo $v->game_code?>">
						<?php echo $v->game_name?>	
					</option>
					<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="game_type" ng-model="formData.game_type" ng-init="formData.game_type=0">
						<option value="0"><?php echo Lang::get('system.select_game_type') ?></option>		
						<option value="1"><?php echo Lang::get('system.web_game') ?></option>
						<option value="2"><?php echo Lang::get('system.mobile_game') ?></option>
					</select>
				</div>

				<div class="form-group">
					<select class="form-control" name="platform_id"
						ng-model="formData.platform_id" ng-init="formData.platform_id=0">
						<option value="0"><?php echo Lang::get('system.select_platform') ?></option>
						<?php foreach (Platform::all() as $k => $v) { ?>
						<option value="<?php echo $v->platform_id?>"><?php echo $v->platform_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					eb_api_url:
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('system.enter_eb_api_url')?>"
						ng-model="formData.eb_api_url" name="eb_api_url"?>
				</div>
				<div class="form-group">
					eb_api_key:
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('system.enter_eb_api_key')?>"
						ng-model="formData.eb_api_key" name="eb_api_key"?>
				</div>
				<div class="form-group">
					eb_api_secret_key:
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('system.enter_eb_api_secret_key')?>"
						ng-model="formData.eb_api_secret_key" name="eb_api_secret_key"?>
				</div>
				<div class="form-group">
					<label> <input type="checkbox" name="is_recommend"
						ng-model="formData.is_recommend" ng-init="formData.is_recommend=1"
						ng-true-value="1" ng-false-value="0" />
						<?php echo Lang::get('server.recommend_game');?>
					</label>
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
</div>