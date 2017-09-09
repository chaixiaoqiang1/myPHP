<html>
<body>

<table cellpadding="10" cellspacing="0" border="1">
    <thead>
    <tr bgcolor="green">
        <td colspan="12"><?php echo 'Registry Report'; ?></td>
    </tr>
    <tr>
        <td><?php echo 'Game'; ?></td>
        <td><?php echo 'Reg Date'; ?></td>
        <td><?php echo 'Source'; ?></td>
        <td><?php echo 'U1'; ?></td>
        <td><?php echo 'U2'; ?></td>
        <td><?php echo 'Reg Formal'; ?></td>
        <td><?php echo 'Reg Anonymous'; ?></td>
        <td><?php echo 'Reg Anonymous LevelUp'; ?></td>
        <td><?php echo 'Create Formal'; ?></td>
        <td><?php echo 'Create Anonymous'; ?></td>
        <td><?php echo 'Create Formal 10level'; ?></td>
        <td><?php echo 'Create Anonymous 10level'; ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($reg as $table_reg) { ?>
        <tr>
            <td><?php echo $table_reg['game']; ?></td>
            <td><?php echo $table_reg['reg_date']; ?></td>
            <td><?php echo $table_reg['source']; ?></td>
            <td><?php echo $table_reg['u1']; ?></td>
            <td><?php echo $table_reg['u2']; ?></td>
            <td><?php echo $table_reg['reg_formal']; ?></td>
            <td><?php echo $table_reg['reg_anonymous']; ?></td>
            <td><?php echo $table_reg['reg_lvlup']; ?></td>
            <td><?php echo $table_reg['create_formal']; ?></td>
            <td><?php echo $table_reg['create_anonymous']; ?></td>
            <td><?php echo $table_reg['create_formal_10']; ?></td>
            <td><?php echo $table_reg['create_anonymous_10']; ?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>
<br/>
<table cellpadding="10" cellspacing="0" border="1">
    <thead>
    <tr bgcolor="red">
        <td colspan="9"><?php echo 'Recharge Report'; ?></td>
    </tr>
    <tr>
        <td><?php echo 'Game'; ?></td>
        <td><?php echo 'Reg Date'; ?></td>
        <td><?php echo 'Source'; ?></td>
        <td><?php echo 'U1'; ?></td>
        <td><?php echo 'U2'; ?></td>
        <td><?php echo 'Recharge Date'; ?></td>
        <td><?php echo 'Recharge Number'; ?></td>
        <td><?php echo 'Recharge Amount'; ?></td>
        <td><?php echo 'Recharge Amount($)'; ?></td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($rec as $table_rec) { ?>
        <tr>
            <td><?php echo $table_rec['game']; ?></td>
            <td><?php echo $table_rec['reg_date']; ?></td>
            <td><?php echo $table_rec['source']; ?></td>
            <td><?php echo $table_rec['u1']; ?></td>
            <td><?php echo $table_rec['u2']; ?></td>
            <td><?php echo $table_rec['rec_date']; ?></td>
            <td><?php echo $table_rec['rec_num']; ?></td>
            <td><?php echo $table_rec['rec_amount']; ?></td>
            <td><?php echo '$' . $table_rec['rec_dollar']; ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
<br/><br/><br/>

</body>
</html>