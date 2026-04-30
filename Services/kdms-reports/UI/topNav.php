<!-- Header section visible on top of every page -->

<nav class="navbar navbar-main navbar-expand-lg px-0 border-radius-xl shadow-none kdms-top-navbar" id="navbarBlur" data-scroll="true">
  
  <div class="container-fluid py-1 px-3 kdms-top-nav">
    <div class="kdms-active-event">
      <h3><?php echo $_SESSION['eventDesc']; ?></h3>
    </div>
    <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top kdms-toggle-navbar">
      <div class="container-fluid">
        <div class="navbar-wrapper">
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
          <span class="sr-only">Toggle navigation</span>
          <span class="navbar-toggler-icon icon-bar"></span>
          <span class="navbar-toggler-icon icon-bar"></span>
          <span class="navbar-toggler-icon icon-bar"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end">
        </div>
      </div>
    </nav>
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
      <ul class="navbar-nav  justify-content-end kdms-navbar-items">
        <li class="nav-item d-flex align-items-center kdms-user-role">
          <h6 class="font-weight-bolder mb-0 kdms-user"><?php echo $_SESSION['UserName'], " - ", $_SESSION['Role']; ?></h6>
        </li>
        <li class="nav-item d-flex align-items-center">
          <a href="login.php" class="nav-link font-weight-bold px-0 text-body">
            <i class="material-icons">person</i>
            <span class="d-sm-inline d-none">Sign Out</span>
          </a>
        <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
          <a href="#" class="nav-link p-0 text-body" id="iconNavbarSidenav">
            <div class="sidenav-toggler-inner">
              <i class="sidenav-toggler-line"></i>
              <i class="sidenav-toggler-line"></i>
              <i class="sidenav-toggler-line"></i>
            </div>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
