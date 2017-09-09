<script>
    function ServerController($scope, $http, alertService, $filter) {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.process = function () {
            $scope.alerts = [];
            alertService.alerts = $scope.alerts;
            // if($scope.formData.crystal >= 10000){
            //     if(!confirm("<?php echo Lang::get('slave.please_confirm'); ?>")){
            //         return;
            //     }
            // }
            $http({
                'method': 'post',
                'url': '/game-server-api/mobilegame/editplayereconomy',
                'data': $.param($scope.formData),
                'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
            }).success(function (data) {
                $scope.formData = {};
                alertService.add('success', data.msg);
            }).error(function (data) {
                alertService.add('danger', data.error);
            });
        };

    }
</script>
<div class="col-xs-12" ng-controller="ServerController">
    <div class="row">
        <div class="eb-content">
            <div class="form-group">
                根据玩家的ID修改玩家的元宝等:<input type="text"  name="player_id"
                        id="player_id" ng-model="formData.player_id" placeholder="请输入要操作的玩家id" required />
            </div>
            <div class="form-group">
                <table style="margin: 10px 20px">
                <thead>
                    <tr>
                        <td>货币项</td>
                        <td>修改值</td>
                    </tr>
                </thead>
                    <tbody>
                        <tr>
                            <td>铜钱：</td>
                            <td><input type="number"  name="mana" id="mana" ng-model="formData.mana" placeholder="正增，负减"  /></td>
                        </tr>
                        <tr>
                            <td>元宝：</td>
                            <td><input type="number"  name="crystal" id="crystal" ng-model="formData.crystal" placeholder="正增，负减"  /></td>
                        </tr>
                        <tr>
                            <td>体力：</td>
                            <td><input type="number"  name="energy" id="energy" ng-model="formData.energy" placeholder="正增，负减"  /></td>
                        </tr>
                        <?php if('mnsg' == $game_code){?>
                            <tr>
                                <td>斗牛币：</td>
                                <td><input type="number"  name="top_coin" id="top_coin" ng-model="formData.top_coin" placeholder="正增，负减"  /></td>
                            </tr>
                            <tr>
                                <td>顶楼PK币：</td>
                                <td><input type="number"  name="arena_coin" id="arena_coin" ng-model="formData.arena_coin" placeholder="正增，负减"  /></td>
                            </tr>
                            <tr>
                                <td>制霸奖杯：</td>
                                <td><input type="number"  name="march_coin" id="march_coin" ng-model="formData.march_coin" placeholder="正增，负减"  /></td>
                            </tr>
                            <tr>
                                <td>社团徽章：</td>
                                <td><input type="number"  name="guild_coin" id="guild_coin" ng-model="formData.guild_coin" placeholder="正增，负减"  /></td>
                            </tr>
                            <tr>
                                <td>跨服币：</td>
                                <td><input type="number" disabled="true" name="region_coin" id="region_coin" ng-model="formData.region_coin" placeholder="正增，负减"  /></td>
                            </tr>        
                            <b>萌娘三国，只输入玩家的ID不填写其他数据即可得到玩家当前的数据值</b><br/>
                        <?php } ?>
                    </tbody>
                    <b>请勿输入正号，增加直接输入数字(如10，输入+10是不合法的)，减少请输入负数(如-10)，不修改某一项不输入即可</b>
                </table>
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
    <div>
        <table class="table table-striped">
            <b style="font-size:15px;color:red">最近30条记录</b>
            <thead>
                <tr class="info">
                    <td>操作人</td>
                    <td>操作时间</td>
                    <td>具体操作(第一位为player_id)</td>
                </tr>
            </thead>
            <tbody>
                <?php
                 foreach ($logs as $log) {
                    echo "<tr>";
                    echo "<td>$log->username</td>";
                    echo "<td>$log->created_at</td>";
                    echo "<td>$log->desc</td>";
                    echo "</tr>";
                } 
                ?>
            </tbody>
        </table>
    </div>
</div>