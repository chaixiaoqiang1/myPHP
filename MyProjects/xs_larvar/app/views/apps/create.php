<div class="col-xs-12" ng-controller="createAppController">
	<div class="row" >
		<div class="eb-content">
			<form action="/apps/store" method="post" role="form" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<input type="text" class="form-control" id="app_name" placeholder="<?php echo Lang::get('system.enter_app_name') ?>" required ng-model="formData.app_name" name="app_name" autofocus="autofocus" ng-autofocus="true" /> 
				</div>				

				<div class="form-group">
					<input type="text" class="form-control" id="app_key" placeholder="<?php echo Lang::get('system.enter_app_key') ?>" required ng-model="formData.app_key" name="app_key" /> 
				</div>				
				<div class="form-group">
					<select name="department_id" class="form-control" ng-model="formData.department_id" ng-init="formData.department_id=1">
						<?php foreach(Department::all() as $v) {?>
						<option value="<?php echo $v->department_id?>"><?php echo $v->department_name?></option>
						<?php }?>
					</select>	
				</div>
				<div class="form-group">
					<select class="form-control" name="game_code" ng-model="formData.game_code" ng-init="formData.game_code=0">
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
					<textarea class="form-control" id="app_description" placeholder="<?php echo Lang::get('system.enter_app_description') ?>" required ng-model="formData.description" name="description">请输入相关描述</textarea>
				</div>
				<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="col-xs-6"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>