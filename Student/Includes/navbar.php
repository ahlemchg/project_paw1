<?php 
  $studentQuery = "SELECT firstName,lastName FROM tblstudents WHERE Id = ".$_SESSION['studentId']."";
  $rsStudent = $conn->query($studentQuery);
  $student = $rsStudent->fetch_assoc();
  $fullName = $student ? $student['firstName']." ".$student['lastName'] : "Etudiant";
?>
<nav class="main-navbar navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="../img/logo/attnlg.jpg" alt="Attendance Management System" class="navbar-logo">
      <div class="d-flex flex-column">
        <span class="navbar-brand-text">Attendance MS</span>
        <small class="navbar-brand-subtitle d-none d-md-inline">Dépt. Informatique - Univ. Alger 1</small>
      </div>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">
            <i class="fas fa-fw fa-home"></i>
            <span>Accueil</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="attendance.php">
            <i class="fa fa-calendar-check"></i>
            <span>Mes présences</span>
          </a>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="img-profile rounded-circle mr-2" src="../img/user-icn.png" alt="User" style="width: 40px; height: 40px;">
            <span class="d-none d-lg-inline"><b><?php echo $fullName;?></b></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="logout.php">
              <i class="fas fa-power-off fa-fw mr-2 text-danger"></i>
              Se déconnecter
            </a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
