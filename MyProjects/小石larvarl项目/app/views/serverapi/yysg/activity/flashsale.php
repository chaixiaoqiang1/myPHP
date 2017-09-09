<script>
    function FlashSaleController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.activities = [];

        $scope.process = function (type) {
            $scope.activities = [];
            $scope.formData.type = type;
            <?php foreach ($flash_types as $id => $name) { ?>
                $scope.formData.start_time_<?php echo $id; ?> = $filter('date')($scope.start_time_<?php echo $id; ?>, 'yyyy-MM-dd HH:mm:ss');
                $scope.formData.end_time_<?php echo $id; ?> = $filter('date')($scope.end_time_<?php echo $id; ?>, 'yyyy-MM-dd HH:mm:ss');
            <?php } ?>
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            $http({
                'method': 'post',
                'url': '/game-server-api/yysg/flashsale',
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                if('set' == type){
                    if(data.success){
                        alertService.add('success', data.success);
                    }
                    if(data.fail){
                        alertService.add('danger', data.fail);
                    }
                }
                if('get' == type){
                    $scope.activities = data;
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

    }
</script>
<div class="col-xs-12" ng-controller="FlashSaleController">
    <div class="row">
        <div class="eb-content">
                <div class="form-group">
                    <select class="form-control" name="server_ids" id="select_game_server" required
                        ng-model="formData.server_ids" size="8" multiple="true">
                        <optgroup label="<?php echo Lang::get('slave.select_server'); ?>">
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
                        <?php } ?>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <?php foreach ($flash_types as $id => $name) { ?>
                        <label> <input type="checkbox" name="flash_types_<?php echo $id; ?>"
                            ng-model="formData.flash_types_<?php echo $id; ?>" ng-init="flash_types_<?php echo $id; ?>=0"
                            ng-true-value="1" ng-false-value="0" />
                            <?php echo $name;?>
                        </label>
                    <?php } ?>
                </div>
                <?php foreach ($flash_types as $id => $name) { ?>
                <div class="panel panel-info" ng-show="1 == formData.flash_types_<?php echo $id; ?>">
                    <div class="panel-heading"><?php echo $name; ?></div>
                    <div class="panel-body">
                        <div class="form-group" style="height: 30px;">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <quick-datepicker ng-model="start_time_<?php echo $id; ?>" init-value="00:00:00"></quick-datepicker>
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <quick-datepicker ng-model="end_time_<?php echo $id; ?>" init-value="23:59:59"></quick-datepicker>
                                    <i class="glyphicon glyphicon-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="giftbag_ids" id="giftbag_ids_<?php echo $id; ?>" required
                                ng-model="formData.giftbag_ids_<?php echo $id; ?>" size="8" multiple="true">
                                <optgroup label="<?php echo Lang::get('serverapi.flash_sale_giftbags'); ?>">
                                <?php foreach ($giftbags as $k => $v) { ?>
                                    <option value="<?php echo $k; ?>"><?php echo $v;?></option>
                                <?php } ?>
                                </optgroup>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-group col-md-4">
                                <input type="number" class="form-control"   required
                                    placeholder="<?php echo Lang::get('serverapi.mg_price')?>"
                                    ng-model="formData.price_<?php echo $id; ?>" name="price_<?php echo $id; ?>"?>
                            </div>
                            <div class="form-group col-md-4">
                                <input type="number" class="form-control"   required
                                    placeholder="<?php echo Lang::get('serverapi.limit_count')?>"
                                    ng-model="formData.limit_count_<?php echo $id; ?>" name="limit_count_<?php echo $id; ?>"?>
                            </div>
                            <div class="form-group col-md-4">
                                <input type="number" class="form-control"   required
                                    placeholder="<?php echo Lang::get('serverapi.player_limit_count')?>"
                                    ng-model="formData.player_limit_count_<?php echo $id; ?>" name="player_limit_count_<?php echo $id; ?>"?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="form-group" style="height: 40px;">
                    <div class="col-md-2" style="padding: 0">
                        <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.btn_submit'); ?>" ng-click="process('set')"/>
                    </div>
                    <div class="col-md-2" style="padding: 0">
                        <input type='button' class="btn btn-primary" value="<?php echo Lang::get('basic.check_statu'); ?>" ng-click="process('get')"/>
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
    <div class="row margin-top-10 col-xs-6">
        <div ng-repeat="a in activities">
            <div class="panel panel-info">
                <div class="panel-heading">{{a.flash_types}}</div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Lang::get('serverapi.start_time') ?></dt>
                        <dd>{{a.start_time}}</dd>
                        <dt><?php echo Lang::get('serverapi.end_time') ?></dt>
                        <dd>{{a.end_time}}</dd>
                        <dt><?php echo Lang::get('serverapi.giftbag') ?></dt>
                        <dd>{{a.giftbags}}</dd>
                        <dt><?php echo Lang::get('serverapi.mg_price') ?></dt>
                        <dd>{{a.price}}</dd>
                        <dt><?php echo Lang::get('serverapi.limit_count') ?></dt>
                        <dd>{{a.limit_count}}</dd>
                        <dt><?php echo Lang::get('serverapi.player_limit_count') ?></dt>
                        <dd>{{a.player_limit_count}}</dd>
                        <dt><?php echo Lang::get('serverapi.bought_count') ?></dt>
                        <dd>{{a.bought_count}}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>