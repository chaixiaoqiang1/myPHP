<script>
function modalReleaseProjectController($scope, $modalInstance, id, name, $http, alertService) {
	$scope.id = id;
	$scope.name = name;
	$scope.releaseInfo = {};
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.releaseProjectForm= function(url) {
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.releaseInfo),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			$modalInstance.close();
		}).error(function(data) {
			alert('error: ' + data.error + '\n');
		});
	}
}
function modalRollBackController($scope, $modalInstance, id, name, $http, alertService) {
	$scope.id = id;
	$scope.name = name;
	$scope.releaseInfo = {};
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.releaseProjectForm= function(url) {
		$http({
			'method' : 'post',
			'url' : url,
			'data' : $.param($scope.releaseInfo),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			$modalInstance.close();
		}).error(function(data) {
			alert('error: ' + data.error + '\n');
		});
	}
}
	function releaseProjectController($scope, $http, alertService, $modal, $filter) {
		$scope.formData = {};
		$scope.alerts = [];
		$scope.check = function(id){
			alertService.alerts = $scope.alerts;
			$scope.formData.id = id;
			$http({
	            'method': 'post',
	            'url': '/project/check',
	            'data': $.param($scope.formData),
	            'headers': {
	                'Content-Type': 'application/x-www-form-urlencoded'
	            }
	        }).success(function(data) {
		        console.log(data.content);
	        	alertService.add('success', data.content);
	        }).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		}
		$scope.release = function(id, name) {
			var modalInstance = $modal.open({
				templateUrl: 'release_project.html',
				controller: modalReleaseProjectController,
				resolve: {
					id : function () {
						return id;
					},
					name : function () {
						return name;
					}
				},
				backdrop : false,
				keyboard : false
			});
			modalInstance.result.then(function() {
				alert('<?php echo Lang::get('project.release_success')?>');	
				location.reload(true);	
			});
		}
		$scope.rollback = function(id, name) {
			var modalInstance = $modal.open({
				templateUrl: 'roll_back.html',
				controller: modalRollBackController,
				resolve: {
					id : function () {
						return id;
					},
					name : function () {
						return name;
					}
			
				},
				backdrop : false,
				keyboard : false
			});
			modalInstance.result.then(function() {
				alert('<?php echo Lang::get('project.release_success')?>');	
				location.reload(true);
				
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="releaseProjectController">
	<table class="table table-striped">
		<thead>
			<tr class="info">
				<td><b><?php echo Lang::get('project.project_id') ?></b></td>
				<td><b><?php echo Lang::get('project.project_name') ?></b></td>
				<td><b><?php echo Lang::get('project.project_owner') ?></b></td>
				<td><b><?php echo Lang::get('project.svn_name') ?></b></td>
				<td><b><?php echo Lang::get('project.exclude_files') ?></b></td>
				<td><b><?php echo Lang::get('project.current_version') ?></b></td>
				<td><b><?php echo Lang::get('project.last_release_user') ?></b></td>
				<td><b><?php echo Lang::get('project.last_release_record') ?></b></td>
				<td><b><?php echo Lang::get('project.last_release_time') ?></b></td>
				<td><b><?php echo Lang::get('project.release_control') ?></b></td>
				<td><b><?php echo Lang::get('project.check_version') ?></b></td>
				<td><b><?php echo Lang::get('project.roll_back_control') ?></b></td>
			</tr>
		</thead>
		<tbody> 
		<?php foreach ($projects as $k => $v) { ?>
		<tr>
				<td><a href="project/<?php echo $v->project_id?>/edit"><?php echo $v->project_id?></a></td>
				<td><a href="project/<?php echo $v->project_id?>"><?php echo $v->project_name?></a></td>
				<td><?php echo $v->project_owner ?></td>
				<td><?php echo $v->svn_name ?></td>
				<td><abbr title="<?php echo $v->exclude_files ?>"><?php
    echo mb_strlen($v->exclude_files, 'utf-8') > 30 ? mb_substr(
            $v->exclude_files, 0, 29, 'utf-8') . '...' : $v->exclude_files;
    ?></abbr></td>
				<td><?php echo $v->current_version ?></td>
				<td><?php echo $v->last_release_user ?></td>
				<td><?php echo $v->last_release_record ?></td>
				<td><?php echo $v->last_release_time ?></td>
				<td><button class="btn btn-danger"
						ng-click="release(<?php echo $v->project_id?>, '<?php echo $v->project_name?>')"><?php echo Lang::get("project.release");?></button></td>
				<td><button class="btn btn-primary"
						ng-click="check(<?php echo $v->project_id?>)"><?php echo Lang::get("project.check");?></button></td>
				<td><button class="btn btn-warning"
						ng-click="rollback(<?php echo $v->project_id?>, '<?php echo $v->project_name?>')"><?php echo Lang::get("project.roll_back");?></button></td>

			</tr>
	<?php } ?>	
		</tbody>
	</table>
	<div class="row margin-top-10">
		<div class="col-xs-12">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>

<script type="text/ng-template" id="release_project.html">
        <div class="modal-header">
        </div>
		<form action="/project/release" method="post" role="form" ng-submit="releaseProjectForm('/project/release')" onsubmit="return false;">
        <div class="modal-body">
			<div class="form-group">
				<label><?php echo Lang::get('project.project_id')?>:</label>	
				<input type="text" readonly class="form-control" ng-model="releaseInfo.project_id" ng-init="releaseInfo.project_id=id"?>
			</div>
            <div class="form-group">
				<label><?php echo Lang::get('project.project_name')?>:</label>	
				<input type="text" readonly class="form-control" ng-model="releaseInfo.project_name" ng-init="releaseInfo.project_name=name"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('project.release_user')?>:</label>
				<input type="text" autofocus="autofocus" required class="form-control" ng-model="releaseInfo.release_user" />
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('project.release_record')?>:</label>
				<textarea type="text" rows='3' class="form-control" ng-model="releaseInfo.release_record" >
                </textarea>
			</div>
        </div>
        <div class="modal-footer" style="text-align:center;">
			<button class="btn btn-primary"><?php echo Lang::get('project.release_project')?></button>
            <a class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('project.cancel')?></a>
        </div>
		</form>
</script>

<script type="text/ng-template" id="roll_back.html">
        <div class="modal-header">
        </div>
		<form action="/project/release" method="post" role="form" ng-submit="releaseProjectForm('/project/release')" onsubmit="return false;">
        <div class="modal-body">
			<div class="form-group">
				<label><?php echo Lang::get('project.project_id')?>:</label>	
				<input type="text" readonly class="form-control" ng-model="releaseInfo.project_id" ng-init="releaseInfo.project_id=id"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('project.project_name')?>:</label>	
				<input type="text" readonly class="form-control" ng-model="releaseInfo.project_name" ng-init="releaseInfo.project_name=name"?>
			</div>
              <div class="form-group">
				<label><?php echo Lang::get('project.roll_back_version')?>:</label>	
				<input type="text" autofocus="autofocus" required class="form-control" ng-model="releaseInfo.roll_back_version" name= "roll_back_version"?>
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('project.release_user')?>:</label>
				<input type="text" required class="form-control" ng-model="releaseInfo.release_user" />
			</div>
			<div class="form-group">
				<label><?php echo Lang::get('project.release_record')?>:</label>
				<textarea type="text" rows='3' class="form-control" ng-model="releaseInfo.release_record" >
                </textarea>
			</div>
        </div>
        <div class="modal-footer" style="text-align:center;">
			<button class="btn btn-primary"><?php echo Lang::get('project.roll_back_project')?></button>
            <a class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('project.cancel')?></a>
        </div>
		</form>
</script>