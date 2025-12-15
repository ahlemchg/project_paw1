<?php 
  $studentQuery = "SELECT firstName,lastName FROM tblstudents WHERE Id = ".$_SESSION['studentId']."";
  $rsStudent = $conn->query($studentQuery);
  $student = $rsStudent->fetch_assoc();
  $fullName = $student ? $student['firstName']." ".$student['lastName'] : "Etudiant";
?>
<nav class="navbar navbar-expand navbar-light bg-gradient-primary topbar mb-4 static-top">
          <ul class="navbar-nav ml-auto">
            <div class="topbar-divider d-none d-sm-block"></div>
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <img class="img-profile rounded-circle" src="../img/user-icn.png" style="max-width: 60px">
                <span class="ml-2 d-none d-lg-inline text-white small"><b><?php echo $fullName;?></b></span>
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
        </nav>

