<<script type="text/javascript">
function createProjectController($scope, $http, alertService) {
	$scope.alerts = [];
	$scope.formData = {};
	$scope.processFrom = function() {
		alertService.alerts = $scope.alerts;
		$http({
			'method' : 'post',
			'url'	 : '/project',
			'data'   : $.param($scope.formData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			alertService.add('success', data.msg);
			$scope.formData.project_name = '';
			$scope.formData.project_owner = '';
			$scope.formData.svn_name = '';
			$scope.formData.release_shell = '';
			$scope.formData.current_version = '';
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
	};
}
</script>

<div class="col-xs-12" ng-controller="createProjectController">
	<div class="row" >
		<div class="eb-content">
			<form action="/project" method="post" role="form" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<input type="text" class="form-control" id="project_name" placeholder="<?php echo Lang::get('project.project_name') ?>"  ng-model="formData.project_name" name="project_name" autofocus="autofocus"/> 
				</div>				

				<div class="form-group">
					<input type="project_owner" class="form-control" placeholder="<?php echo Lang::get('project.project_owner') ?>" required ng-model="formData.project_owner" name="project_owner" /> 
				</div>				
				<div class="form-group">
					<input type="svn_name" class="form-control" placeholder="<?php echo Lang::get('project.svn_name') ?>" required ng-model="formData.svn_name" name="svn_name" /> 
				</div>				
				<div class="form-group">
					<input type="release_shell" class="form-control" placeholder="<?php echo Lang::get('project.release_shell') ?>"  ng-model="formData.release_shell" name="release_shell" /> 
				</div>				
				<div class="form-group">
					<input type="exclude_files" class="form-control" placeholder="<?php echo Lang::get('project.exclude_files') ?>"  ng-model="formData.exclude_files" name="exclude_files" /> 
				</div>				
				<div class="form-group">
					<input type="current_version" class="form-control" placeholder="<?php echo Lang::get('project.current_version') ?>"  ng-model="formData.current_version" name="current_version" /> 
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