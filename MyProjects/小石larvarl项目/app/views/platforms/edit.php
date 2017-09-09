<div class="col-xs-12" ng-controller="updatePlatformController">
	<div class="row" >
		<div class="eb-content">
			<form action="/platforms/<?php echo $platform->platform_id; ?>" method="put" role="form" ng-submit="processFrom('/platforms/<?php echo $platform->platform_id; ?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="platform_name"></label>
					<input type="text" class="form-control" id="platform_name" ng-init="formData.platform_name='<?php echo $platform->platform_name?>'" required ng-model="formData.platform_name" name="region_name" /> 
				</div>				

				<div class="form-group">
					<label for="platform_url"></label>
					<input type="text" class="form-control" id="platform_url" ng-init="formData.platform_url='<?php echo $platform->platform_url?>'" required ng-model="formData.platform_url" name="platform_url" /> 
				</div>				

				<div class="form-group">
					<label for="choose_region"></label>	
					<select class="form-control" name="region_id" id="choose_region" ng-model="formData.region_id" ng-init="formData.region_id=<?php echo $platform->region_id;?>">
						<option value="0"><?php echo Lang::get('system.select_region') ?></option>
						<?php foreach (Region::all() as $k => $v) { ?>
						<option value="<?php echo $v->region_id?>"><?php echo $v->region_name;?></option>
						<?php } ?>		
					</select>
				</div>
				
				<div class="form-group">
					<select name="default_game_id" ng-model="formData.default_game_id" ng-init="formData.default_game_id=<?php echo $platform->default_game_id?>" class="form-control">
						<option value="0"><?php echo Lang::get('system.select_default_name');?></option>
						<?php 
						    foreach (Game::where('platform_id',  '=', $platform->platform_id)->get() as $k => $v) {
						?>
						    <option value="<?php echo $v->game_id?>"><?php echo $v->game_name;?></option>
						<?php
							}
						?>
					</select>
				</div>

				<div class="form-group">
					<label for="platform_api_url"></label>
					<input type="text" class="form-control" id="platform_api_url" required ng-model="formData.platform_api_url" name="platform_api_url" placeholder="<?php echo Lang::get('system.enter_platform_api_url')?>" ng-init="formData.platform_api_url='<?php echo $platform->platform_api_url?>'"/> 
				</div>				
				<div class="form-group">
					<label for="payment_api_url"></label>
					<input type="text" class="form-control" id="payment_api_url" required ng-model="formData.payment_api_url" name="payment_api_url" placeholder="<?php echo Lang::get('system.enter_payment_api_url')?>" ng-init="formData.payment_api_url='<?php echo $platform->payment_api_url?>'"/> 
				</div>				
				<div class="form-group">
					<label for="api_key"></label>
					<input type="text" class="form-control" id="api_key" required ng-model="formData.api_key" name="api_key" placeholder="<?php echo Lang::get('system.enter_platform_api_key')?>" ng-init="formData.api_key='<?php echo $platform->api_key?>'"/> 
				</div>				
				<div class="form-group">
					<label for="api_secret_key"></label>
					<input type="text" class="form-control" id="api_secret_key" required ng-model="formData.api_secret_key" name="api_secret_key" placeholder="<?php echo Lang::get('system.enter_platform_api_secret_key')?>" ng-init="formData.api_secret_key = '<?php echo $platform->api_secret_key ?>'"/> 
				</div>				
				
				<div class="form-group">
					<select name="default_currency_id" ng-model="formData.default_currency_id" ng-init="formData.default_currency_id=<?php echo $platform->default_currency_id?>" class="form-control">
						<option value="0"><?php echo Lang::get('system.select_currency');?></option>
						<?php 
						    foreach (Currency::all() as $k => $v) {
						?>
						    <option value="<?php echo $v->currency_id?>"><?php echo $v->currency_name;?></option>
						<?php
							}
						?>
					</select>
				</div>
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>