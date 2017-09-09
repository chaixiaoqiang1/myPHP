<div class="col-xs-12" ng-controller="createUserController">
	<div class="row" >
		<div class="eb-content">
			<form action="/users" method="post" role="form" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<input type="text" class="form-control" id="username" placeholder="<?php echo Lang::get('user.username') ?>" required ng-model="formData.username" name="username" autofocus="autofocus"/> 
				</div>				
				<div class="form-group">
					<input type="text" class="form-control" id="nickname" placeholder="<?php echo Lang::get('user.nickname') ?>" ng-model="formData.nickname" name="nickname"/> 
				</div>		
				<div class="form-group">
					<input type="email" class="form-control" placeholder="Email" required ng-model="formData.email" name="email" /> 
				</div>				

				<div class="form-group">
					<input type="password" class="form-control" id="password" placeholder="<?php echo Lang::get('user.password') ?>" required ng-model="formData.password" name="password" /> 
				</div>				
				<div class="form-group">
					<input type="password" class="form-control" id="password_confirmation" placeholder="<?php echo Lang::get('user.password_confirmation') ?>" required ng-model="formData.password_confirmation" name="password_confirmation" /> 
				</div>				
				<div class="form-group">
					<select class="form-control" name="department_id" id="choose_department" ng-model="formData.department_id" ng-init="formData.department_id=0" required>
						<option value="0"><?php echo Lang::get('user.choose_department') ?></option>
						<?php foreach (Department::organization()->get() as $k => $v) { ?>
						<option value="<?php echo $v->department_id?>"><?php echo $v->department_name;?></option>
						<?php } ?>		
					</select>
				</div>		
				<div class="checkbox">
					<label>
						<input type="checkbox" value="1" ng-model="formData.is_admin" name="is_admin" ng-true-value="1" ng-false-value="0" /> 
						<?php echo Lang::get('user.is_admin') ?>
					</label>
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