<div class="col-xs-6" ng-controller="updateUserPasswordController">
	<h4><?php echo Lang::get('user.password_edit') ?></h4>
	<div class="row" >
		<div class="eb-content">
			<form action="/users/<?php echo $user->user_id; ?>" method="put" role="form" ng-submit="processFrom('/users/<?php echo $user->user_id; ?>')" onsubmit="return false;" id="updatePasswordForm">
				<?php if (Auth::user()->user_id == $user->user_id) { ?>
				<div class="form-group">
					<label for="old_password"></label>
					<input type="password" class="form-control" id="old_password" placeholder="<?php echo Lang::get('user.old_password') ?>" required ng-model="formData.old_password" name="old_password" /> 
				</div>				
				<?php } ?>
				<div class="form-group">
					<label for="password"></label>
					<input type="password" class="form-control" id="password" placeholder="<?php echo Lang::get('user.new_password') ?>" required ng-model="formData.password" name="password" /> 
				</div>				
				<div class="form-group">
					<label for="password_confirmation"></label>
					<input type="password" class="form-control" id="password_confirmation" placeholder="<?php echo Lang::get('user.password_confirmation') ?>" required ng-model="formData.password_confirmation" name="password_confirmation" /> 
				</div>				
				<input type="hidden" name="type" ng-value="formData.type='pwd'"/>	
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

<div class="col-xs-6" ng-controller="updateUserProfileController">
	<h4>部门信息</h4>
	<div class="row" >
		<div class="eb-content">
				<div class="form-group">
					<label for="username"></label>
					<input type="text" class="form-control" id="username" placeholder="<?php echo Lang::get('user.username') ?>" required ng-model="formData.username" name="username" ng-init="formData.username='<?php echo $user->username ?>'" disabled="true" /> 
				</div>				
				<div class="form-group">
					<label for="nickname"></label>
					<input type="text" class="form-control" id="nickname" placeholder="<?php echo Lang::get('user.nickname') ?>" ng-model="formData.nickname" name="nickname" ng-init="formData.nickname='<?php echo $user->nickname ?>'" disabled="true" /> 
				</div>	
				<div class="form-group">
					<label for="email"></label>
					<input disabled="true"  type="email" class="form-control" id="email" placeholder="Email" required ng-model="formData.email" name="email" ng-init="formData.email='<?php echo $user->email ?>'"/> 
				</div>				

				<div class="form-group">
					<label form="choose_department"></label>	
					<select disabled="true" class="form-control" name="department_id" id="choose_department" ng-model="formData.department_id" ng-init="formData.department_id=<?php echo $user->department_id; ?>" required>
						<option value="0"><?php echo Lang::get('user.choose_department') ?></option>
						<?php foreach (Department::organization()->get() as $k => $v) { ?>
						<option value="<?php echo $v->department_id?>"><?php echo $v->department_name;?></option>
						<?php } ?>		
					</select>
				</div>			
		</div><!-- /.col -->
	</div>
</div>

<?php if (Auth::user()->is_admin && !$user->is_admin) { ?>
<div class="col-xs-6" ng-controller="updateUserPermissionController">
	<h4><?php echo lang::get('user.permission_edit') ?></h4>
	<div class="row" >
		<div class="eb-content">
			<form action="/users/<?php echo $user->user_id ?>" method="put" role="form" ng-submit="processFrom('/users/<?php echo $user->user_id ?>')" onsubmit="return false;" id="updatepermissionform">
				<?php foreach($apps as $vv) {?>
				<div class="panel panel-info">
					<div class="panel-heading">
					<label>
					<input type="checkbox" name="is_checked_<?php echo $vv['department_id']?>" ng-model="is_checked_<?php echo $vv['department_id']?>" ng-change="is_checked(<?php echo $vv['department_id'] ?>, '<?php echo $vv['app_ids']?>')" ng-true-value="1" ng-false-value="0" ng-checked="0" ng-init="is_checked_<?php echo $vv['department_id']?>=0" /> <?php echo $vv['department_name']?> 
					</label>
					</div>
					<ul class="panel-body">
					<?php foreach($vv['apps'] as $k => $v) { ?>
						<?php $v = (object)$v;$checked_value = 0 ?>
						<?php if (in_array($v->app_id, $user->permissions())) { ?>
						<?php $checked_value = $v->app_id ?>
					<?php } ?>
						<li class="checkbox">
							<label>
							<input type="checkbox" value="<?php echo $v->app_id;?>" ng-model="formData.permissions[<?php echo $v->app_id; ?>]" ng-init="formData.permissions[<?php echo $v->app_id;?>]='<?php echo $checked_value?>'" name="permission" ng-true-value="<?php echo $v->app_id; ?>" ng-false-value="0"/> 
						<?php echo $v->app_name; ?>
							</label>
						</li>				
					<?php } ?>
					</ul>
				</div>
				<?php } ?>
				<input type="hidden" name="type" ng-value="formData.type='permission'"/>	
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


<div class="col-xs-6" ng-controller="updateUserGamesController">
	<h4><?php echo Lang::get('user.games_edit') ?></h4>
	<div class="row" >
		<div class="eb-content">
			<form action="/users/<?php echo $user->user_id ?>" method="put" role="form" ng-submit="processFrom('/users/<?php echo $user->user_id ?>')" onsubmit="return false;" id="updateUserGames">
				<?php foreach(GameCode::all() as $vv) { ?>
				<div class="panel panel-info">
					<div class="panel-heading">
						<?php echo $vv->game_name;?>
					</div>
					<ul class="panel-body">
					<?php foreach(Game::userGames()->where('game_code', $vv->game_code)->get() as $k => $v) { ?>
						<?php $checked_value = 0 ?>
					<?php if (in_array($v->game_id, $user->games())) { ?>
						<?php $checked_value = $v->game_id?>
					<?php } ?>
						<li class="checkbox">
						<label>
						<input type="checkbox" value="<?php echo $v->game_id;?>" ng-model="formData.games[<?php echo $v->game_id; ?>]" ng-init="formData.games[<?php echo $v->game_id;?>]='<?php echo $checked_value?>'" name="games" ng-true-value="<?php echo $v->game_id; ?>" ng-false-value="0"/> 
							<?php echo $v->game_name; ?> 
						</label>
						</li>				
					<?php } ?>
						</ul>
				</div>
				<?php } ?>
				<input type="hidden" name="type" ng-value="formData.type='games'"/>	
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
<script>
	function CopyPermitionController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$scope.formData.to_user_id = <?php echo $user->user_id; ?>;
			$http({
				'method' : 'post',
				'url'	 : '/change/users/copypermition',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
				setTimeout('window.location.reload()', 300);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-6" ng-controller="CopyPermitionController">
	<h4><?php echo Lang::get('user.copy_permition') ?></h4>
	<div class="row" >
		<div class="eb-content">
			<form action="" method="put" role="form" ng-submit="process()" onsubmit="return false;" id="updateUserGames">
				<div class="eb-content">
					<select class="form-control" name="from_user_id" id="from_user_id"
						ng-model="formData.from_user_id" size="10" ng-init="formData.from_user_id = 0">
							<option value="0"><?php echo Lang::get('user.select_from_user'); ?></option>
						<?php foreach ($users as $single_user) { ?>
							<option value="<?php echo $single_user->user_id; ?>"><?php echo $single_user->username; ?></option>
						<?php } ?>
					</select>
					<div class="input-group">
						<input type="button" class="btn btn-danger" value="<?php echo Lang::get('basic.copy_permissions') ?>" 
						ng-click="process()"/>
					</div>
				</div>
			</form>	 
		</div><!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>

<?php } ?>