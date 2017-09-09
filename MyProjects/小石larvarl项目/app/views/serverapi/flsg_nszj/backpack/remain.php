<script>
    function remainController($scope, $http, alertService) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.process = function() {
            alertService.alerts = $scope.alerts;
            $http({
                'method' : 'post',
                'url'    : '/game-server-api/server/remain',
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                $scope.total = data.total;
                $scope.items = data.result;
                $scope.player = data.player;
            }).error(function(data) {
                alertService.add('danger', data.error);
            });
        }
    }
</script>
<div class="col-xs-12" ng-controller="remainController">
    <div class="row">
        <div class="eb-content">
            <form method="post" ng-submit="process()" onsubmit="return false;">
                <div class="form-group">
                    <select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=10>
                        <optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id?>"><?php echo $v->server_name.'----'.$v->game_id.'--'.$v->server_id.'--'.$v->server_internal_id;?></option>
                        <?php } ?>      
                        </optgroup>
                    </select>
                </div>  
                <div class="form-group col-md-6">
                    <input type="text" class="form-control" id="min_yuanbao"
                        placeholder="<?php echo Lang::get('slave.enter_min_yuanbao') ?>"
                         ng-model="formData.min_yuanbao" name="min_yuanbao" />
                </div>
                <div class="form-group" style="height:35px;">
                    <div class="input-group">
                        <input type="submit" class="btn btn-success" value="<?php echo Lang::get('basic.btn_submit') ?>" />
                    </div>
                </div>
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
                    <td><b><?php echo Lang::get('slave.server_name')?></b></td>
                    <td><b><?php echo Lang::get('slave.is_server_on');?></b></td>
                    <td><b><?php echo Lang::get('slave.created_at')?></b></td>
                    <td><b><?php echo Lang::get('slave.yuanbao');?></b></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{total.server_name}}</td>
                    <td>{{total.is_server_on}}</td>
                    <td>{{total.created_at}}</td>
                    <td>{{total.server_yuanbao}}</td>
                </tr>
                <tr ng-repeat="t in items">
                    <td>{{t.server_name}}</td>
                    <td>{{t.is_server_on}}</td>
                    <td>{{t.created_at}}</td>
                    <td>{{t.server_yuanbao}}</td>
                    
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="info">
                    <td><b>服务器</b></td>
                    <td><b>玩家名</b></td>
                    <td><b>玩家ID</b></td>
                    <td><b>玩家剩余元宝</b></td>
                </tr>
                <tr ng-repeat="p in player">
                    <td>{{p.server_name}}</td>
                    <td>{{p.player_name}}</td>
                    <td>{{p.player_id}}</td>
                    <td>{{p.yuanbao}}</td>
                </tr>
            </tbody>
        </table>
        
    </div>
   
</div>