<script>
    function addNSPromotionController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.start_time = null;
        $scope.end_time = null;
        $scope.formData = {};
        $scope.process = function (url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.is_timing = 0;
            $scope.formData.url_type = 0;
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
                var extra_activity = data.extra_activity;
                if(1 == extra_activity){
                    /*var r=confirm("是否现在设置奖励？")
                    if(r == true){
                        window.open("http://eastblue.local/game-server-api/player/escort"); 
                    }*/
                    alertService.add('info', '所开活动中有需要对其进行设置的活动');
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };
        $scope.lookup = function (url) {
            $scope.formData.is_timing = 0;
            $scope.formData.url_type = 0;
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
        $scope.timing = function(url) {
            if (!confirm('确定每个伺服器的活动开启时间大于上次所开与当前冲突活动的结束时间2分钟以上?')) {
                return;
            }
            alertService.alerts = $scope.alerts;
            $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.is_timing = 1;
            $scope.formData.url_type = 0;
            $http({
                'method' : 'post',
                'url'    : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                var result = data;
                if (result.status == 'ok') {
                    alertService.add('success', result.msg);
                } else if (result['status'] == 'error') {
                    alertService.add('danger', result.msg);
                }
            }).error(function(data) {
                alertService.add('danger', data.error);
            });
        };
        $scope.extra_set = function(url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
            $scope.formData.is_timing = 0;
            $scope.formData.url_type = 1;
            $http({
                'method' : 'post',
                'url'    : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function(data) {
                alertService.add('danger', data.error);
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="addNSPromotionController">
    <div class="row">
        <div class="eb-content">
            <div class="form-group">
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <br/>
            </div>
            <div class="form-group">
                <select class="form-control" name="server_id"
                        id="select_game_server" ng-model="formData.server_id"
                        ng-init="formData.server_id=0" multiple="multiple"
                        ng-multiple="true" size=10>
                    <optgroup
                        label="<?php echo Lang::get('serverapi.select_game_server') ?>(按住Ctrl可多选)">
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
                        <?php } ?>
                    </optgroup>
                </select>
            </div>

            <div class="form-group">
                <select class="form-control" name="promotion_type"
                        id="select_game_server" ng-model="formData.promotion_type"
                        ng-init="formData.promotion_type=0" multiple="multiple"
                        ng-multiple="true" size=20>
                    <optgroup
                        label="请选择活动(按住Ctrl可多选)">
                        <?php foreach ($activities as $k => $v) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $v; ?></option>
                        <?php } ?>
                    </optgroup>
                </select>
            </div>
            <?php if('flsg' == $game_code){?>
                <div class="form-group" ng-if="formData.promotion_type == 76">
                    <input type="text" class="form-control" placeholder="<?php echo Lang::get('serverapi.enter_proportion') ?>"
                        ng-model="formData.proportion" name="proportion"/>
                </div>
            <?php }else{ ?>
                <div class="from-group">
                    <select class="form-control" name="server_id" ng-model="formData.ratio" ng-init="formData.ratio=500"
                            ng-if="formData.promotion_type == 15">
                        <option value="500"><?php echo Lang::get('serverapi.shop_back1') ?></option>
                        <option value="1000"><?php echo Lang::get('serverapi.shop_back2') ?></option>
                        <option value="2000"><?php echo Lang::get('serverapi.shop_back3') ?></option>
                        <option value="3000"><?php echo Lang::get('serverapi.shop_back4') ?></option>
                    </select>
                </div>
                <?php if('nszj' == $game_code){?>
                <div class="from-group">
                    <select class="form-control" name="server_id" ng-model="formData.ratio" ng-init="formData.ratio=3000"
                            ng-if="formData.promotion_type == 95">
                        <option value="3000">30%</option>
                        <option value="5000">50%</option>
                        <option value="7000">70%</option>
                    </select>
                </div>
                <!-- 名人堂设置入选条件-->
                <div class="from-group" ng-if="formData.promotion_type == 113">
                    <div class="col-md-4">
                    <input type="text" class="form-control" ng-model="formData.fighting" name="fighting" placeholder="<?php echo Lang::get('serverapi.fighting') ?>" />
                    </div>
                    <div class="col-md-4">
                    <input type="text" class="form-control" ng-model="formData.vip_lev" name="vip_lev" placeholder="<?php echo Lang::get('serverapi.vip_lev') ?>" />
                    </div>
                    <div class="col-md-4">
                    <input type="button" class="btn btn-warning" value="<?php echo Lang::get('serverapi.set_celebrity') ?>" ng-click="extra_set('/game-server-api/promotion/ns')"/>
                    </div>
                </div>
                <?php }?>
                <div class="from-group">
                    <select class="form-control" name="server_id" ng-model="formData.ratio2" ng-init="formData.ratio2=3000"
                            ng-if="formData.promotion_type == 25">
                        <option value="3000">+<?php echo Lang::get('serverapi.shop_back4') ?></option>
                        <option value="5000">+<?php echo Lang::get('serverapi.shop_back5') ?></option>
                        <option value="10000">+<?php echo Lang::get('serverapi.shop_back6') ?></option>
                    </select>
                </div>
            <?php } ?>
            <p><font color=red>1、體力護送半價活動、煉金洗練返金活動、塔羅牌許願增利活動、清涼夏日、商城返點、挖礦這些只針對女神，風流三國開啟以上活動請到風流三國活動.
                    溫馨提示:女神過大年和春節7天樂當天開啟，次日才可以領取第一天獎勵，其他功能當天開啟就可以領取第一天獎勵</font></p>
            <p><font color=red>2、1个月半内的都算新服。其他是老服</font></p>
            <p><font color=red>3、建议单独开启的活动，请在一次开启操作中只开启此活动，不要和其他活动在一次操作中同时开启</font></p>
            <p><font color=red>4、开启后需要设置的活动：<a href="/game-server-api/promotion/award/set" target="_blank">累计消费、累计储值、单笔储值、累计签到、累计储值2、储值大返利、累计消费2、单笔储值2</a> 
                <a href="/game-server-api/promotion/limit/buy/set" target="_blank">限时抢购设置</a>
                <a href="/game-server-api/promotion/group/buy/set" target="_blank">团购设置</a>
                <a href="/game-server-api/promotion/online/award/set" target="_blank">在线奖励设置</a>(点击可打开操作界面对活动进行设置)
            </font></p>

            <div class="form-group" style="height: 40px;">
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.promotion_set') ?>"
                           ng-click="process('/game-server-api/promotion/ns')"/>
                </div>
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.promotion_lookup') ?>(单服)"
                           ng-click="lookup('/game-server-api/promotion/ns/lookup')"/>
                </div>
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-danger"
                           value="<?php echo Lang::get('serverapi.promotion_close') ?>"
                           ng-click="process('/game-server-api/promotion/ns/close')"/>
                </div>
                <?php if('flsg' == $game_code){?>
                    <div class="col-md-2" style="padding: 0" ng-if="formData.promotion_type == 53 || formData.promotion_type == 54">
                        <input type='button' class="btn btn-info"
                               value="<?php echo Lang::get('serverapi.urgent_open') ?>"
                               ng-click="process('/game-server-api/promotion/ns/urgent_open')"/>
                    </div>
                    <div class="col-md-2" style="padding: 2" ng-if="formData.promotion_type == 53 || formData.promotion_type == 54">
                        <input type='button' class="btn btn-warning"
                               value="<?php echo Lang::get('serverapi.urgent_close') ?>"
                               ng-click="process('/game-server-api/promotion/ns/urgent_close')"/>
                    </div>
                    <div class="col-md-2" style="padding: 2" ng-if="formData.promotion_type == 76">
                        <input type='button' class="btn btn-warning"
                               value="<?php echo Lang::get('serverapi.set_proportion') ?>"
                               ng-click="process('/game-server-api/promotion/ns/urgent_open')"/>
                    </div>
                <?php }elseif ('nszj' == $game_code){ ?>
                    <div class="col-md-2" style="padding: 0" ng-if="formData.promotion_type == 55 || formData.promotion_type == 56">
                        <input type='button' class="btn btn-info"
                               value="<?php echo Lang::get('serverapi.urgent_open') ?>"
                               ng-click="process('/game-server-api/promotion/ns/urgent_open')"/>
                    </div>
                    <div class="col-md-2" style="padding: 2" ng-if="formData.promotion_type == 55 || formData.promotion_type == 56">
                        <input type='button' class="btn btn-warning"
                               value="<?php echo Lang::get('serverapi.urgent_close') ?>"
                               ng-click="process('/game-server-api/promotion/ns/urgent_close')"/>
                    </div>
                    <?php }?>
                    <div class="col-md-2" style="padding: 0">
                        <input type='button' class="btn btn-warning"
                               value="<?php echo Lang::get('serverapi.promotion_timing') ?>"
                               ng-click="timing('/game-server-api/promotion/ns')"/>
                    </div>
            </div>
        </div>
    </div>
    <div class="row margin-top-10">
        <div class="eb-content">
            <alert ng-repeat="alert in alerts" type="alert.type"
                close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
    <div class="row margin-top-10 col-xs-6">
        <div ng-repeat="t in items">
            <div class="panel panel-info">
                <div class="panel-heading"><?php echo Lang::get('serverapi.promotion_info') ?></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Lang::get('serverapi.yuanbao_server') ?></dt>
                        <dd>{{t.server_name}}</dd>
                        <dt><?php echo Lang::get('serverapi.promotion_name') ?></dt>
                        <dd>{{t.promotion_name}}</dd>
                        <!-- <dt><?php echo Lang::get('serverapi.promotion_type') ?></dt>
                        <dd>{{t.ratio}}</dd> -->
                        <dt><?php echo Lang::get('serverapi.promotion_is_open') ?></dt>
                        <dd>{{t.is_open}}</dd>
                        <dt><?php echo Lang::get('serverapi.promotion_open_time') ?></dt>
                        <dd>{{t.open_time}}</dd>
                        <dt><?php echo Lang::get('serverapi.promotion_close_time') ?></dt>
                        <dd>{{t.close_time}}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>