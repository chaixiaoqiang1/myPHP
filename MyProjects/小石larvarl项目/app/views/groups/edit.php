<div class="col-xs-12" ng-controller="updateGroupNameController">
	<h4><?php echo Lang::get('system.group_edit') ?></h4>
	<div class="row" >
		<div class="eb-content">
			<form action="/groups/<?php echo $group->group_id; ?>" method="put" role="form" ng-submit="processFrom('/groups/<?php echo $group->group_id; ?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="group_name"></label>
					<input type="text" class="form-control" id="group_name" ng-init="formData.group_name='<?php echo $group->group_name?>'" required ng-model="formData.group_name" name="group_name" /> 
				</div>				
				<input type="hidden" name="type" ng-value="formData.type='name'"/>	
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

<div class="col-xs-12" ng-controller="updateGroupAppController">
	<h4><?php echo Lang::get('system.group_app') ?></h4>
	<div class="row" >
		<div class="eb-content">
			<form action="/groups/<?php echo $group->group_id?>" method="put" role="form" ng-submit="processFrom('/groups/<?php echo $group->group_id?>')" onsubmit="return false;">
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>
				<?php foreach(EastblueApp::orderBy('app_key')->get() as $k => $v) { ?>
					<?php $checked_value = 0 ?>
				<?php if (in_array($v->app_id, $group->apps())) { ?>
					<?php $checked_value = $v->app_id ?>
				<?php } ?>
				<div class="checkbox">
					<label>
					<input type="checkbox" value="<?php echo $v->app_id;?>" ng-model="formData.apps[<?php echo $v->app_id; ?>]" ng-init="formData.apps[<?php echo $v->app_id;?>]=<?php echo $checked_value?>" ng-checked="<?php echo $checked_value?>" name="app" ng-true-value="<?php echo $v->app_id; ?>" ng-false-value="0"/> 
						<?php echo $v->app_name; ?>
					</label>
				</div>				
				<?php } ?>
				<input type="hidden" name="type" ng-value="formData.type='app'"/>	
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