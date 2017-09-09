<script>
	function OfficeMailsController($scope, $filter, $modal, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.show = 0;
		$scope.pagecontrol = 0;
		$scope.pagination = {};

		$scope.pagination.totalItems = 0;
		$scope.pagination.currentPage = 1;
		$scope.pagination.perPage= 50;

		$scope.$watch('pagination.currentPage', function(newPage, oldPage) {
			if ($scope.pagecontrol > 0 && newPage != oldPage) {
				$scope.processFrom('search', newPage);
			}
		});

		$scope.processFrom = function(type, newPage) {
			$scope.mails = [];
			$scope.formData.type = type;
			$scope.formData.page = newPage;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/office/mails',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.pagecontrol = 1;
				$scope.show = 1;
				$scope.pagination.currentPage = data.current_page;
				$scope.pagination.totalItems = data.count;
				$scope.mails = data.mails;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};

		$scope.download = function() {
			$scope.formData.type = 'download';
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/office/mails',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				window.location.replace("/office/mails?filename=" + data.filename);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};


		$scope.update = function(mail){
			var modalInstance = $modal.open({
				templateUrl: 'update.html',
				controller: modalUpdateMailController,
				resolve: {
					mail : function () {
						return mail;
					}
				},
				backdrop : false,
				keyboard : false
			});
			modalInstance.result.then(function() {
				$scope.processFrom('search', $scope.pagination.currentPage);	
			});
		}

		$scope.check = function(mail){
			var modalInstance = $modal.open({
				templateUrl: 'check.html',
				controller: checkController,
				resolve: {
					mail : function () {
						return mail;
					}
				},
				backdrop : false,
				keyboard : false
			});
			modalInstance.result.then(function() {
			});		
		}
	}

	function modalUpdateMailController($scope, $modalInstance, mail, $http, alertService) {
		$scope.mail_init = mail;
		$scope.UpdateData = {};
		$scope.UpdateData.id = $scope.mail_init.id;
		$scope.UpdateData.type = 'update';
		$scope.cancel = function() {
			$modalInstance.dismiss('cancel');
		}
		$scope.UpdateForm= function() {
			$http({
				'method' : 'post',
				'url' : '/office/mails',
				'data' : $.param($scope.UpdateData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alert(data.msg);
				$modalInstance.close();
			}).error(function(data) {
				alert('error: ' + data.error);
			});
		}
	}

	function checkController($scope, $modalInstance, mail, $http, alertService) {
		$scope.mail = mail;
		$scope.cancel = function() {
			$modalInstance.dismiss('cancel');
		}
	}
</script>
<div class="col-xs-12" ng-controller="OfficeMailsController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom('search', 1)" onsubmit="return false;">
				<div class="form-group">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group col-md-6" style="margin-top:10px;padding: 0 0 0 0">
					<select class="form-control" name="mail_type"
						id="mail_type" ng-model="formData.mail_type" ng-init = "formData.mail_type = 0" />
						<option value="0"><?php echo Lang::get('office.all'); ?><?php echo Lang::get('office.mail'); ?></option>
						<option value="1"><?php echo Lang::get('office.leave'); ?><?php echo Lang::get('office.mail'); ?></option>
						<option value="2"><?php echo Lang::get('office.overtime'); ?><?php echo Lang::get('office.mail'); ?></option>
						<option value="3"><?php echo Lang::get('office.adjust'); ?><?php echo Lang::get('office.mail'); ?></option>
				</div>
				<div class="form-group col-md-6" style="margin-top:10px;padding: 0 0 0 0">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('office.enter_operator_name')?>"
						ng-model="formData.operator" name="operator"?>
				</div>
				<div class="form-group col-md-6" style="margin-top:10px;padding: 0 0 0 0">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('office.enter_sender_name')?>"
						ng-model="formData.sender" name="sender"?>
				</div>
				<div class="clearfix"></div>
				<input type="submit" class="btn btn-primary"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />

				<input type="button" class="btn btn-primary" ng-click="download()"
					value="<?php echo Lang::get('basic.download_csv') ?>" />
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><nobr><b><?php echo Lang::get('office.mail_type'); ?></b></nobr></td>	
					<td><nobr><b><?php echo Lang::get('office.mail_time'); ?></b></nobr></td>
					<td><nobr><b><?php echo Lang::get('office.l_type'); ?></b></nobr></td>
					<td><nobr><b><?php echo Lang::get('office.sender'); ?></b></nobr></td>
					<td><nobr><b><?php echo Lang::get('office.operator'); ?></b></nobr></td>
					<td><nobr><b><?php echo Lang::get('office.l_time'); ?></b></nobr></td>
					<td><nobr><b><?php echo Lang::get('office.l_days'); ?></b></nobr></td>
					<td><nobr><b><?php echo Lang::get('office.l_reason'); ?></b></nobr></td>
					<td><nobr><b><?php echo Lang::get('office.l_result'); ?></b></nobr></td>
					<td></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="m in mails">
					<td>{{m.mail_type}}</td>
					<td>{{m.mail_time}}</td>
					<td>{{m.l_type}}</td>
					<td>{{m.sender}}</td>
					<td>{{m.operator}}</td>
					<td>{{m.l_time}}</td>
					<td>{{m.l_days}}</td>
					<td>{{m.l_reason}}</td>
					<td style="color:red"><b>{{m.l_result}}</b></td>
					<td><button class="btn btn-primary" ng-click="update(m)"><?php echo Lang::get('office.modify'); ?></button></td>
					<td><button class="btn btn-primary" ng-click="check(m)"><?php echo Lang::get('office.check'); ?></button></td>
				</tr>
			</tbody>
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>
</div>

<script type="text/ng-template" id="update.html">
        <div class="modal-header">
        </div>
		<form action="" method="post" role="form" ng-submit="UpdateForm()" onsubmit="return false;">
			<div class="modal-body">
				<div class="form-group">
					<label><?php echo Lang::get('office.sender')?>:</label>
					<input type="text" class="form-control" ng-model="UpdateData.sender" ng-init="UpdateData.sender = mail_init.sender"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('office.operator')?>:</label>
					<input type="text" class="form-control" ng-model="UpdateData.operator" ng-init="UpdateData.operator= mail_init.operator"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('office.l_type')?>:</label>
					<input type="text" class="form-control" ng-model="UpdateData.l_type" ng-init="UpdateData.l_type= mail_init.l_type"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('office.l_time')?>:</label>
					<input type="text" class="form-control" ng-model="UpdateData.l_time" ng-init="UpdateData.l_time= mail_init.l_time"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('office.l_days')?>:</label>
					<input type="text" class="form-control" ng-model="UpdateData.l_days" ng-init="UpdateData.l_days= mail_init.l_days"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('office.l_reason')?>:</label>
					<input type="text" class="form-control" ng-model="UpdateData.l_reason" ng-init="UpdateData.l_reason= mail_init.l_reason"?>
				</div>
				<div class="form-group">
					<label><?php echo Lang::get('office.l_result')?>:</label>
					<input type="text" class="form-control" ng-model="UpdateData.l_result" ng-init="UpdateData.l_result= mail_init.l_result"?>
				</div>
			</div>
	        <div class="modal-footer" style="text-align:center;">
				<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
	            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
	        </div>
		</form>
</script>

<script type="text/ng-template" id="check.html">
        <div class="modal-header">
        </div>
		<div class="modal-body">
			<div class="form-group" id="mail_body" ng-bind-html = "mail.body|trustHtml">
			</div>
		</div>
        <div class="modal-footer" style="text-align:center;">
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
</script>