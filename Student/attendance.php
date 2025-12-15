<?php 
include '../Includes/dbcon.php';
include '../Includes/studentSession.php';

$studentId = intval($_SESSION['studentId']);
$admissionNumber = mysqli_real_escape_string($conn, $_SESSION['admissionNumber']);
$statusMsg = "";

function tableExists($conn, $tableName) {
    $tableName = mysqli_real_escape_string($conn, $tableName);
    $result = $conn->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '$tableName' LIMIT 1");
    return $result && $result->num_rows > 0;
}

function ensureJustificationTable($conn){
    if(tableExists($conn, 'tblattendancejustifications')){
        return true;
    }
    $createSql = "CREATE TABLE IF NOT EXISTS `tblattendancejustifications` (
        `Id` int(11) NOT NULL AUTO_INCREMENT,
        `attendanceId` int(11) NOT NULL,
        `studentId` int(11) NOT NULL,
        `classId` int(11) NOT NULL,
        `classArmId` int(11) NOT NULL,
        `note` text NOT NULL,
        `status` varchar(20) NOT NULL DEFAULT 'Pending',
        `createdAt` datetime NOT NULL,
        `updatedAt` datetime NOT NULL,
        PRIMARY KEY (`Id`),
        KEY `attendance_idx` (`attendanceId`),
        KEY `student_idx` (`studentId`)
      ) ENGINE=MyISAM DEFAULT CHARSET=latin1;";
    return $conn->query($createSql);
}

$courseQuery = "SELECT tblclass.Id AS classId,tblclass.className,tblclassarms.Id AS classArmId,
tblclassarms.classArmName
FROM tblstudents
INNER JOIN tblclass ON tblclass.Id = tblstudents.classId
INNER JOIN tblclassarms ON tblclassarms.Id = tblstudents.classArmId
WHERE tblstudents.Id = '$studentId'";
$courseResult = $conn->query($courseQuery);
$courses = [];
if($courseResult && $courseResult->num_rows > 0){
    while($row = $courseResult->fetch_assoc()){
        $courses[] = $row;
    }
}

$selectedCourseId = $courses[0]['classId'] ?? 0;
$selectedClassArmId = $courses[0]['classArmId'] ?? 0;
if(isset($_GET['courseKey'])){
    $parts = explode('|', $_GET['courseKey']);
    $selectedCourseId = intval($parts[0]);
    $selectedClassArmId = isset($parts[1]) ? intval($parts[1]) : $selectedClassArmId;
}else{
    if(isset($_GET['courseId'])){
        $selectedCourseId = intval($_GET['courseId']);
    }
    if(isset($_GET['classArmId'])){
        $selectedClassArmId = intval($_GET['classArmId']);
    }
}

if(isset($_POST['submitJustification'])){
    $attendanceId = intval($_POST['attendanceId']);
    $courseId = intval($_POST['courseId']);
    $classArmId = intval($_POST['classArmId']);
    $note = trim($_POST['justificationNote']);

    if($note == ""){
        $statusMsg = "<div class='alert alert-danger'>Merci de préciser votre justification.</div>";
    }else{
        $verifyQuery = $conn->query("SELECT Id FROM tblattendance WHERE Id = '$attendanceId' AND classId = '$courseId' AND classArmId = '$classArmId' AND admissionNo = '$admissionNumber'");
        if($verifyQuery && $verifyQuery->num_rows > 0){
            if(ensureJustificationTable($conn)){
                $noteEscaped = mysqli_real_escape_string($conn, $note);
                $existing = $conn->query("SELECT Id FROM tblattendancejustifications WHERE attendanceId = '$attendanceId' AND studentId = '$studentId'");
                $now = date("Y-m-d H:i:s");
                if($existing && $existing->num_rows > 0){
                    $row = $existing->fetch_assoc();
                    $update = $conn->query("UPDATE tblattendancejustifications SET note = '$noteEscaped', status = 'Pending', updatedAt = '$now' WHERE Id = '".$row['Id']."'");
                    if($update){
                        $statusMsg = "<div class='alert alert-success'>Justification mise à jour et renvoyée.</div>";
                    }else{
                        $statusMsg = "<div class='alert alert-danger'>Une erreur est survenue lors de la mise à jour.</div>";
                    }
                }else{
                    $insert = $conn->query("INSERT INTO tblattendancejustifications(attendanceId,studentId,classId,classArmId,note,status,createdAt,updatedAt) VALUES('$attendanceId','$studentId','$courseId','$classArmId','$noteEscaped','Pending','$now','$now')");
                    if($insert){
                        $statusMsg = "<div class='alert alert-success'>Justification soumise avec succès.</div>";
                    }else{
                        $statusMsg = "<div class='alert alert-danger'>Impossible d'enregistrer la justification.</div>";
                    }
                }
            }else{
                $statusMsg = "<div class='alert alert-danger'>La table des justifications n'a pas pu être créée.</div>";
            }
        }else{
            $statusMsg = "<div class='alert alert-danger'>Vous ne pouvez soumettre qu'une justification pour vos absences.</div>";
        }
    }
}

$attendanceRecords = [];
if($selectedCourseId > 0){
    $attendanceQuery = "SELECT tblattendance.Id AS attendanceId,tblattendance.dateTimeTaken,
    tblattendance.status,tblclass.className,tblclassarms.classArmName,
    justifs.status AS justificationStatus,justifs.note AS justificationNote,justifs.updatedAt
    FROM tblattendance
    INNER JOIN tblclass ON tblclass.Id = tblattendance.classId
    INNER JOIN tblclassarms ON tblclassarms.Id = tblattendance.classArmId
    LEFT JOIN tblattendancejustifications AS justifs ON justifs.attendanceId = tblattendance.Id AND justifs.studentId = '$studentId'
    WHERE tblattendance.admissionNo = '$admissionNumber' AND tblattendance.classId = '$selectedCourseId' AND tblattendance.classArmId = '$selectedClassArmId'
    ORDER BY tblattendance.dateTimeTaken DESC";
    $recordsResult = $conn->query($attendanceQuery);
    if($recordsResult && $recordsResult->num_rows > 0){
        while($row = $recordsResult->fetch_assoc()){
            $attendanceRecords[] = $row;
        }
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
  <title>Attendance Management System - Mes présences</title>
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css">
  <link href="../vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
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
            <h1 class="h3 mb-0 text-gray-800">Mes présences</h1>
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="./">Home</a></li>
              <li class="breadcrumb-item active" aria-current="page">Attendance</li>
            </ol>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Filtrer par cours</h6>
                </div>
                <div class="card-body">
                  <form method="get">
                    <div class="form-row">
                      <div class="col-md-8 mb-3">
                        <label>Cours</label>
                        <select name="courseKey" class="form-control">
                          <?php foreach($courses as $course){ 
                            $value = $course['classId'].'|'.$course['classArmId'];
                            $selected = ($selectedCourseId == $course['classId'] && $selectedClassArmId == $course['classArmId']) ? "selected" : "";
                            ?>
                            <option value="<?php echo $value;?>" <?php echo $selected;?>><?php echo $course['className'].' - '.$course['classArmName'];?></option>
                          <?php } ?>
                        </select>
                        <small class="form-text text-muted">Le choix inclut automatiquement la classe et le bras associés.</small>
                      </div>
                      <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">Actualiser</button>
                      </div>
                    </div>
                  </form>
                  <?php echo $statusMsg; ?>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12">
              <div class="card mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                  <h6 class="m-0 font-weight-bold text-primary">Historique de présence</h6>
                </div>
                <div class="table-responsive p-3">
                  <table class="table align-items-center table-flush table-hover" id="attendanceTable">
                    <thead class="thead-light">
                      <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Justification</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if(count($attendanceRecords) == 0){
                        echo "<tr><td colspan='5' class='text-center'>Aucun enregistrement disponible.</td></tr>";
                      }else{
                        $sn = 0;
                        foreach($attendanceRecords as $record){
                          $sn++;
                          $statusText = $record['status'] == '1' ? 'Présent' : 'Absent';
                          $statusClass = $record['status'] == '1' ? 'badge-success' : 'badge-danger';
                          $justifStatus = $record['justificationStatus'] ? $record['justificationStatus'] : 'Aucune';
                          $justifClass = 'badge-light';
                          if($justifStatus == 'Pending') $justifClass = 'badge-warning';
                          if($justifStatus == 'Approved') $justifClass = 'badge-success';
                          if($justifStatus == 'Rejected') $justifClass = 'badge-danger';
                          ?>
                          <tr>
                            <td><?php echo $sn;?></td>
                            <td><?php echo $record['dateTimeTaken'];?></td>
                            <td><span class="badge <?php echo $statusClass;?>"><?php echo $statusText;?></span></td>
                            <td>
                              <?php if($record['justificationNote']){ ?>
                                <span class="badge <?php echo $justifClass;?>"><?php echo $justifStatus;?></span>
                                <div class="small text-muted mt-1"><?php echo substr($record['justificationNote'],0,80);?><?php if(strlen($record['justificationNote']) > 80) echo '...';?></div>
                              <?php } else { ?>
                                <span class="badge badge-light">Aucune</span>
                              <?php } ?>
                            </td>
                            <td>
                              <?php if($record['status'] == '0'){ ?>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#justifyModal<?php echo $record['attendanceId'];?>">
                                  Justifier
                                </button>
                              <?php } else { ?>
                                <span class="text-muted">-</span>
                              <?php } ?>
                            </td>
                          </tr>

                          <div class="modal fade" id="justifyModal<?php echo $record['attendanceId'];?>" tabindex="-1" role="dialog" aria-labelledby="justifyModalLabel<?php echo $record['attendanceId'];?>" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="justifyModalLabel<?php echo $record['attendanceId'];?>">Justification pour le <?php echo $record['dateTimeTaken'];?></h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form method="post">
                                  <div class="modal-body">
                                    <div class="form-group">
                                      <label>Expliquez votre absence</label>
                                      <textarea class="form-control" name="justificationNote" rows="4" required><?php echo $record['justificationNote'];?></textarea>
                                      <input type="hidden" name="attendanceId" value="<?php echo $record['attendanceId'];?>">
                                      <input type="hidden" name="courseId" value="<?php echo $selectedCourseId;?>">
                                      <input type="hidden" name="classArmId" value="<?php echo $selectedClassArmId;?>">
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                    <button type="submit" name="submitJustification" class="btn btn-primary">Soumettre</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          <?php
                        }
                      }
                      ?>
                    </tbody>
                  </table>
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
  <script src="../vendor/datatables/jquery.dataTables.min.js"></script>
  <script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
  <script src="../js/ruang-admin.min.js"></script>
  <script>
    $(document).ready(function () {
      $('#attendanceTable').DataTable({
        "order": [[1, "desc"]]
      });
    });
  </script>
</body>

</html>

