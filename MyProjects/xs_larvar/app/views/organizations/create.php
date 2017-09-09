<div class="col-xs-12" ng-controller="createOrganizationController">
	<div class="row">
		<div class="eb-content">
			<form action="/organizations?>" method="post" role="form" ng-submit="processFrom('/organizations')" onsubmit="return false;">
				<div class="form-group">
					<label for="organ_name"></label>
					<input type="text" class="form-control" id="organ_name" placeholder="<?php echo Lang::get('system.enter_organ_name') ?>" required ng-model="formData.organ_name" name="organ_name" autofocus="autofocus" ng-autofocus="true" />
				</div>
				
				<div class="form-group">
					<label for="allowed_ips"></label>
					<input type="text" class="form-control" id="allowed_ips" placeholder="<?php echo Lang::get('system.enter_allowed_ips') ?>" required ng-model="formData.allowed_ips" name="allowed_ips" autofocus="autofocus" ng-autofocus="true" />
				</div>
				
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>
		</div>
	</div>
	
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>