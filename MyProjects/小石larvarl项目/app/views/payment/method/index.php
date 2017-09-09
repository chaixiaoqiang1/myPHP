
<div class="col-xs-12">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php if ($platform) { ?>
                <i class="fa fa-flag-checkered"></i>
                            <?php echo $platform->platform_name?>
                            (<?php echo $platform->region->region_name ?>)
                             -
                            <?php } ?>
                            <?php if ($game) echo $game->game_name.'——————';?>
                            <?php echo Lang::get('type.platform_pay_type')?>
                        </li>
            </h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <td><?php echo Lang::get('payment.pay_id') ?></td>
                        <td><?php echo Lang::get('payment.pay_type_id') ?></td>
                        <td><?php echo Lang::get('payment.method_id') ?></td>  
                        <td><?php echo Lang::get('payment.method_name') ?></td>
                        <td><?php echo Lang::get('payment.method_description') ?></td>
                        <td><?php echo Lang::get('payment.is_selected') ?></td>
                        <td><?php echo Lang::get('payment.is_recommend') ?></td>
                        <td><?php echo Lang::get('payment.method_order') ?></td>    
                        <td><?php echo Lang::get('payment.post_url') ?></td>        
                        <td><?php echo Lang::get('payment.html_name') ?></td>
                        <td><?php echo Lang::get('payment.class_name') ?></td>
                        <td><?php echo Lang::get('payment.is_use') ?></td>
                        <td><?php echo Lang::get('payment.currency_id') ?></td>
                        <td><?php echo Lang::get('payment.zone') ?></td>
                        <td><?php echo Lang::get('payment.domain_name') ?></td>
                        <td><?php echo Lang::get('payment.start_time') ?></td>
                        <td><?php echo Lang::get('payment.end_time') ?></td>
                        <td><?php echo Lang::get('payment.huodong_rate') ?></td>
                           
                    </tr>
                </thead>
                <tbody>

                <?php foreach ($platform_pay_methods as $k => $v) { ?>
                    <tr>
                        <td><?php echo $v->id ?></td>
                        <td><?php echo $v->pay_type_id ?></td>
                         <td><?php echo $v->method_id?></td>
                        <td><?php echo $v->method_name ?></td>
                        <td><?php echo $v->method_description ?></td>
                        <td><?php echo $v->is_selected ?></td>
                        <td><?php echo $v->is_recommend?></td>
                        <td><?php echo $v->method_order?></td>            
                        <td><?php echo $v->post_url?></td>
                        <td><?php echo $v->html_name?></td>
                        <td><?php echo $v->class_name?></td>
                        <td><?php echo $v->is_use?></td>
                        <td><?php echo $v->currency?></td>
                        <td><?php echo $v->zone?></td>
                        <td><?php echo $v->domain_name?></td>
                        <td><?php echo $v->start_time?></td>
                        <td><?php echo $v->end_time?></td>
                        <td><?php echo $v->huodong_rate?></td>
                    </tr>
                <?php } ?>  
        </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title">
                <?php echo Lang::get('type.eastblue_pay_type')?>
                </h3>
        </div>
        <div class="panel-body">
            <table class="table table-striped">
        <thead>
            <tr>
                    <td><?php echo Lang::get('payment.pay_id') ?></td>
                    <td><?php echo Lang::get('payment.platform_method_id') ?></td>
                    <td><?php echo Lang::get('payment.pay_type_id') ?></td>
                    <td><?php echo Lang::get('payment.method_id') ?></td>  
                    <td><?php echo Lang::get('payment.method_name') ?></td>
                    <td><?php echo Lang::get('payment.method_description') ?></td>
                    <td><?php echo Lang::get('payment.is_selected') ?></td>
                    <td><?php echo Lang::get('payment.is_recommend') ?></td>
                    <td><?php echo Lang::get('payment.method_order') ?></td>    
                    <td><?php echo Lang::get('payment.post_url') ?></td>        
                    <td><?php echo Lang::get('payment.html_name') ?></td>
                    <td><?php echo Lang::get('payment.class_name') ?></td>
                    <td><?php echo Lang::get('payment.is_use') ?></td>
                    <td><?php echo Lang::get('payment.currency_id') ?></td>
                    <td><?php echo Lang::get('payment.zone') ?></td>
                    <td><?php echo Lang::get('payment.domain_name')?></td>
                    <td><?php echo Lang::get('payment.start_time') ?></td>
                    <td><?php echo Lang::get('payment.end_time') ?></td>
                    <td><?php echo Lang::get('payment.huodong_rate') ?></td>  
                                
        </tr>
        </thead>
        <tbody>

    <?php 
        if ($platform->platform_id == 1) {
            $payment = Payment::whereIn("domain_name", array(1,2))->orderBy('pay_id','asc')->paginate(100);
        } else {
            $payment = Payment::where("platform_id", "=", $platform->platform_id)->orderBy('pay_id','asc')->paginate(100);
        }
        foreach ($payment as $k => $v) { ?>
        <tr>
            <td><?php echo $v->pay_id ?></td>
            <td><?php echo $v->platform_method_id?></td>
            <td><?php echo $v->pay_type_id ?></td>
            <td><?php echo $v->method_id?></td>
            <td><a href="/payment/<?php echo $v->pay_id ?>/edit"><?php echo $v->method_name ?></a></td>
            <td><?php echo $v->method_description ?></td>
            <td><?php echo $v->is_selected ?></td>
            <td><?php echo $v->is_recommend?></td>
            <td><?php echo $v->method_order?></td>            
            <td><?php echo $v->post_url?></td>
            <td><?php echo $v->html_name?></td>
            <td><?php echo $v->class_name?></td>
            <td><?php echo $v->is_use?></td>
            <td><?php echo $v->currency_id?></td>
            <td><?php echo $v->zone?></td>
            <td><?php echo $v->domain_name?></td>
            <td><?php echo $v->start_time?></td>
            <td><?php echo $v->end_time?></td>
            <td><?php echo $v->huodong_rate?></td>
        </tr>
    <?php } ?>  
        </tbody>
    </table>
        </div>
    </div>
</div>
