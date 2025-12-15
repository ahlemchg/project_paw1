<?php 
  $query = "SELECT * FROM tblclassteacher WHERE Id = ".$_SESSION['userId']."";
  $rs = $conn->query($query);
  $num = $rs->num_rows;
  $rows = $rs->fetch_assoc();
  $fullName = $rows['firstName']." ".$rows['lastName'];
?>
<nav class="main-navbar navbar navbar-expand-lg navbar-light">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="img/logo/attnlg.jpg" alt="Attendance Management System" class="navbar-logo">
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
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="studentsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-graduate"></i>
            <span>Students</span>
          </a>
          <div class="dropdown-menu" aria-labelledby="studentsDropdown">
            <h6 class="dropdown-header">Manage Students</h6>
            <a class="dropdown-item" href="viewStudents.php">View Students</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="attendanceDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-calendar-alt"></i>
            <span>Attendance</span>
          </a>
          <div class="dropdown-menu" aria-labelledby="attendanceDropdown">
            <h6 class="dropdown-header">Manage Attendance</h6>
            <a class="dropdown-item" href="takeAttendance.php">Take Attendance</a>
            <a class="dropdown-item" href="viewAttendance.php">View Class Attendance</a>
            <a class="dropdown-item" href="viewStudentAttendance.php">View Student Attendance</a>
            <a class="dropdown-item" href="downloadRecord.php">Today's Report (xls)</a>
          </div>
        </li>
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
          <a class="nav-link" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-search"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
            <form class="navbar-search">
              <div class="input-group">
                <input type="text" class="form-control" placeholder="What do you want to look for?" aria-label="Search">
                <div class="input-group-append">
                  <button class="btn btn-primary" type="button">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img class="img-profile rounded-circle mr-2" src="img/user-icn.png" alt="User" style="width: 40px; height: 40px;">
            <span class="d-none d-lg-inline"><b><?php echo $fullName;?></b></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="logout.php">
              <i class="fas fa-power-off fa-fw mr-2 text-danger"></i>
              Logout
            </a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>
