<script>
    function activityAnnounceController($scope, $http, alertService, $modal, $filter) {
        $scope.alerts = [];
        $scope.start_time = null;
        $scope.end_time = null;
        $scope.formData = {};
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
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                        alertService.add('success', result[i].msg);
                    } else if (result[i]['status'] == 'error') {
                        alertService.add('danger', result[i].msg);
                    }
                }
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };
        $scope.look = function (url) {
            $scope.items = [];
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
        $scope.processDelete = function (list) {
            var modalInstance = $modal.open({
                templateUrl: 'delete_announce.html',
                controller: updateAnnounceController,
                resolve: {
                    list : function () {
                        return list;
                    }
                },
                backdrop : false,
                keyboard : false
            });
            modalInstance.result.then(function() {//模态窗口打开之后执行的函数
                $scope.process_update();   
            });
        };
        $scope.processUpdate= function (list) {
            var modalInstance = $modal.open({
                templateUrl: 'update_announce.html',
                controller: updateAnnounceController,
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
    function updateAnnounceController($scope, $modalInstance, list, $http, alertService, $filter) {
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
                var result = data.result;
                var len = result.length;
                for (var i = 0; i < len; i++) {
                    if (result[i].status == 'ok') {
                       $modalInstance.close();
                    } else if (result[i]['status'] == 'error') {
                        alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
                    }
                }
            }).error(function (data) {
                alert('error: ' + '<?php echo Lang::get('serverapi.operate_fail')?>');
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="activityAnnounceController">
    <div class="row">
        <div class="eb-content">
            <div class="form-group">
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <br/>
            </div>
            <div class="form-group">
                <div class="col-md-6" style="padding-left: 0">
                    <select class="form-control" name="choice" id="select_choice"
                        ng-model="formData.choice" ng-init="formData.choice=2">
                        <option value="2"><?php echo Lang::get('serverapi.activity_information') ?></option>
                        <option value="3"><?php echo Lang::get('serverapi.talk_top') ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-control" name="activity_id" id="activity_id"
                        ng-model="formData.activity_id" ng-init="formData.activity_id=0">
                        <option value="0">请选择活动(可不选)</option>
                        <?php foreach ($activity as $k => $v) { ?>
                            <option value="<?php echo $v->id ?>"><?php echo $v->id; ?> : <?php echo $v->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="clearfix">
                
            </div>
            <div class="form-group" style="padding-top: 8px">
                    <textarea type="text" class="form-control" id="announce_title"
                        placeholder="<?php echo Lang::get('serverapi.announce_title') ?>"
                        required ng-model="formData.announce_title" name="announce_title" rows="4"></textarea>
                </div>
            <div class="form-group">
                <div class="col-md-4" style="padding: 0">
                    <input type="text" class="form-control" id="announce_banner"
                        placeholder="<?php echo Lang::get('serverapi.announce_banner') ?>"
                        ng-model="formData.announce_banner" name="announce_banner" />
                    </div>
                    <div class="col-md-4">
                    <input type="text" class="form-control" id="announce_url"
                        placeholder="<?php echo Lang::get('serverapi.announce_url') ?>"
                        ng-model="formData.announce_url" name="announce_url" />
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="is_show"
                            id="is_show" ng-model="formData.is_show"
                            ng-init="formData.is_show=0">
                            <option value="0">进入游戏时不显示公告</option>
                            <option value="1">进入游戏时显示公告</option>
                        </select>
                    </div></br></br>
            </div>

            <div class="form-group" style="height: 40px;">
                <div class="col-md-1" style="padding: 0;display:none">
                    <input type="text" name="id" 
                    ng-model="formData.id" style="width:70px;">
                </div>
                <div class="col-md-3" style="padding: 0;">
                    <input type='button' class="btn btn-warning"
                           value="<?php echo Lang::get('serverapi.announce_release') ?>"
                           ng-click="process('/game-server-api/activity/announce/release')"/>
                </div>
                <div class="col-md-4" style="padding-left: 10;">
                    <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.activity_information_list') ?>"
                           ng-click="look('/game-server-api/activity/announce/look?type=2')"/>
                </div>
                <div class="col-md-4" style="padding-left: 10;">
                    <input type='button' class="btn btn-primary"
                           value="<?php echo Lang::get('serverapi.talk_top_list') ?>"
                           ng-click="look('/game-server-api/activity/announce/look?type=3')"/>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <p><font color=red size=4>1、发布公告：活动、banner和url为可选项</font></p>
            <p><font color=red size=4>2、获得列表：获得最近发布的最多20条公告，用来查看、更新和删除公告</font></p>
            <p><font color=red size=4>3、更新公告和删除公告时：请先进行获取列表操作，然后对其进行相应的操作，如果操作成功页面不会返回信息，也可以通过获取列表进行查看操作的结果</font></p>
            <p><font color=red size=4>4、关于banner的填写，请点击<a class="btn btn-primary" target="upload_img" href="<?php echo $platform->platform_api_url; ?>/upload_img">上传图片</a>
            选定对应的游戏以及图片后上传图片（请记住文件名）,然后请尝试用浏览器访问 <?php echo $platform->platform_api_url; ?>/assets/img/upload/<?php echo $game_id; ?>/文件名（包括后缀） 这个地址，如果成功访问到刚刚上传的图片，那么把这个地址填在banner处，否则请联系技术</font></p>
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
        <div ng-repeat="t in items">
            <div class="panel panel-info">
                <div class="panel-heading"><?php echo Lang::get('serverapi.promotion_info') ?></div>
                <div class="panel-body">
                    <dl class="dl-horizontal">
                        <dt><?php echo Lang::get('serverapi.announce_id') ?></dt>
                        <dd>{{t.id}}</dd>
                        <dt><?php echo Lang::get('serverapi.promotion_is_open') ?></dt>
                        <dd>{{t.is_open}}</dd>
                        <dt><?php echo Lang::get('serverapi.activity_title') ?></dt>
                        <dd>{{t.title}}</dd>
                        <dt>url:</dt>
                        <dd>{{t.url}}</dd>
                        <dt>banner:</dt>
                        <dd>{{t.banner}}</dd>
                        <dt>type:</dt>
                        <dd ng-if="t.type==2"><?php echo Lang::get('serverapi.activity_information')?></dd>
                        <dd ng-if="t.type==3"><?php echo Lang::get('serverapi.talk_top')?></dd>
                        <dt><?php echo Lang::get('serverapi.is_show') ?>:</dt>
                        <dd ng-if="t.is_show==0">NO</dd>
                        <dd ng-if="t.is_show==1">YES</dd>
                        <dt>created_time:</dt>
                        <dd>{{t.created_time}}</dd>
                        <dt>update_time:</dt>
                        <dd>{{t.update_time}}</dd>
                        <dt>start_time:</dt>
                        <dd>{{t.start_time}}</dd>
                        <dt>end_time:</dt>
                        <dd>{{t.end_time}}</dd>
                        <dd>
                            <input type='button' class="btn btn-warning"
                               value="<?php echo Lang::get('serverapi.announce_update') ?>"
                               ng-click="processUpdate(t)"/>
                            <input type='button' class="btn btn-danger"
                              value="<?php echo Lang::get('serverapi.announce_delete') ?>"
                              ng-click="processDelete(t)"/>
                       </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/ng-template" id="delete_announce.html">
        <div class="modal-header">
        </div>
        <form action="/game-server-api/activity/announce/update" method="post" role="form" ng-submit="process_update('/game-server-api/activity/announce/update')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label><?php echo Lang::get('serverapi.announce_id')?>:</label>    
                <input type="text" readonly="true" class="form-control" ng-model="listData.id" ng-init="listData.id = list.id"?>
            </div>
        </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('serverapi.announce_delete')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        <input type="hidden" ng-model="listData.is_open" ng-init="listData.is_open = 0" name="is_open"/>
        </form>
</script>
<script type="text/ng-template" id="update_announce.html">
        <div class="modal-header">
        </div>
        <form action="/game-server-api/activity/announce/update" method="post" role="form" ng-submit="process_update('/game-server-api/activity/announce/update')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="listData.start_time1" init-value="00:00:00"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
                <div class="col-md-6" style="padding: 0">
                    <div class="input-group">
                        <quick-datepicker ng-model="listData.end_time1" init-value="23:59:59"></quick-datepicker>
                        <i class="glyphicon glyphicon-calendar"></i>
                    </div>
                </div>
            </div>
            <div class="clearfix">
                <br/>
            </div>
            <div class="form-group">
                <div class="col-md-6" style="padding-left: 0">
                    <select class="form-control" name="choice" id="select_type"
                        ng-model="listData.choice" ng-init="listData.choice=list.type">
                        <option value="2"><?php echo Lang::get('serverapi.activity_information') ?></option>
                        <option value="3"><?php echo Lang::get('serverapi.talk_top') ?></option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-control" name="activity_id" id="activity_id"
                        ng-model="listData.activity_id" ng-init="listData.activity_id=list.activity_id">
                        <option value="0">请选择活动(可不选)</option>
                        <?php foreach ($activity as $k => $v) { ?>
                            <option value="<?php echo $v->id ?>"><?php echo $v->id; ?> : <?php echo $v->name; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="clearfix"> </div>
            <div class="form-group" style="padding-top: 8px">
                    <textarea type="text" class="form-control" id="announce_title"
                        placeholder="<?php echo Lang::get('serverapi.announce_title') ?>"
                        required ng-model="listData.announce_title" ng-init="listData.announce_title=list.title" name="announce_title" rows="4"></textarea>
                </div>
            <div class="form-group">
                <div class="col-md-4" style="padding: 0">
                    <input type="text" class="form-control" id="announce_banner"
                        placeholder="<?php echo Lang::get('serverapi.announce_banner') ?>"
                        ng-model="listData.announce_banner" ng-init="listData.announce_banner=list.banner" name="announce_banner" />
                    </div>
                    <div class="col-md-4">
                    <input type="text" class="form-control" id="announce_url"
                        placeholder="<?php echo Lang::get('serverapi.announce_url') ?>"
                        ng-model="listData.announce_url" ng-init="listData.announce_url=list.url" name="announce_url" />
                    </div>
                    <div class="col-md-4">
                        <select class="form-control" name="is_show"
                            id="is_show" ng-model="listData.is_show"
                            ng-init="listData.is_show=list.is_show">
                            <option value="0">进入游戏时不显示公告</option>
                            <option value="1">进入游戏时显示公告</option>
                        </select>
                    </div></br></br>
            </div>
        </div>
        <input type="hidden" ng-model="listData.id" ng-init="listData.id = list.id" name="id"/>
        <input type="hidden" ng-model="listData.is_open" ng-init="listData.is_open = 1" name="is_open"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('serverapi.announce_update')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>