<!-- sidebar menu: : style can be found in sidebar.less -->
<ul class="sidebar-menu">
	<?php foreach ($groups as $k => $v) { ?>
		<?php if (empty($v->child)) continue;?> 
	<li class="treeview <?php if ($gid == $v->group_id) {?>active<?php } ?>">
			<a href="#">
				<i class="fa fa-dashboard"></i>
				<span><?php echo $v->group_name; ?></span>
				<i class="fa fa-angle-left pull-right"></i>
			</a>
			<?php if (!empty($v->child)) { ?>
			<ul class="treeview-menu">
				<?php foreach ($v->child as $kk => $vv) { ?>
				<li <?php if (Request::is($vv->app_key)) {?>class="active"<?php } ?>>
					<a href="<?php echo url($vv->app_key).'?gid='.$v->group_id; ?>"><i class="fa fa-angle-double-right"></i> <?php echo $vv->app_name; ?></a>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>
		</li>
	<?php } ?>
</ul>