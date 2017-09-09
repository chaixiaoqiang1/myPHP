<div class="col-xs-12" ng-controller="updateRegionController">
	<div class="row" >
		<div class="eb-content">
			<form action="/regions/<?php echo $region->region_id; ?>" method="put" role="form" ng-submit="processFrom('/regions/<?php echo $region->region_id; ?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="region_name"></label>
					<input type="text" class="form-control" id="region_name" ng-init="formData.region_name='<?php echo $region->region_name?>'" required ng-model="formData.region_name" name="region_name" /> 
				</div>				

				<div class="form-group">
					<label for="region_code"></label>
					<input type="text" class="form-control" id="region_code" ng-init="formData.region_code='<?php echo $region->region_code?>'" required ng-model="formData.region_code" name="region_code" /> 
				</div>				
				<div class="form-group">
					<input type="text" class="form-control"  ng-init="formData.timezone='<?php echo $region->timezone?>'" required ng-model="formData.timezone" name="timezone" /> 
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