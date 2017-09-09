<script>
    function timingActivityController($scope, $http, alertService, $modal, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.deleteDate = {};
        $scope.process = function (url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
            $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                $scope.items = data;
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };
        $scope.processDelete = function (id) {
            if (!confirm('确定要删除当前活动?')) {
                return;
            }
            alertService.alerts = $scope.alerts;
            $scope.deleteDate.id = id;
            $http({
                'method' : 'post',
                'url'    : '/game-server-api/activity/timing',
                'data'   : $.param($scope.deleteDate),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                if (data == 'ok') {
                   alertService.add('success', 'ok');
                } else if (data == 'error') {
                    alertService.add('danger', data.error);
                }
            }).error(function(data) {
                alertService.add('danger', data.error);
            });
        };
        $scope.processUpdate= function (list) {
            var modalInstance = $modal.open({
                templateUrl: 'update_timing.html',
                controller: updateTimingController,
                resolve: {
                    list : function () {
                        return list;
                    }
                },
                backdrop : false,
                keyboard : false
            });
            modalInstance.result.then(function() {
                $scope.process_update();   
            });
        };
    }
    function updateTimingController($scope, $modalInstance, list, $http, alertService, $filter) {
        $scope.list = list;
        $scope.listData = {};
        $scope.cancel = function() {
            $modalInstance.dismiss('cancel');
        }
        $scope.process_update = function (url) {
            alertService.alerts = $scope.alerts;
            $scope.listData.start_time = $filter('date')($scope.listData.start_time1, 'yyyy-MM-dd HH:mm:ss');
            $scope.listData.end_time = $filter('date')($scope.listData.end_time1, 'yyyy-MM-dd HH:mm:ss');
            $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.listData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                if (data == 'ok') {
                   $modalInstance.close();
                } else if (data == 'error') {
                    alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
                }
            }).error(function (data) {
                alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="timingActivityController">
    <div class="row">
        <div class="eb-content">
            <div class="form-group">
                <select class="form-control" name="activity_type" ng-model="formData.activity_type"
                        ng-init="formData.activity_type=0">
                            <option value="0"><?php echo Lang::get('serverapi.all_activities'); ?></option>
                            <option value="1"><?php echo Lang::get('serverapi.turnplate_activity'); ?></option>
                            <option value="2"><?php echo Lang::get('serverapi.holiday_activity'); ?></option>
                            <option value="3"><?php echo Lang::get('serverapi.holiday_award_set'); ?></option>
                </select>
            </div>

            <div class="form-group">
                <div class="col-md-5" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="col-md-5" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('basic.btn_show') ?>"
                           ng-click="process('/game-server-api/activity/timing')"/>
                </div>
            </div>
            <p><font color=red><?php echo Lang::get('serverapi.timing_activity_tip'); ?></font></p>
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
                    <td><b><?php echo Lang::get('serverapi.gm_type'); ?></b></td>
                    <td><b><?php echo Lang::get('serverapi.promotion_name'); ?></b></td>
                    <td><b><?php echo Lang::get('serverapi.start_time'); ?></b></td>
                    <td><b><?php echo Lang::get('serverapi.end_time'); ?></b></td>
                    <td><b><?php echo Lang::get('serverapi.yuanbao_server'); ?></b></td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="t in items">
                    <td>{{t.type_name}}</td>
                    <td>{{t.activity_name}}</td>
                    <td>{{t.start_time}}</td>
                    <td>{{t.end_time}}</td>
                    <td>
                        <select class="form-control" ng-model="seleted" ng-options="a for a in t.server_name">
                            <option value=""><?php echo Lang::get('serverapi.yuanbao_server'); ?></option>
                        </select>
                    </td>
                    <td><input type='button' class="btn btn-warning"
                           value="<?php echo Lang::get('basic.update').Lang::get('serverapi.operate_time') ?>"
                           ng-click="processUpdate(t)"/></td>
                    <td><input type='button' class="btn btn-danger"
                           value="<?php echo Lang::get('basic.delete') ?>"
                           ng-click="processDelete(t.id)"/></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="text/ng-template" id="update_timing.html">
        <div class="modal-header">
        </div>
        <form action="/game-server-api/activity/timing" method="post" role="form" ng-submit="process_update('/game-server-api/activity/timing')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="listData.start_time1" init-value={{list.start_time}}></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0" ng-if="list.type != 3">
                    <div class="input-group">
                        <quick-datepicker ng-model="listData.end_time1" init-value={{list.end_time}}></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <br/>
            </div>
        </div>
        <input type="hidden" ng-model="listData.id" ng-init="listData.id = list.id" name="id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('basic.update')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>