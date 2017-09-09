<script>
    function godRemoveController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.process = function (url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.is_alertIntegral = 0;
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
        $scope.alertIntegral = function (url) {
            alertService.alerts = $scope.alerts;
            $scope.formData.is_alertIntegral = 1;
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
        $scope.look = function(url){
            alertService.alerts = $scope.alerts;
            $scope.items = {};
            $http({
                'method' : 'post',
                'url' : url,
                'data' : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data){
                $scope.items = data;
            }).error(function(data){
                alertService.add('danger', data.error);
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="godRemoveController">
    <div class="row" >
        <div class="form-group" style="width:50%;">
            <select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
                <option value="0"><?php echo Lang::get('serverapi.select_main_game_server')?></option>
                <?php foreach ($servers as $key => $value) { ?>
                    <option value="<?php echo $value->server_id?>"><?php echo $value->server_name?></option>
                <?php }?>
            </select>
        </div>
        <div class="form-group" style="display:none;">
            <label>
                <input type="radio" name="name_or_id" value="1"  ng-model="formData.name_or_id" ng-init="formData.name_or_id=1"  ng-checked="true"/>
                <?php echo Lang::get('serverapi.playerID_set')?>
            </label>
            <label>
                <input type="radio" ng-model="formData.name_or_id" name="name_or_id" value="2"/>
                <?php echo Lang::get('serverapi.playerName_set')?>
            </label>
        </div>
        <div class="col-xs-6">
                <div class="form-group">
                    <textarea name="gift_data" ng-model="formData.gift_data"
                        placeholder="<?php echo Lang::get('serverapi.enter_tip') ?>"
                        rows="15" required class="form-control"></textarea>
                </div>   
        </div>
        <div class="form-group col-md-6">
            <p><font color=red size=4><?php echo Lang::get('serverapi.allServerFightremind1')?></font></p>
            <p><font color=red size=4><?php echo Lang::get('serverapi.allServerFightremind2')?></font></p>
            <p><font color=red size=4><?php echo Lang::get('serverapi.allServerFightremind3')?></font></p>
        </div>
        <div class="clearfix">
                <br/>
        </div>
        <div class="form-group" style="height: 40px;">
            <div class="col-md-2">
                <input type='button' class="btn btn-primary"
                       value="<?php echo Lang::get('serverapi.look') ?>"
                       ng-click="look('/game-server-api/all/server/fight/look')"/>
            </div>
            <div class="col-md-2" style="padding: 0">
                <input type='button' class="btn btn-warning"
                       value="<?php echo Lang::get('serverapi.tournament_set') ?>"
                       ng-click="process('/game-server-api/all/server/fight')"/>
            </div>
            <div class="col-md-2" style="padding: 0">
                <input type='button' class="btn btn-warning"
                       value="<?php echo Lang::get('serverapi.alertIntegral') ?>"
                       ng-click="alertIntegral('/game-server-api/all/server/fight')"/>
            </div>
        </div>
        
    </div>
    <div class="row margin-top-10">
        <div class="col-xs-6"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>
    <div class="col-xs-12">
        <table class="table table-striped">
            <thead>
                <tr class="info">
                    <td ng-if="formData.name_or_id == 2"><b><?php echo Lang::get('serverapi.gm_player_name') ?></b></td>
                    <td ng-if="formData.name_or_id == 1"><b><?php echo Lang::get('serverapi.gm_player_id') ?></b></td>
                    <td><b>ServerID</b></td>
                    <td><b>opeartorID</b></td>
                    <td><b>teamID</b></td>
                </tr>
            </thead>
            <tbody>
                <tr ng-repeat="t in items">
                    <td ng-if="formData.name_or_id == 2">{{t.player_name}}</td>
                    <td ng-if="formData.name_or_id == 1">{{t.player_id}}</td>
                    <td>{{t.server_id}}</td>
                    <td>{{t.opertor_id}}</td>
                    <td>{{t.team_id}}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>