<div class="col-xs-12" ng-controller="createGroupController">
	<div class="row" >
		<div class="eb-content">
			<form action="/groups" method="post" role="form" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<label for="group_name"></label>
					<input type="text" class="form-control" id="group_name" placeholder="<?php echo Lang::get('system.enter_group_name') ?>" required ng-model="formData.group_name" name="group_name" autofocus="autofocus"/> 
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