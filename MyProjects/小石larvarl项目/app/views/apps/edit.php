<div class="col-xs-12" ng-controller="updateAppController">
	<div class="row" >
		<div class="eb-content">
			<form action="/apps/<?php echo $app->app_id?>" method="put" role="form" ng-submit="processFrom('/apps/<?php echo $app->app_id;?>')" onsubmit="return false;">
				<div class="form-group">
					<label for="app_name"></label>
					<input type="text" class="form-control" id="app_name" ng-init="formData.app_name='<?php echo EastBlueApp::where('app_id','=',$app->app_id)->pluck('app_name') ?>'" required ng-model="formData.app_name" name="app_name" autofocus="autofocus" ng-autofocus="true" />
					 
				</div>				

				<div class="form-group">
					<label for="app_key"></label>
					<input type="text" class="form-control" id="app_key" ng-init="formData.app_key='<?php echo EastBlueApp::where('app_id','=',$app->app_id)->pluck('app_key') ?>'" required ng-model="formData.app_key" name="app_key" /> 
				</div>
				<div class="form-group">
				<select name="department_id" class="form-control" ng-model="formData.department_id" ng-init="formData.department_id=<?php echo $app->department_id?>">
						<?php foreach(Department::all() as $v) {?>
						<option value="<?php echo $v->department_id?>"><?php echo $v->department_name?></option>
						<?php }?>
					</select>	
				</div>
				<div class="form-group">
					<select class="form-control" name="game_code" ng-model="formData.game_code" ng-init="formData.game_code=<?php echo $app->game_code_id; ?>">
					<option value="0"><?php echo Lang::get('system.all_games') ?></option>	
					<option value="101"><?php echo Lang::get('system.old_games') ?></option>
					<option value="102"><?php echo Lang::get('system.our_games') ?></option>
					<option value="103"><?php echo Lang::get('system.web_game') ?></option>
					<option value="104"><?php echo Lang::get('system.mobile_game') ?></option>
					<?php foreach (GameCode::all() as $v) { ?>		
					<option value="<?php echo $v->code_id?>">
						<?php echo $v->game_name?>	
					</option>
					<?php } ?>
					</select>
				</div>
				<div class="form-group">
					<label for="description"></label>
					<textarea class="form-control" id="app_description" ng-init="formData.description='<?php echo EastBlueApp::where('app_id','=',$app->app_id)->pluck('description') ?>'" required ng-model="formData.description" name="description">请输入相关描述</textarea>
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