<script>
    function YYSGSendPartnerController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.checkData = {};
        $scope.activities = [];
        $scope.records = [];
        $scope.is_send = 1;

        $scope.SendPartner = function () {
            if(!confirm("<?php echo Lang::get('serverapi.confirm_send') ?>")){   //防止失误重复点击
                return;
            }
            $scope.records = [];
            $scope.alerts = [];
            $scope.formData.type = 'send';
            alertService.alerts = $scope.alerts;
            $http({
                'method': 'post',
                'url': '/game-server-api/yysg/send/partner',
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                alertService.add('success', data.msg);
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

        $scope.CheckRecord = function () {
            $scope.records = [];
            $scope.checkData.type = 'check';
            $scope.checkData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.checkData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            $http({
                'method': 'post',
                'url': '/game-server-api/yysg/send/partner',
                'data': $.param($scope.checkData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                $scope.records = data.records;
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

        $scope.switch_view = function(statu) {
            $scope.is_send = statu;
        }

    }
</script>
<div class="col-xs-12" ng-controller="YYSGSendPartnerController">
    <div class="row">
        <div class="eb-content">
            <div class="form-group col-md-8">
                <div class="col-md-3">
                    <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.switch_send_partner'); ?>" ng-click="switch_view(1)"/>
                </div>
                <div class="col-md-3">
                    <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.switch_check_record'); ?>" ng-click="switch_view(0)"/>
                </div>
            </div>
            <div ng-show="is_send==1">
                <div class="form-group col-md-10">
                    <select class="form-control" name="server_id" id="server_id" required
                        ng-model="formData.server_id" ng-init="formData.server_id=0">
                        <option value="0"><?php echo Lang::get('slave.select_server');?></option>
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-10">
                    <select class="form-control" name="partner_id" id="partner_id" required
                        ng-model="formData.partner_id" ng-init="formData.partner_id=0">
                        <option value="0"><?php echo Lang::get('serverapi.select_partner');?></option>
                        <?php foreach ($partners as $id => $name) { ?>
                            <option value="<?php echo $id; ?>"><?php echo $name;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <input type="text" class="form-control"
                        placeholder="<?php echo Lang::get('slave.player_name')?>"
                        ng-model="formData.player_name" name="player_name"?>
                </div>
                <div class="form-group col-md-4">
                    <input type="number" class="form-control"
                        placeholder="<?php echo Lang::get('slave.player_id')?>"
                        ng-model="formData.player_id" name="player_id"?>
                </div>
                <div class="form-group col-md-4">
                    <div class="col-md-2">
                        <input type='button' class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit'); ?>" ng-click="SendPartner()"/>
                    </div>
                </div>
                <div class="form-group col-md-10">
                    <ul>
                        <li><b><?php echo Lang::get('serverapi.send_partner_note1'); ?></b></li>
                        <li><b><?php echo Lang::get('serverapi.send_partner_note2'); ?></b></li>
                    </ul>
                </div>
            </div>
            <div ng-show="is_send==0">
                <div class="form-group col-md-10">
                    <select class="form-control" name="partner_id" id="partner_id" required
                        ng-model="checkData.partner_id" ng-init="checkData.partner_id=0">
                        <option value="0"><?php echo Lang::get('serverapi.select_partner');?></option>
                        <?php foreach ($partners as $id => $name) { ?>
                            <option value="<?php echo $id; ?>"><?php echo $name;?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <input type="text" class="form-control"
                        placeholder="<?php echo Lang::get('slave.operator')?>"
                        ng-model="checkData.operator" name="operator"?>
                </div>
                <div class="form-group col-md-3">
                    <input type="text" class="form-control"
                        placeholder="<?php echo Lang::get('slave.player_name')?>"
                        ng-model="checkData.player_name" name="player_name"?>
                </div>
                <div class="form-group col-md-3">
                    <input type="number" class="form-control"
                        placeholder="<?php echo Lang::get('slave.player_id')?>"
                        ng-model="checkData.player_id" name="player_id"?>
                </div>
                <div class="form-group col-md-12">
                    <div class="col-md-6">
                        <div class="input-group">
                            <quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <div class="col-md-2">
                        <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.btn_show'); ?>" ng-click="CheckRecord()"/>
                    </div>
                </div>
                <div class="col-xs-12">
                    <table class="table table-striped">
                        <thead>
                            <tr class="info">
                                <td><b><?php echo Lang::get('slave.operation_time'); ?></b></td>
                                <td><b><?php echo Lang::get('slave.operator'); ?></b></td>
                                <td><b><?php echo Lang::get('slave.server_name'); ?></b></td>
                                <td><b><?php echo Lang::get('slave.player_id'); ?></b></td>
                                <td><b><?php echo Lang::get('slave.player_name'); ?></b></td>
                                <td><b><?php echo Lang::get('slave.partner'); ?></b></td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="t in records">
                                <td>{{t.time}}</td>
                                <td>{{t.operator}}</td>
                                <td>{{t.server_name}}</td>
                                <td>{{t.player_id}}</td>
                                <td>{{t.player_name}}</td>
                                <td>{{t.giftbag_id}}-{{t.extra_msg}}</td>
                            </tr>
                        </tbody>
                    </table>
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

</div>