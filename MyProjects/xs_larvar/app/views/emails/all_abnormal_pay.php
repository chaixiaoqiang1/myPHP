<html>
<body>

<table cellpadding="10" cellspacing="0" border="1">
    <thead>
    <tr bgcolor="green">
        <td colspan="3"><?php echo '使用代充IP充值用户'; ?></td>
    </tr>
    <tr>
        <td><?php echo 'IP'; ?></td>
        <td><?php echo Lang::get('slave.pay_user_num'); ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($ip_data as $v) { ?>
        <tr>
            <td><?php echo $v->pay_ip; ?></td>
            <td><?php echo $v->num; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br/>
<table cellpadding="10" cellspacing="0" border="1">
    <thead>
    <tr bgcolor="green">
        <td colspan="3"><?php echo '使用多个IP充值用户'; ?></td>
    </tr>
    <tr>
        <td><?php echo 'UID'; ?></td>
        <td><?php echo Lang::get('slave.pay_ip_num'); ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($user_data as $v) { ?>
        <tr>
            <td><?php echo $v->pay_user_id; ?></td>
            <td><?php echo $v->num; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br/><br/><br/>
</body>
</html>