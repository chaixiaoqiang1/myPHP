<script type="text/javascript">
function serverNoticeController($scope,$http,alertService,$filter){
    $scope.alerts = [];
    $scope.formData = {};
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.processForm = function(url){
        alertService.alerts = $scope.alerts;
        $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
        $scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.formData),
            'headers' : {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data){
            var len = data.length;
            for(var i =0;i<len;i++){
                if(data[i].status == 'ok'){
                    alertService.add('success',data[i].msg);
                }else if(data[i].status == 'error'){
                    alertService.add('danger',data[i].msg);
                }
            }
        }).error(function(data){
            alertService.add('danger',data.error);
        });
    };

}
</script>
<div class="col-xs-12" ng-controller="serverNoticeController">
    <div class="row">
        <div class="eb-content">

            <form action="/game-server-api/notice" method="post" role="form" ng-submit="processForm()" onsubmit="return false;">
                
                <div class="form-group" style="height:30px;">
                    <div class="col-md-2" style="padding: 0 0 0 0"><label>公告开始时间</label></div>
                    <div class="col-md-6" style="padding: 0 0 0 0">                       
                        <div class="input-group">
                            <quick-datepicker ng-model="start_time" init-value="00:00:00" ></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="height:30px;">
                    <div class="col-md-2" style="padding: 0 0 0 0"><label>公告结束时间</label></div>
                    <div class="col-md-6" style="padding: 0 0 0 0">                       
                        <div class="input-group">
                            <quick-datepicker ng-model="end_time" init-value="23:59:59" ></quick-datepicker> 
                            <i class="glyphicon glyphicon-calendar"></i>
                        </div>
                    </div>
                </div>

                <div class="form-group">

                    <select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=10>
                        <optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id?>"><?php echo $v->server_name.'----'.$v->game_id.'--'.$v->server_id.'--'.$v->server_internal_id;?></option>
                        <?php } ?>      
                        </optgroup>
                    </select>

<!--                     <div class="col-md-6" style="padding:10 0 0 0">
                        <select class="form-control" name="server_id" id="select_game_server" ng-model="formData.bulletin_id" ng-init="formData.bulletin_id=0" multiple="multiple" ng-multiple="true">
                            <optgroup label="请选择公告">
                                <?php ?>
                                <option>notice</option>
                                <?php ?>
                            </optgroup>
                        </select>
                    </div> -->
                </div>
                <div class="form-group col-md-8" style="padding: 0;">
                    <select class="form-control" name="position" id="form_pos" ng-model="formData.pos" ng-init="formData.pos=0">
                        <option value="0"><?php echo Lang::get('serverapi.select_annouce_pos') ?></option>
                        <?php foreach ($pos as $k => $v) { ?>
                        <option value="<?php echo $k?>"><?php echo $v;?></option>
                        <?php } ?>      
                    </select>
                </div>
                <?php if(59 == $game_id){ ?>
                 <div class="form-group col-md-4">
                    <select class="form-control"  name="area_id"
                        id="area_id" ng-model="formData.area_id"
                        ng-init="formData.area_id=0">
                        <option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
                        <option value="59"><?php echo Lang::get('serverapi.tw_area')?></option>
                        <option value="65"><?php echo Lang::get('serverapi.hk_area')?></option>
                    </select>
                </div>
                <?php } ?>
                <?php if(63 == $game_id){ ?>
                 <div class="form-group col-md-4">
                    <select class="form-control" name="area_id"
                        id="area_id" ng-model="formData.area_id"
                        ng-init="formData.area_id=0">
                        <option value="0"><?php echo Lang::get('serverapi.select_area') ?></option>
                        <option value="63"><?php echo Lang::get('serverapi.uk_area')?></option>
                        <option value="64"><?php echo Lang::get('serverapi.sg_area')?></option>
                    </select>
                </div>
                <?php } ?>
                <div class="clearfix"></div>
<!--                 <div class="clearfix" style="height:30px"></div>   -->

                <div class="form-group" >
                    <textarea type="text" class="form-control" id="form_content" placeholder="<?php echo Lang::get('serverapi.enter_announce_content') ?>"  ng-model="formData.content" name="content" rows="5"></textarea> 
                </div>
<!--                 <div class="form-group">
                    <input type="text" placeholder="请输入过期天数" ng-model="formData.days" name="days" />
                </div> -->
                <div class="form-group col-md-12" style="margin-left:-15px">
                    <input type="text" class="form-control" id="cycle_time"  placeholder="<?php echo Lang::get('amount.cycle')?>"  ng-model="formData.cycle_time" name="cycle_time" /> 
                </div>


                <div class="form-group" style="height: 30px;">
                    <br/>
                    <span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.notice_introduce1')?></span>
                </div>
                <br>
                <br>
                <div class="form-group" style="height:40px;">
                    <div class="col-md-4" style="padding:0;">
                        <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_make_notice')?>" ng-click="processForm('/game-server-api/notice')"/>
                    </div>
                   <!--  <div class="col-md-4" style="padding:0;">
                        <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_lookup_notice')?>" ng-click="lookupNotice('/game-server-api/lookupNotice')"/>
                    </div>
                    <div class="col-md-4" style="padding:0;">
                        <input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_stop_notice')?>" ng-click="stopNotice('/game-server-api/stopNotice')"/>
                    </div> -->
                </div>
            </form>
        </div>
    </div>
        <div class="row margin-top-10">
        <div class="eb-content"> 
            <alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
        </div>
    </div>

</div>