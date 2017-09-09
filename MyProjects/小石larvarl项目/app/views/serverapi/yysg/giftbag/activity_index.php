<script>
    function activityController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.start_time = null;
        $scope.end_time = null;
        $scope.formData = {};
        $scope.show = 0;
        $scope.process = function (url) {
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
            $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
                var result2 = data.result2;
                var len2 = result2.length;
                for (var i = 0; i < len2; i++) {
                    if (result2[i].status == 'ok') {
                        alertService.add('success', result2[i].msg);
                    } else if (result2[i]['status'] == 'error') {
                        alertService.add('danger', result2[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

        $scope.processCheck = function (url) {
            $scope.show = 0;
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
            $http({
                'method': 'post',
                'url': url,
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                $scope.show = 1;
                $scope.items = data;
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="activityController">
    <div class="row">
        <div class="eb-content">
             <div class="form-group">
                <select class="form-control" name="server_internal_ids"
                    id="select_game_server" ng-model="formData.server_internal_ids"
                    ng-init="formData.server_internal_ids=0" multiple="multiple" ng-multiple="true" size=6 required>
                    <?php foreach ($servers as $k => $v) { ?>
                        <option value="<?php echo $v->server_internal_id?>"><?php echo $v->server_name.':'.$v->server_track_name;?></option>
                    <?php } ?>      
                </select>
                </div>
            <?php if('yysg' == $game_code){ ?>
                <div class="form-group">
                    <select class="form-control" name="is_lang" ng-model="formData.is_lang" ng-init="formData.is_lang=1">
                        <option value="0">开启所有地区活动</option>
                        <option value="1">只开启当前地区活动</option>
                    </select>
                </div>
            <?php }?>
            <div class="form-group">
                <select class="form-control" name="activity_id"
                        id="select_activity" ng-model="formData.activity_id"
                        ng-init="formData.activity_id=0" multiple="multiple"
                        ng-multiple="true" size=10 required>
                    <optgroup
                        label="<?php echo Lang::get('serverapi.select_activity') ?>">
                        <?php foreach ($activity as $k => $v) { ?>
                            <option value="<?php echo $v->id ?>"><?php echo $v->id; ?> : <?php echo $v->name; ?></option>
                        <?php } ?>
                    </optgroup>
                </select>
            </div>
            <div class="clearfix">
                <br/>
            </div>
            <div class="form-group">
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="start_time" init-value="<?php if ('mnsg' == $game_code) { ?> 05:00:00<?php }else{ ?>00:10:00<?php }?>"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="end_time" init-value="<?php if ('mnsg' == $game_code) { ?> 04:59:59<?php }else{ ?>23:50:00<?php }?>"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <br/>
                <br/>
            </div>
            <div class="form-group" style="height: 40px;">
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.promotion_set') ?>"
                           ng-click="process('/game-server-api/activity/open')"/>
                </div>
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-danger"
                           value="<?php echo Lang::get('serverapi.promotion_close') ?>"
                           ng-click="process('/game-server-api/activity/close')"/>
                </div>
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-primary"
                           value="查看未结束活动"
                           ng-click="processCheck('/game-server-api/activity/check')"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                   close="alert.close()">{{alert.msg}}
            </alert>
        </div>
    </div>
    <div class="col-xs-12">
        <table class="table table-striped" ng-if = "show == 1">
            <thead>
                <tr class="info">
                    <td><b>操作人</b></td>
                    <td><b>活动名称</b></td>
                    <td><b>开始时间</b></td>
                    <td><b>结束时间</b></td>
                    <td><b>服务器</b></td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="t in items">
                    <td>{{t.operator}}</td>
                    <td>{{t.activity}}</td>
                    <td>{{t.start_time}}</td>
                    <td>{{t.end_time}}</td>
                    <td>{{t.servers}}</td>
                </tr>
            </tbody>
        </table>
        
    </div>
</div>