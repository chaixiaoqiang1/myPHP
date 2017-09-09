<script type="text/javascript">
function updateProjectController($scope, $http, alertService) {
	$scope.alerts = [];
	$scope.formData = {};
	$scope.processFrom = function(url) {
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'put',
			'url'	 : url,
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alertService.add('success', data.msg);
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
}
</script>
<div class="col-xs-12" ng-controller="updateProjectController">
	<div class="row">
		<div class="eb-content">
			<form action="/project/<?php echo $project->project_id?>"
				method="put" role="form"
				ng-submit="processFrom('/project/<?php echo $project->project_id;?>')"
				onsubmit="return false;">
				<div class="form-group">
					<label for="project_name"><?php echo Lang::get('project.project_name') ?></label>
					<input type="text" class="form-control" id="project_name"
						ng-init="formData.project_name='<?php echo Project::where('project_id','=',$project->project_id)->pluck('project_name') ?>'"
						 ng-model="formData.project_name" name="project_name"
						autofocus="autofocus" ng-autofocus="true" />
				</div>
				<div class="form-group">
					<label for="project_owner"><?php echo Lang::get('project.project_owner') ?></label>
					<input type="text" class="form-control" id="project_owner"
						ng-init="formData.project_owner='<?php echo Project::where('project_id','=',$project->project_id)->pluck('project_owner') ?>'"
						required ng-model="formData.project_owner" name="project_owner" />
				</div>
				<div class="form-group">
					<label for="svn_name"><?php echo Lang::get('project.svn_name') ?></label>
					<input type="text" class="form-control" id="svn_name"
						ng-init="formData.svn_name='<?php echo Project::where('project_id','=',$project->project_id)->pluck('svn_name') ?>'"
						required ng-model="formData.svn_name" name="svn_name" />
				</div>
				<div class="form-group">
					<label for="release_shell"><?php echo Lang::get('project.release_shell') ?></label>
					<input type="text" class="form-control" id="release_shell"
						ng-init="formData.release_shell='<?php echo Project::where('project_id','=',$project->project_id)->pluck('release_shell') ?>'"
						ng-model="formData.release_shell" name="release_shell" />
				</div>
				<div class="form-group">
					<label for="exclude_files"><?php echo Lang::get('project.exclude_files') ?></label>
					<input type="text" class="form-control" id="exclude_files"
						ng-init="formData.exclude_files='<?php echo Project::where('project_id','=',$project->project_id)->pluck('exclude_files') ?>'"
						 ng-model="formData.exclude_files" name="exclude_files" />
				</div>
				<div class="form-group">
					<label for="current_version"><?php echo Lang::get('project.current_version') ?></label><input
						type="text" class="form-control" id="current_version"
						ng-init="formData.current_version='<?php echo Project::where('project_id','=',$project->project_id)->pluck('current_version') ?>'"
						 ng-model="formData.current_version"
						name="current_version" />

				</div>
				<div class="form-group">
					<label for="last_release_user"><?php echo Lang::get('project.last_release_user') ?></label>
					<input type="text" class="form-control" id="last_release_user"
						ng-init="formData.last_release_user='<?php echo Project::where('project_id','=',$project->project_id)->pluck('last_release_user') ?>'"
						 ng-model="formData.last_release_user"
						name="last_release_user" />
				</div>
				<div class="form-group">
					<label for="last_release_record"><?php echo Lang::get('project.last_release_record') ?></label>
					<textarea type="text" class="form-control" id="last_release_record"
						 ng-model="formData.last_release_record"
						name="last_release_record" rows="4" ng-init="formData.last_release_record='<?php echo $project->last_release_record;?>'"></textarea>

				</div>
				<div class="form-group">
					<label for="last_release_time"><?php echo Lang::get('project.last_release_time') ?></label>
					<input type="text" class="form-control" id="last_release_time"
						ng-init="formData.last_release_time='<?php echo $project->last_release_time ?>'"
						readonly required ng-model="formData.last_release_time"
						name="last_release_time" />
				</div>
				<input type="submit" class="btn btn-default"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>

	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>