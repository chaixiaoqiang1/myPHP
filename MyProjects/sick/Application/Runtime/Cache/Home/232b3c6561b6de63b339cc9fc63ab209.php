<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>

<html lang="zh-CN">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=0">

    <title>图文咨询设置</title>

    <link href="/public_html/Public/sick/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="/public_html/Public/sick/css/Free_consultation_fit.css"/>
    <link rel="stylesheet" href="/public_html/Public/sick/css/lc_switch.css"/>
    <style>
        .lcs_switch{
            height: 25px;
        }
        .lcs_cursor{
            width: 18px;
            height: 18px;
        }
    </style>
</head>

<body>
<div class="container">
    <form action="/public_html/index.php/Home/Doctor/img_set_up/type/1.html" method="post">

    <div class="row">
        <!--<div class="col-md-12 col-sm-12 col-xs-12 br">-->
            <!--<div class="text fl">-->
                <!--<div class="text_left lf">图文咨询</div>-->
                <!--<div class="text_right rt">-->
                    <!--<p>-->
                        <!--<input type="checkbox" name="check-1" value="4" class="lcs_check" autocomplete="off" />-->
                    <!--</p>-->
                <!--</div>-->
            <!--</div>-->
        <!--</div>-->
        <div class="col-md-12 col-sm-12 col-xs-12 money">
            <div class="text fl">
                <div class="text_left lf">请选择价格</div>
                <div class="text_right rt">
                    <input type="text" class="select" style="text-align: right" placeholder="请输入价格" name="price" value="<?php echo ($info["price"]); ?>"/>元
                   <!-- <select name="" class="select">
                    </select>-->
                </div>
            </div>
        </div>
      <!--  <div class="col-md-12 col-sm-12 col-xs-12 br">
            <div class="text fl">
                <div class="text_left lf">复诊劵</div>
                <div class="text_right rt">
                    <p><?php if($info['is_ticket'] == 1): ?><input id="off" type="checkbox" class="lcs_check"  autocomplete="off"  name="is_ticket" checked value="1"/>
                        <?php else: ?>
                        <input id="off" type="checkbox" class="lcs_check"  autocomplete="off"  name="is_ticket"  value="2"/><?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 money">
            <div class="text fl">
                <div class="text_left lf">请设置折扣</div>
                <div class="text_right rt">
                    <input type="text" class="select" style="text-align: right" placeholder="请输入折扣率" name="ticketdiscount" value="<?php echo ($info["ticketdiscount"]); ?>"/>折
                        &lt;!&ndash;   <select name="" class="select">
                       </select>&ndash;&gt;
                </div>
            </div>
        </div>-->
    </div>
     </form>
    <button class="button" id="tijiao">保存</button>
</div>
<script src="/public_html/Public/sick/js/jquery-1.11.3.js"></script>

<script src="/public_html/Public/sick/js/bootstrap.js"></script>
<script src="/public_html/Public/sick/js/lc_switch.min.js"></script>
<script>
    $(document).ready(function(e) {
        console.log($("input[type='checkbox']").is(':checked'))
        $('input').lc_switch();
        // triggered each time a field changes status
        $('body').delegate('.lcs_check', 'lcs-statuschange', function() {
            var status = ($(this).is(':checked')) ? 'checked' : 'unchecked';
        });

        // triggered each time a field is checked
        $('body').delegate('.lcs_check', 'lcs-on', function() {
           $('#off').val(1);
           // console.log('field is checked');
    });
    // triggered each time a is unchecked
        $('body').delegate('.lcs_check', 'lcs-off', function() {
            $('#off').val(2);
          //  console.log('field is unchecked');
        });
        $(".text").click(function(){
            $(this).find(".select").focus();
        })
        $('#tijiao').click(function(){
            $('form:first').submit();
        })
    });
</script>

</body>

</html>