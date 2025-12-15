<?php 
include '../Includes/dbcon.php';
include '../Includes/studentSession.php';

$studentId = intval($_SESSION['studentId']);
$admissionNumber = mysqli_real_escape_string($conn, $_SESSION['admissionNumber']);

function tableExists($conn, $tableName) {
    $tableName = mysqli_real_escape_string($conn, $tableName);
    $result = $conn->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$tableName' LIMIT 1");
    return $result && $result->num_rows > 0;
}

$courseQuery = "SELECT tblclass.Id AS classId,tblclass.className,tblclassarms.Id AS classArmId,
tblclassarms.classArmName,CONCAT(IFNULL(tblclassteacher.firstName,''),' ',IFNULL(tblclassteacher.lastName,'')) AS teacherName
FROM tblstudents
INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
LEFT JOIN tblclassteacher ON tblclassteacher.classId = tblclass.Id AND tblclassteacher.classArmId = tblclassarms.Id
WHERE tblstudents.Id = '$studentId'";
$courseResult = $conn->query($courseQuery);
$courses = [];
if($courseResult && $courseResult->num_rows > 0){
    while($row = $courseResult->fetch_assoc()){
        $courses[] = $row;
    }
}

$coursesCount = count($courses);

$totalAttendance = 0;
$presentCount = 0;
$absentCount = 0;

$attQuery = $conn->query("SELECT 
SUM(1) AS total,
SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) AS presentCount,
SUM(CASE WHEN status = '0' THEN 1 ELSE 0 END) AS absentCount
FROM tblattendance WHERE admissionNo = '$admissionNumber'");

if($attQuery && $attQuery->num_rows > 0){
    $attRow = $attQuery->fetch_assoc();
    $totalAttendance = intval($attRow['total']);
    $presentCount = intval($attRow['presentCount']);
    $absentCount = intval($attRow['absentCount']);
}

$pendingJustifications = 0;
if(tableExists($conn, 'tblattendancejustifications')){
    $pendingResult = $conn->query("SELECT COUNT(*) AS totalPending FROM tblattendancejustifications WHERE studentId = '$studentId' AND status = 'Pending'");
    if($pendingResult){
        $pendingJustifications = intval($pendingResult->fetch_assoc()['totalPending']);
    }
}

$sessionQuery = $conn->query("SELECT sessionName, termId FROM tblsessionterm WHERE isActive = '1' LIMIT 1");
$activeSession = $sessionQuery && $sessionQuery->num_rows > 0 ? $sessionQuery->fetch_assoc() : null;
$termName = "";
if($activeSession){
    $termId = intval($activeSession['termId']);
    $termQuery = $conn->query("SELECT termName FROM tblterm WHERE Id = '$termId'");
    if($termQuery && $termQuery->num_rows > 0){
        $termRow = $termQuery->fetch_assoc();
        $termName = $termRow['termName'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="../img/logo/attnlg.jpg" rel="icon">
  <?php include "Includes/title.php";?>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="../css/ruang-admin.min.css" rel="stylesheet">
  <link href="../css/custom-styles.css" rel="stylesheet">
</head>

<body id="page-top">
  <div id="wrapper">
    <!-- Navbar -->
    <?php include "Includes/navbar.php";?>
    <!-- Navbar -->
    <div id="content-wrapper" class="d-flex flex-column">
      <div id="content">
        <div class="container-fluid" id="container-wrapper">
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Espace étudiant</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
          </div>

          <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Cours inscrits</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $coursesCount;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-book-open fa-2x text-primary"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Présences enregistrées</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $totalAttendance;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-calendar-check fa-2x text-success"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Présent</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $presentCount;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-user-check fa-2x text-info"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
              <div class="card h-100">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                      <div class="text-xs font-weight-bold text-uppercase mb-1">Absences</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $absentCount;?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-user-times fa-2x text-danger"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Mes cours</h6>
                  <?php if($activeSession){ ?>
                    <span class="text-muted small">Session active : <?php echo $activeSession['sessionName'].' - '.$termName;?></span>
                  <?php } ?>
                </div>
                <div class="card-body">
                  <?php if($coursesCount == 0){ ?>
                    <div class="alert alert-warning mb-0">Aucun cours associé pour le moment.</div>
                  <?php } else { ?>
                    <div class="row">
                      <?php foreach($courses as $course){ ?>
                        <div class="col-lg-4 mb-4">
                          <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                              <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                  <div class="font-weight-bold text-primary text-uppercase mb-1"><?php echo $course['className'];?> - <?php echo $course['classArmName'];?></div>
                                  <div class="mb-2 text-muted small">Enseignant : <?php echo trim($course['teacherName']) !== '' ? $course['teacherName'] : 'Non assigné';?></div>
                                  <a href="attendance.php?courseKey=<?php echo $course['classId'].'|'.$course['classArmId'];?>" class="btn btn-sm btn-outline-primary">Consulter la présence</a>
                                </div>
                                <div class="col-auto">
                                  <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-6">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Justifications en attente</h6>
                </div>
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="mr-3">
                      <i class="fas fa-comments fa-2x text-warning"></i>
                    </div>
                    <div>
                      <h4 class="font-weight-bold mb-0"><?php echo $pendingJustifications;?></h4>
                      <span class="text-muted small">Soumissions en cours de traitement</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
      <?php include "Includes/footer.php";?>
    </div>
  </div>

  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <script src="../vendor/jquery/jquery.min.js"></script>
  <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
  <script src="../js/ruang-admin.min.js"></script>
</body>

</html>

