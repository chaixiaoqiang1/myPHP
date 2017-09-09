<script>
    function SetSoulCasketController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.MCData = {};

        $scope.processSX = function () {
            $scope.formData.type = 'SX';
            $scope.formData.time = $filter('date')($scope.time, 'yyyy-MM-dd HH:mm:ss');
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            $http({
                'method': 'post',
                'url': '/game-server-api/mnsg/setSoulCasket',
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                alertService.add('success', data.msg);
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

        $scope.processManaCrystal = function () {
            $scope.MCData.type = 'MC';
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            $http({
                'method': 'post',
                'url': '/game-server-api/mnsg/setSoulCasket',
                'data': $.param($scope.MCData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                alertService.add('success', data.msg);
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

        $scope.searchFrom = function(url) {
            if ($scope.formData.set_type == 0) {
                $scope.formData.is_auto = 'SX';
            }else{
                $scope.formData.is_auto = 'MC';
            }
            $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
            alertService.alerts = $scope.alerts;
            $http({
                'method' : 'post',
                'url'    : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                alertService.add('success', data.msg);
                $scope.items = data.items;
            }).error(function(data) {
                alertService.add('danger', data.error);
                $scope.items = data.items;
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="SetSoulCasketController">
    <div class="row">
        <div class="eb-content">
            <div class="form-group">
                <select class="form-control" name="set_type"
                    id="set_type" ng-model="formData.set_type" ng-init="formData.set_type=0" required>
                        <option value="0"><?php echo Lang::get('serverapi.SoulCasket');?></option>
                        <option value="1"><?php echo Lang::get('serverapi.ManaCrystalCasket');?></option>
                </select>
            </div>
            <div ng-show="0==formData.set_type">
                <b><?php echo Lang::get('slave.valid_time'); ?></b>
                <div class="form-group" style="height:35px;">
                    <div class="col-md-6" style="padding: 0">
                        <div class="input-group">
                            <quick-datepicker ng-model="time" init-value="12:00:00"></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>
                <b><?php echo Lang::get('slave.main').Lang::get('slave.hot_point'); ?></b>
                <div class="form-group">
                    <select class="form-control" name="main"
                        id="main" ng-model="formData.main" ng-init="formData.main=0" required>
                            <option value="0"><?php echo Lang::get('serverapi.select_main');?></option>
                        <?php foreach ($main as $k => $v) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $v;?></option>
                        <?php } ?>      
                    </select>
                </div>
                <div class="clearfix">
                    <br/>
                </div>
                <div class="form-group" style="height: 40px;">
                    <div class="col-md-2" style="padding: 0">
                        <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.btn_submit'); ?>" ng-click="processSX()"/>
                    </div>
                </div>
            </div>
            <div ng-show="1==formData.set_type">
                <div class="form-group">
                    <select class="form-control" name="mana_id"
                        id="mana_id" ng-model="MCData.mana_id" ng-init="MCData.mana_id=0" required>
                            <option value="0"><?php echo Lang::get('serverapi.select_mana_partner_id');?></option>
                        <?php foreach ($mana_partner_ids as $k => $v) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $v;?></option>
                        <?php } ?>      
                    </select>
                </div>
                <div class="form-group">
                    <select class="form-control" name="crystal_id"
                        id="crystal_id" ng-model="MCData.crystal_id" ng-init="MCData.crystal_id=0" required>
                            <option value="0"><?php echo Lang::get('serverapi.select_crystal_partner_id');?></option>
                        <?php foreach ($crystal_partner_ids as $k => $v) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $v;?></option>
                        <?php } ?>      
                    </select>
                </div>
                <div class="clearfix">
                    <br/>
                </div>
                <div class="form-group" style="height: 40px;">
                    <div class="col-md-2" style="padding: 0">
                        <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.btn_submit'); ?>" ng-click="processManaCrystal()"/>
                    </div>
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

    <div style="padding-top:20px;">
        <p><font size="3">--以下为查询操作记录部分--</p></font>
    </div>

    <div class="form-group margin-top-10" style="margin-bottom:60px;">
        <div class="col-md-5" style="padding: 0">
            <div class="input-group">
                <quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
                <i class="glyphicon glyphicon-calendar"></i>
            </div>
        </div>
        <div class="col-md-5" style="padding: 0">
            <div class="input-group">
                <quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
                <i class="glyphicon glyphicon-calendar"></i>
            </div>
        </div>
        <div class="col-md-2">
            <input type="submit" class="btn btn-info" ng-click="searchFrom('/game-server-api/mnsg/setSoulCasket')"
                value="<?php echo Lang::get('basic.btn_show') ?>" />
        </div>
    </div>

    <div class="col-xs-12" style="padding-left: 0px;">
        <table class="table table-striped">
            <thead>
                <tr class="info">
                    <td><b>game_name</b></td>
                    <td><b>desc</b></td>
                    <td><b>type</b></td>
                    <td><b>user_name</b></td>
                    <td><b>created_at</b></td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="t in items">
                    <td>{{t.game_name}}</td>
                    <td>{{t.desc}}</td>
                    <td>{{t.log_key}}</td>
                    <td>{{t.username}}</td>
                    <td>{{t.created_at}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>