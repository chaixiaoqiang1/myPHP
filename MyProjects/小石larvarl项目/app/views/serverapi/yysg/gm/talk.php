<script type="text/javascript">
    function modalReplyCtroller1($scope, $modalInstance, gm, $http, alertService) {
        $scope.gm = gm;
        $scope.gmData = {};

        $scope.cancel = function () {
            $modalInstance.dismiss('cancel');
        };

        $scope.process = function(url) {
            alert('111');
            $http({
                'method' : 'post',
                'url'	 : url,
                'data'   : $.param($scope.gmData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                //alertService.add('success', data.result);
                $modalInstance.close(gm);
            }).error(function(data) {
                alertService.add('danger', data.error);
            });
        };
    }

    function loadGMController1($scope, $http, alertService, $modal)
    {
        $scope.alerts = [];
        $scope.formData = {};
        $scope.questions = [];
        $scope.reply_dones= [];
        $scope.processFrom = function(url) {
            alertService.alerts = $scope.alerts;
            $http({
                'method' : 'post',
                'url'	 : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                $scope.questions = data;
                $scope.reply_dones=data.reply_done;
                /*if (!data.GM_Logs) {
                 alertService.add('danger', JSON.stringify(data));
                 }*/
                //alertService.add('success', data.result);
                if (data.error=="没有要回复玩家的内容") {
                    alertService.add('success', data.error);
                };
            }).error(function(data) {
                alertService.add('danger',  data.error);
            });
        };
        /*add function myDate*/
        $scope.myDate = function(timestamp) {
            return timestamp*1000;
        };
        $scope.done = function(gm) {
            alertService.alerts = $scope.alerts;
            var params = {
                'ser_id':gm.ser_id,
                'server_gm_id' : gm.id,
                'player_id' : gm.player_id,
                'reply_message' : ''
            };
            $http({
                'method' : 'post',
                'url'	 : '/game-server-api/yysg/gm/reply',
                'data'   : $.param(params),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                gm.IsDone = 1;
                // alertService.add('success', data.result);
            }).error(function(data) {
                alertService.add('danger', JSON.stringify(data));
            });
        };

        $scope.process = function(url) {
            alertService.alerts = $scope.alerts;
            $http({
                'method' : 'post',
                'url'	 : url,
                'data'   : $.param($scope.formData),
                'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
            }).success(function(data) {
                $scope.processFrom('/game-server-api/yysg/gmTalk');
                if (data.error=="没有要回复玩家的内容") {
                    alertService.add('success', data.error);
                };
            }).error(function(data) {
                alertService.add('danger', data.error);
            });
        };
    }
</script>
<div class="col-xs-12" ng-controller="loadGMController1">
    <div class="row" >
        <div class="col-xs-2" >
            <form action="/game-server-api/yysg/gmTalk" method="post" role="form"
                  ng-submit="processFrom('/game-server-api/yysg/gmTalk')"
                  onsubmit="return false;">
                <div class="form-group">
                    <select class="form-control" name="player_id"
                            id="select_player_id" ng-model="formData.player_id"
                            ng-init="formData.player_id=0" multiple="multiple" ng-multiple="true" size=10>
                        <optgroup label = "<?php echo Lang::get('serverapi.select_player') ?>">
                        <?php foreach ($player_list as $player) { ?>
                            <option value="<?php echo $player->player_id?>"><?php echo $player->player_id?></option>
                        <?php } ?>
                        </optgroup>
                    </select>
                </div>
                <div class="form-group">
                    <span><b><?php echo Lang::get('slave.certain_player'); ?></b></span>
                    <input type="number" class="form-control" id="enter_player_id" name="enter_payer_id" ng-model="formData.enter_player_id"
                           placeholder="<?php echo Lang::get('serverapi.enter_player_body') ?>">
                    <span><b><?php echo Lang::get('slave.last_msg_num'); ?></b></span>
                    <input type="number" class="form-control" name="page_num" ng-model="formData.page_num" ng-init="formData.page_num=10">
                    <br>
                </div>
                <input type="submit" class="btn btn-success"
                       value="<?php echo Lang::get('basic.btn_show') ?>" />
                <input type="submit" class="btn btn-danger" style="float: right;"
                       value="<?php echo Lang::get('basic.btn_send') ?>"
                       ng-click="process('/game-server-api/yysg/gmTalkSend')"/>
                </br>
                </br>
                <div class="form-group">
					<textarea type="text" class="form-control" id="msg" name="msg"
                              placeholder="<?php echo Lang::get('serverapi.enter_message_body') ?>"
                              ng-model="formData.msg"  rows="8"></textarea>
                </div>
            </form>
        </div>
        <div class="col-xs-10" style="overflow-y:auto;height:500px;">
            <p ng-repeat="q in questions" >
                Name：{{q.talker_name}}---<small><a href="/game-server-api/yysg/player?player_id={{q.talker_id}}" target="{{q.talker_id}}_blank">(ID：{{q.talker_id}})</a>---(Time：{{q.question_time}})</small><small ng-if="q.talker_id==10000">---(gm_name：{{q.gm_name}})</small><br>
                <b>{{q.msg}}</b>
            </p>
        </div>

        <!-- /.col -->
    </div>

    <div class="col-xs-6 margin-top-10">
        <div class="panel panel-danger" ng-if="q.is_done == 0"
             ng-repeat="q in questions" ng-init="q.server_id = formData.server_id">
            <div class="panel-heading">
                (Title：{{q.title}})---(Name：{{q.player_name}})---(ID：{{q.player_id}})---(Time：{{q.question_time}})---(gm_name：{{q.gm_name}})
            </div>
            <div class="panel-body">{{q.question}}</div>
            <div class="panel-footer">
                <button ng-click="reply(q)" class="btn btn-default"><?php echo Lang::get('basic.btn_reply')?></button>
                <!--<button ng-click="done(q)" class="btn btn-default"><?php /*echo Lang::get('basic.btn_done')*/?></button>-->
            </div>
        </div>

        <div class="panel panel-success" ng-if="q.is_done == 1"
             ng-repeat="q in questions">
            <div class="panel-heading">
                (Title：{{q.title}})---(Name：{{q.player_name}})---(ID：{{q.player_id}})---(Time：{{q.question_time}})---(gm_name：{{q.gm_name}})
            </div>
            <div class="panel-body">{{q.question}}</div>
            <div class="panel-footer">{{q.reply_message}}</div>

        </div>
    </div>
</div>

<script type="text/ng-template" id="replyModalContent.html">
    <div class="modal-header">
        <h3>{{gm.question}}</h3>
    </div>
    <form action="/game-server-api/yysg/gm/reply" method="post" role="form" ng-submit="replyFrom('/game-server-api/yysg/gm/reply')" onsubmit="return false;">
        <div class="modal-body">
            <textarea ng-model="gmData.reply_message" rows="5" class="form-control" autofocus></textarea>
            <input type="hidden" ng-model="gmData.player_id" ng-init="gmData.player_id = gm.player_id"/>
            <input type="hidden" ng-model="gmData.server_gm_id" ng-init="gmData.server_gm_id = gm.id" />
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary"><?php echo Lang::get('basic.btn_reply')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
    </form>
    <div class="col-xs-6">
        <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
    </div>
</script>