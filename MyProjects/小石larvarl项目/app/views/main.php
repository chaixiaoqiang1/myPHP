<!DOCTYPE html>
<html ng-app="eastblueApp">
    <head>
        <meta charset="UTF-8">
		<title><?php echo Lang::get('basic.site_name'); ?></title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
		<!--
        <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="/css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <link href="/css/adminlte.css" rel="stylesheet" type="text/css" />
		<link href="/css/loading-bar.min.css" rel="stylesheet" type="text/css" />
		<link href="/css/ng-animation.css" rel="stylesheet" type="text/css" />
		-->
        <link href="/css/bootstrap-and-other.css?t=1" rel="stylesheet" type="text/css" />
        <link href="/css/eastblue.css?t-1" rel="stylesheet" type="text/css" />
		<!--
		<script src="/js/angular.min.js"></script>
		<script src="/js/loading-bar.min.js"></script>
		<script src="/js/angular-animate.min.js"></script>
		<script src="/js/jquery.min.js"></script>
		<script src="/js/ui-bootstrap.min.js"></script>
		<script src="/js/ui-bootstrap-tpls.min.js"></script>
		-->
		<script src="/js/angular.bootstrap.jquery.js?t=1"></script>
		<script src="/js/services.js?t=2"></script>
		<script src="/js/controller.js?t=5"></script>
		<script src="/js/eastblue.js"></script>
		<script src="/js/Chart.js"></script>
    </head>
    <body class="skin-blue">
        <!-- header logo: style can be found in header.less -->
        <header class="header">
            <a href="/" class="logo">
                <!-- Add the class icon to your logo image or logo icon to add the margining -->
				<?php echo Lang::get('basic.site_name') ?>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>
                <div class="navbar-right">
					<ul class="nav navbar-nav">
						<li class="dropdown" id="game-menu-dropdown">
							<a class="dropdown-toggle" id="menu-game">
								<i class="glyphicon glyphicon-globe"></i>
								<span><?php echo Lang::get('basic.change_platform') ?></span>
							</a>
							<dl class="dropdown-menu well game-menu">
								<?php foreach(GameCode::all() as $vv) { ?>
								<dt><?php echo $vv->game_name?></dt>
								<?php foreach (Game::userGames()->where('game_code', $vv->game_code)->get() as $v) { ?>
								<dd><a href="/games/<?php echo $v->game_id?>"><?php echo $v->game_name . '-' . $v->platform->platform_name?></a></dd>
								<?php } ?>
								<?php } ?>
							</dl>	
						</li>
						<li class="dropdown user user-menu">
							<a class="dropdown-toggle">
								<i class="glyphicon glyphicon-user"></i>
								<span><?php echo Auth::user()->username ?><i class="caret"></i></span>
							</a>
							<ul class="dropdown-menu">
								<!-- User image -->                                
								<li class="user-header bg-light-blue">             
									<img src="/img/avatar3.png" class="img-circle" alt="User Image" />
									<p>                                            
										<?php echo Auth::user()->username;?> - <?php echo Auth::user()->department->department_name ?>  
										<small><?php echo Auth::user()->created_at ?></small>
								  </p>                                           
							  	</li>                                              
                                <li class="user-body">  
                                	<div class="pull-left">                     
										<a href="/change/language" class="btn btn-default btn-flat"><?php echo Lang::get('user.language')?></a>
								  	</div>                            
							  	</li>                                              
							  	<li class="user-footer">                           
									<div class="pull-left">                     
										<a href="/users/<?php echo Auth::user()->user_id ?>/edit" class="btn btn-default btn-flat"><?php echo Lang::get('user.profile')?></a>
								  	</div>                                         
								  	<div class="pull-right">                       
								  		<a href="/logout" class="btn btn-default btn-flat"><?php echo Lang::get('user.logout') ?></a>
								  	</div>                                      
							  	</li>                   
							</ul>
						</li>
					</ul>
                </div>
            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">
            <!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas eb-left-side-width">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="/img/avatar3.png" class="img-circle" alt="User Image" />
                        </div>
                        <div class="pull-left info">
							<p><?php echo Auth::user()->username; ?></p>
							<p><?php echo Auth::user()->department->department_name ?> </p>
                        </div>
                    </div>
					<?php echo $sidebar ?>
                </section>
                <!-- /.sidebar -->
            </aside>

            <!-- Right side column. Contains the navbar and content of the page -->
            <aside class="right-side eb-right-side-offset">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
						<?php echo $app_name; ?>
						<small></small>
                    </h1>
					<ol class="breadcrumb">                                     
						<li>
							<?php if ($platform) { ?>
							<i class="fa fa-flag-checkered"></i>
							<?php echo $platform->platform_name?>
							(<?php echo $platform->region->region_name ?>)
							 -
							<?php } ?>
						 	<?php if ($game) echo $game->game_name;?>
						</li>
                     </ol>  
                </section>

                <!-- Main content -->
                <section class="content">
					<?php echo $content ?>
                </section><!-- /.content -->
				<?php echo View::make('subview.footer') ?>
            </aside><!-- /.right-side -->
        </div><!-- ./wrapper -->
    </body>
</html>