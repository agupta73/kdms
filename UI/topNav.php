<!-- Header section visible on top of every page -->
<nav class="navbar navbar-expand-lg bg-primary kdms-top-nav">
  <div class="container">
    <a class="navbar-brand event-title" align="center">
        <div class="tim-typo">
            <h2> <b><? echo $_SESSION['eventDesc']; ?> </b></h2>
        </div>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
  <span class="navbar-toggler-bar navbar-kebab"></span>
  <span class="navbar-toggler-bar navbar-kebab"></span>
  <span class="navbar-toggler-bar navbar-kebab"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavDropdown" >
    <ul class="navbar-nav kdms-account">
        <li class="nav-item account-user">
          <? echo $_SESSION['UserName'], " - ", $_SESSION['Role']; ?>

      </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle account-logo" href="#" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="material-icons">account_circle</i>
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
            <a class="dropdown-item" href="login.php">Logout</a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
