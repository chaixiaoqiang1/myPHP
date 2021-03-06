<div class="col-xs-12" ng-controller="createPlatformController">
	<div class="row" >
		<div class="eb-content">
			<form action="/platforms" method="post" role="form" ng-submit="processFrom('/platforms')" onsubmit="return false;">
				<div class="form-group">
					<label for="platform_name"></label>
					<input type="text" class="form-control" id="platform_name" autofocus="autofocus" required ng-model="formData.platform_name" name="platform_name" placeholder="<?php echo Lang::get('system.enter_platform_name') ?>"/> 
				</div>				

				<div class="form-group">
					<label for="platform_url"></label>
					<input type="text" class="form-control" id="platform_url" required ng-model="formData.platform_url" name="platform_url" placeholder="<?php echo Lang::get('system.enter_platform_url')?>"/> 
				</div>				
				<div class="form-group">
					<select class="form-control" name="region_id" ng-model="formData.region_id" ng-init="formData.region_id=0">
						<option value="0"><?php echo Lang::get('system.select_region') ?></option>
						<?php foreach (Region::all() as $k => $v) { ?>
						<option value="<?php echo $v->region_id?>"><?php echo $v->region_name;?></option>
						<?php } ?>		
					</select>
				</div>

				<div class="form-group">
					<label for="platform_api_url"></label>
					<input type="text" class="form-control" id="platform_api_url" required ng-model="formData.platform_api_url" name="platform_api_url" placeholder="<?php echo Lang::get('system.enter_platform_api_url')?>"/> 
				</div>				
				<div class="form-group">
					<label for="payment_api_url"></label>
					<input type="text" class="form-control" id="payment_api_url" required ng-model="formData.payment_api_url" name="payment_api_url" placeholder="<?php echo Lang::get('system.enter_payment_api_url')?>"/> 
				</div>				
				<div class="form-group">
					<label for="api_key"></label>
					<input type="text" class="form-control" id="api_key" required ng-model="formData.api_key" name="api_key" placeholder="<?php echo Lang::get('system.enter_platform_api_key')?>"/> 
				</div>				
				<div class="form-group">
					<label for="api_secret_key"></label>
					<input type="text" class="form-control" id="api_secret_key" required ng-model="formData.api_secret_key" name="api_secret_key" placeholder="<?php echo Lang::get('system.enter_platform_api_secret_key')?>"/> 
				</div>				
				<div class="form-group">
					<select class="form-control" name="default_currency_id" ng-model="formData.default_currency_id" ng-init="formData.default_currency_id=0">
						<option value="0"><?php echo Lang::get('system.select_currency') ?></option>
						<?php foreach (Currency::all() as $k => $v) { ?>
						<option value="<?php echo $v->currency_id?>"><?php echo $v->currency_name;?></option>
						<?php } ?>		
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