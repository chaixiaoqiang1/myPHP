<script>
    function ServerController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.process = function (url) {
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            if($scope.formData.server_id==0){
                alertService.add('danger', '请选择一个服务器');
            }else{
                $http({
                    'method': 'post',
                    'url': '/game-server-api/mnsgserver/openandswitch',
                    'data': $.param($scope.formData),
                    'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
                }).success(function (data) {
                    alertService.add('success', data.msg);
                }).error(function (data) {
                    alertService.add('danger', data.error);
                });
            }
        };

    }
</script>
<div class="col-xs-12" ng-controller="ServerController">
    <div class="row">
        <div class="eb-content">
            <div class="form-group">
                <select class="form-control" name="server_id"
                    id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" size=6 required>
                        <option value='0'>选择服务器</option>
                    <?php foreach ($servers as $k => $v) { ?>
                        <option value="<?php echo $v->server_id?>"><?php echo $v->server_name.':'.$v->server_track_name;?></option>
                    <?php } ?>      
                </select>
            </div>
            <div class="form-group">
                <select class="form-control" name="operation_type"
                        id="operation_type" ng-model="formData.operation_type"
                        ng-init="formData.operation_type=0">
                        <option value="0">切换服务器对外显示状态</option>
                       <!-- <option value="1">创建新的服务器并命名</option> -->
                </select>
            </div>
            <div  class="form-group" ng-if="formData.operation_type==0">
                <select class="form-control" name="is_hide"
                        id="is_hide" ng-model="formData.is_hide"
                        ng-init="formData.is_hide=0">
                        <option value="0">对外显示（普通玩家登陆选服可见）</option>
                        <option value="1">对外隐藏（普通玩家登陆选服不可见）</option>
                </select>
            </div>
            <div  class="form-group" ng-if="formData.operation_type==1">
                <input type="text" name="server_name"
                        id="server_name" ng-model="formData.server_name" placeholder="请输入新服务器在游戏中显示的名字" required style="width:300px" />
                <br/>
                <br/>
                <b>此时应该选择一个已经创建好的服务器并输入一个在游戏中将显示的名字，成功后内玩可见，玩家不可见，如需玩家可见需切换服务器对外显示状态</b>
            </div>
            <div class="clearfix">
                <br/>
            </div>
            <div class="form-group" style="height: 40px;">
                <div class="col-md-2" style="padding: 0">
                    <input type='button' class="btn btn-primary" value="提交" ng-click="process()"/>
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