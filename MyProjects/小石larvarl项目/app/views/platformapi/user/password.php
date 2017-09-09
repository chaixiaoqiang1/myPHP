<div class="col-xs-12" ng-controller="updatePlatformUserPasswordController">
	<div class="row" >
		<div class="eb-content">
			<form action="/platform-api/user/pwd" method="post" role="form" ng-submit="processFrom('/platform-api/user/pwd')" onsubmit="return false;">
				<div class="form-group">
					<input type="text" class="form-control" id="uid" placeholder="<?php echo Lang::get('platformapi.enter_user_id') ?>" required ng-model="formData.uid" name="uid" /> 
				</div>				
				<div class="form-group">
					<input type="password" class="form-control" id="password" placeholder="<?php echo Lang::get('platformapi.user_new_password') ?>" required ng-model="formData.password" name="password" /> 
				</div>				
				<div class="form-group">
					<input type="password" class="form-control" id="password_confirmation" placeholder="<?php echo Lang::get('platformapi.user_repeat_password') ?>" required ng-model="formData.password_confirmation" name="password_confirmation" /> 
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