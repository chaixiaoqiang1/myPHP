<div class="col-xs-12" ng-controller="updateSettingController">
	<div class="row" >
		<div class="eb-content">
			<form action="/organizations/<?php echo $organ['organization_id']?>" method="put" role="form" ng-submit="processFrom('/organizations/<?php echo $organ['organization_id']?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="allowed_ips"></label>
					<input type="text" class="form-control" id="allowed_ips" ng-init="formData.allowed_ips='<?php echo $organ['allowed_ips']?>'" required ng-model="formData.allowed_ips" name="alowed_ips" /> 
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