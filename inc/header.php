<?php
$token = md5(uniqid(microtime(), true));
$_SESSION['token'] = $token;
?>
<!-- BEGIN HEADER-->
	<header id="header" >
   	<div class="headerbar">

      <div class="headerbar-left">
        <ul class="header-nav header-nav-options">
          <li class="header-nav-brand" >
            <div class="brand-holder">
              <a href="<?php echo BASEURL?>home.php">
                <span class="text-lg text-bold text-primary">DTE CHILE</span>
              </a>
            </div>
          </li>
          <li>
            <a class="btn btn-icon-toggle menubar-toggle" data-toggle="menubar" href="javascript:void(0);">
              <i class="fa fa-bars"></i>
            </a>
          </li>
        </ul>
      </div>

      <div class="headerbar-right">
        <ul class="header-nav header-nav-options">
          <li>
            <form class="navbar-search" role="search">
              <div class="form-group">
                <input type="text" class="form-control" name="headerSearch" placeholder="Buscador de contenidos">
              </div>
              <button type="submit" class="btn btn-icon-toggle ink-reaction"><i class="fa fa-search"></i></button>
            </form>
          </li>
        </ul>
        <ul class="header-nav header-nav-profile">
          <li class="dropdown">
            <a href="javascript:void(0);" class="dropdown-toggle ink-reaction" data-toggle="dropdown">
              <img src="<?php echo $_SESSION['foto_usuario']?>" alt="<?php echo $_SESSION['nombre_usuario']?>" />
              <span class="profile-info">
                <?php echo $_SESSION['nombre_usuario']?>
                <small><?php echo $_SESSION['cargo_usuario']?></small>
              </span>
            </a>
            <ul class="dropdown-menu animation-dock">
              <li class="dropdown-header">Configuar</li>
              <li><a href="<?php echo BASEURL?>profile.php">Mi perfil</a></li>
              <li class="divider"></li>
              <li><a href="<?php echo BASEURL?>logout.php"><i class="fa fa-fw fa-power-off text-danger"></i> Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>

    </div>
  </header>