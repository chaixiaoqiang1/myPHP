<html>
<body>
<h3>PokerDataDaily</h3>
<table cellpadding="10" cellspacing="0" border="1">
    <thead>
    <tr bgcolor="red">
        <td colspan="8"><?php echo Lang::get('pokerData.users'); ?></td>
    </tr>
    <tr>
        <td><?php echo Lang::get('pokerData.date'); ?></td>
        <td><?php echo Lang::get('pokerData.wangzhandenglu'); ?></td>
        <td><?php echo Lang::get('pokerData.meiriyouxidenglu'); ?></td>
        <td><?php echo Lang::get('pokerData.nimingyouxidenglu'); ?></td>
        <td><?php echo Lang::get('pokerData.meiriwanpairenshu'); ?></td>
        <td><?php echo Lang::get('pokerData.laoyonghudenglu'); ?></td>
        <td><?php echo Lang::get('pokerData.zuozhuhuitoulv'); ?></td>
        <td><?php echo Lang::get('pokerData.huiliuyonghu'); ?></td>
    </tr>
    </thead>
    <tbody>
    <?php $i = 1; foreach ($table_yonghu as $day_yonghu) { ?>
        <tr>
            <td><?php echo $day_yonghu['date']; ?></td>
            <td><?php echo $day_yonghu['web_login']; ?></td>
            <td><?php echo $day_yonghu['game_login']; ?></td>
            <td><?php echo $day_yonghu['is_anoy_login']; ?></td>
            <td><?php echo $day_yonghu['play_game']; ?></td>
            <td><?php echo $day_yonghu['old_game_login']; ?></td>
            <td><?php if(1 != $i) echo $day_yonghu['retention_day2'].'%'; ?></td>
            <td><?php echo $day_yonghu['reflux']; ?></td>
        </tr>
    <?php $i++; } ?>
    </tbody>
</table>
<br/>
<table cellpadding="10" cellspacing="0" border="1">
    <thead>
    <tr bgcolor="cyan">
        <td colspan="5"><?php echo Lang::get('pokerData.chuzhi'); ?></td>
    </tr>
    <tr>
        <td><?php echo Lang::get('pokerData.date'); ?></td>
        <td><?php echo Lang::get('pokerData.chuzhi'); ?></td>
        <td><?php echo Lang::get('pokerData.rishouru'); ?></td>
        <td><?php echo Lang::get('pokerData.ARPPUzhi'); ?></td>
        <td><?php echo Lang::get('pokerData.shoufuyonghu'); ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($table_chuzhi as $day_chuzhi) { ?>
        <tr>
            <td><?php echo $day_chuzhi['date']; ?></td>
            <td><?php echo $day_chuzhi['dollor']; ?></td>
            <td><?php echo $day_chuzhi['user']; ?></td>
            <td><?php echo $day_chuzhi['ARPPU']; ?></td>
            <td><?php echo $day_chuzhi['first_pay']; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br/>
<table cellpadding="10" cellspacing="0" border="1">
    <thead>
    <tr bgcolor="yellow">
        <td colspan="6"><?php echo Lang::get('pokerData.jingji'); ?></td>
    </tr>
    <tr>
        <td><?php echo Lang::get('pokerData.date'); ?></td>
        <td><?php echo Lang::get('pokerData.choumazongfafang'); ?></td>
        <td><?php echo Lang::get('pokerData.choumazongxiaohao'); ?></td>
        <td><?php echo Lang::get('pokerData.choumazongliang'); ?></td>
        <td><?php echo Lang::get('pokerData.renzhunchouma'); ?></td>
        <td><?php echo Lang::get('pokerData.huoyueyonghuchoumazongliang'); ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($table_jingji as $day_jingji) { ?>
        <tr>
            <td><?php echo $day_jingji['date']; ?></td>
            <td><?php echo $day_jingji['all_chip_fafang']; ?></td>
            <td><?php echo $day_jingji['all_chip_xiaohao']; ?></td>
            <td><?php echo $day_jingji['allChip']; ?></td>
            <td><?php echo $day_jingji['averageChip']; ?></td>
            <td><?php echo $day_jingji['activeChip']; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br/>


<?php
    $name2name = array(
            'endOneRound' => 'endOneRound',
            'playSlot' => 'playSlot',
            'betRedBlackCard' => 'betRedBlackCard',
            'betLuckyCard' => 'settleLuckyCardReward',
            'betLuckyPool' => 'settleLuckyPoolReward',
            'saveChipsToStrongBox|saveChipsToStrongBox' => 'getChipsFromStrongBox|getChipsFromStrongBox',
            'startSpinAndGo' => 'matchSpinRank|matchRank',
            'startSitAndGo' => 'matchSitRank|matchRank',
            'regMU' => 'matchMURank|matchRank',
            'xiaohaozongji' => 'fafangzongji',
    );

    $namelessthan0 = array('lateJoinMU', 'standUp', 'buyUseChips|buyItemByID', 'renewGift', 'buyUseGold|buyItemByID', 'deductChips|deductChips',
                            'deductChips', 'buyIn', 'endOneRound', 'playSlot', 'betRedBlackCard', 'betLuckyCard', 'betLuckyPool', 'saveChipsToStrongBox|saveChipsToStrongBox',
                            'startSpinAndGo', 'startSitAndGo', 'regMU', 'xiaohaozongji', 'recycle');

    $namebiggerthan0 = array('startMU|startMUByTime','playTurnTable', 'getTimeBoxReward', 'playTokenTurnTable', 'getNoviceTaskReward',
                            'getLevelReward|getUpLevel', 'takeSpree', 'playerReward|getDailyLoginReward', 'setNoviceTutorialReward', 'getLivenessReward',
                            'getSigninReward|signin', 'getSigninReward|getOneWeekSigninReward', 'getPlayerCardReward', 'get7DayReward',
                            'getDailyLoginReward', 'playFavoriteTurntable', 'useItemCard|usePlayerItemByPos', 'giveReward', 'getBustReward',
                            'getTreasureBowlReward', 'recharge', 'rechargeChips', 'playerReward|getPlayerCardDoubleReward', 'useGem|useItemBysubTypeAction',
                            'playerReward|getFirstRechargeReward', 'getDailyGemChips', 'exchangeChips', 'takeZidingyiSpree',
                            'playerReward|getNextDayReward', 'getGoldEggReward', 'addDailyInvitedTime|addDailyInviteTime', 'createPlayer',
                            'usePlayerVipCard|useItemBysubTypeAction', 'playerReward|getCompleteAllTaskReward', 'getReward', 'getScratchReward|scratchAction',
                            'giveChips', 'regBack|fire', 'randLivenessLuckyPlayer', 'exchangeDropLetters', 'playART', 'endOneRound',
                            'playSlot', 'betRedBlackCard', 'settleLuckyCardReward', 'settleLuckyPoolReward', 'getChipsFromStrongBox|getChipsFromStrongBox', 
                            'matchSpinRank|matchRank', 'matchSitRank|matchRank', 'matchMURank|matchRank', 'getNoviceTaskReward', 'fafangzongji');
    foreach ($table_mingxi as $day_mingxi) {
        $already_show = array(); //用来保存收入中已经在对应消耗时展示过的数据的$key,显示每一天的新的数据前清空
     ?>
    <table cellpadding="10" cellspacing="0" border="1">
        <thead>
        <tr bgcolor="green">
            <td colspan="6"><?php echo Lang::get('pokerData.choumajingji'); ?></td>
        </tr>
        <tr bgcolor="green">
            <td colspan="6"><?php echo $day_mingxi['date'] ?></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?php echo Lang::get('pokerData.youxibixiaohao'); ?></td>
            <td>消耗值</td>
            <td><?php echo Lang::get('pokerData.youxibifafang'); ?></td>
            <td>发放值</td>
            <td>总和</td>
            <td>回收比</td>
        </tr>
        <?php foreach ($day_mingxi['xiaohao'] as $key => $value) { ?>
            <tr>
                <?php if(in_array($key, $namelessthan0)){ ?>
                    <td><?php { 
                        echo Lang::get('pokerData.' . $key);
                         ?>消耗</td>
                    <td><?php 
                        echo $value;
                        } ?></td>
                <?php }else{ ?>
                    <td bgcolor="yellow"><?php { 
                        echo Lang::get('pokerData.' . $key);
                         ?>-异常或未分类</td>
                    <td bgcolor="yellow"><?php 
                        echo $value;
                        } ?></td>                
                <?php } ?>
                <?php if((isset($name2name[$key])) && (isset($day_mingxi['fafang'][$name2name[$key]]))){
                        $tokey = $name2name[$key];
                        $already_show[] = $tokey;
                 ?>
                    <td><?php { 
                        echo Lang::get('pokerData.' . $tokey);
                         ?>发放</td>
                    <td><?php 
                        echo $day_mingxi['fafang'][$tokey];
                        ?></td>
                    <td>
                        <?php 
                        echo ($day_mingxi['fafang'][$tokey] + $value);
                        } ?>
                    </td>
                    <?php if(($day_mingxi['fafang'][$tokey] + $value) <= 0){
                        $rate = round(($day_mingxi['fafang'][$tokey] + $value)/$value*100, 2).'%';
                     ?>
                        <td bgcolor="green">
                            <?php 
                            echo $rate;
                            ?>
                        </td>
                    <?php }else{
                        $rate = round(($day_mingxi['fafang'][$tokey] + $value)/$value*100, 2).'%';
                     ?>
                        <td bgcolor="red">
                            <?php 
                            echo $rate;
                            ?>
                        </td>
                    <?php } ?>
                <?php }else{ ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php } ?>
            </tr>
        <?php } ?>
        <?php foreach ($day_mingxi['fafang'] as $key => $value) {
                if(!in_array($key, $already_show)){
         ?>
            <tr>
                <td></td>
                <td></td>
                <?php if(in_array($key, $namebiggerthan0)){ ?>
                    <td><?php {
                        echo Lang::get('pokerData.' . $key);
                         ?>发放</td>
                    <td><?php 
                        echo $value;
                        } ?></td>
                <?php }else{ ?>
                    <td bgcolor="yellow"><?php {
                        echo Lang::get('pokerData.' . $key);
                         ?>-异常或未分类</td>
                    <td bgcolor="yellow"><?php 
                        echo $value;
                        } ?></td>
                <?php } ?>
                <td></td>
                <td></td>
            </tr>
        <?php }
        } ?>
        </tbody>
    </table>
    <br/>
<?php 
    unset($already_show);
} ?>



<br/>

<p><?php echo $warning_info; ?></p>
</body>
</html>