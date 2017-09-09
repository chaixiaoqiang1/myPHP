<div class="col-xs-12" ng-controller="createRegionController">
	<div class="row" >
		<div class="eb-content">
			<form action="/regions" method="post" role="form" ng-submit="processFrom('/regions')" onsubmit="return false;">
				<div class="form-group">
					<label for="region_name"></label>
					<input type="text" class="form-control" id="region_name" autofocus="autofocus" required ng-model="formData.region_name" name="region_name" placeholder="<?php echo Lang::get('system.enter_region_name') ?>"/> 
				</div>				

				<div class="form-group">
					<label for="region_code"></label>
					<input type="text" class="form-control" id="region_code" required ng-model="formData.region_code" name="region_code" placeholder="<?php echo Lang::get('system.enter_region_code')?>"/> 
				</div>				
				<div class="form-group">
					<label for="timezone"></label>
					<input type="text" class="form-control"  required ng-model="formData.timezone" name="timezone" placeholder="<?php echo Lang::get('system.enter_timezone')?>"/> 
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