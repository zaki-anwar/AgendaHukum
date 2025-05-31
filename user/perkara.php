<?php
include "../config/db.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Data Perkara</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <i class="bi bi-list toggle-sidebar-btn"></i>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="dropdown-item d-flex align-items-center" href="../auth/logout.php">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="perkara.php">
          <i class="bi bi-journal-text"></i>
          <span>Data Perkara</span>
        </a>
      </li> 
      <li class="nav-item">
        <a class="nav-link collapsed" href="profil.php">
          <i class="bi bi-person-circle"></i>
          <span>Profil</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="jumlah_anggota.php">
          <i class="bi bi-person-lines-fill"></i>
          <span>Anggota Tim</span>
        </a>
      </li>
      <li class="nav-heading">__________________________________________________</li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../auth/logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Data Perkara</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Data Perkara</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <?php
          if (isset($_SESSION['message']) && $_SESSION['message_section'] == 'perkara') {
            $message = $_SESSION['message'];
            $message_type = $_SESSION['message_type'];

            echo "<div id='alertMessage' class='alert alert-$message_type alert-dismissible fade show' role='alert'>
                    $message
                  </div>";

            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            unset($_SESSION['message_section']);
          }
          ?>
                    
          <?php
          if (isset($_SESSION['message']) && $_SESSION['message_section'] == 'data_perkara') {
            $message = $_SESSION['message'];
            $message_type = $_SESSION['message_type'];
                            
              echo "<div id='alertMessage' class='alert alert-$message_type alert-dismissible fade show' role='alert'>
                      $message
                    </div>";         
              unset($_SESSION['message']);
              unset($_SESSION['message_type']);
              unset($_SESSION['message_section']);
          }
          ?>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Daftar Perkara</h5>
              <div class="card mb-4">
                <div class="card-body">
                  <h5 class="card-title"></h5>
                  <?php
                  $query = "SELECT * FROM perkara";
                  $result = mysqli_query($conn, $query);
                  
                  if (mysqli_num_rows($result) > 0) {
                      echo '<div class="table-responsive">
                              <table class="table table-bordered">
                                  <thead>
                                      <tr>
                                          <th>No</th>
                                          <th>Nama Perkara</th>
                                      </tr>
                                  </thead>
                                  <tbody>';

                      $no = 1;
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['nama_perkara']) . "</td>";
                        echo "</tr>";
                      }
                      echo '</tbody></table></div>'; 
                  } else {
                      echo "<p class='text-center'>Tidak ada perkara.</p>";
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>

          

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Daftar Data Perkara</h5>
              <?php
                $query_check = "SELECT COUNT(*) AS jumlah FROM perkara";
                $result_check = mysqli_query($conn, $query_check);
                $row_check = mysqli_fetch_assoc($result_check);
              ?>

              <?php
                $query = "SELECT p.id_perkara, p.nama_perkara, dp.id_data, dp.no_perkara, dp.nama_klien, dp.jadwal_sidang, dp.peradilan, dp.keterangan
                          FROM perkara p
                          LEFT JOIN data_perkara dp ON p.id_perkara = dp.id_perkara
                          ORDER BY p.id_perkara, dp.id_data";
                $result = mysqli_query($conn, $query);
                $current_perkara = null;
                $no = 1;

                while ($row = mysqli_fetch_assoc($result)) {
                    if ($current_perkara != $row['nama_perkara']) {
                      if ($current_perkara !== null) {
                        echo "</tbody></table></div></div></div><br>";
                      }
                      $current_perkara = $row['nama_perkara'];
                        echo "<div class='card mb-4'>";
                        echo "<div class='card-body'>";
                        echo "<h5 class='card-title'>" . htmlspecialchars($current_perkara, ENT_QUOTES, 'UTF-8') . "</h5>";
                        echo "<div class='table-responsive'>";
                        echo "<table class='table table-bordered'>
                                <thead>
                                  <tr>
                                    <th>No</th>
                                    <th>Nomor Perkara</th>
                                    <th>Nama Klien</th>
                                    <th>Jadwal Sidang</th>
                                    <th>Peradilan</th>
                                    <th>Keterangan</th>
                                  </tr>
                                </thead>
                                <tbody>"; 
                      $no = 1;
                    }
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['no_perkara'] ?? '-', ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['nama_klien'] ?? '-', ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . (isset($row['jadwal_sidang']) ? date('d-m-Y H:i', strtotime($row['jadwal_sidang'])) : '-') . "</td>";
                    echo "<td>" . htmlspecialchars($row['peradilan'] ?? '-', ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "<td>" . htmlspecialchars($row['keterangan'] ?? '-', ENT_QUOTES, 'UTF-8') . "</td>";
                    echo "</tr>";
                }
                if ($current_perkara !== null) {
                    echo "</tbody></table></div></div></div>";
              }
              ?>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer id="footer" class="footer">
    <div class="copyright">
      <strong><span>AgendaHukum</span></strong>
      <p class="small">by Kelompok_8</p>
    </div>
  </footer>

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="../assets/js/main.js"></script>
  <script>
    setTimeout(function() {
        let alertBox = document.getElementById("alertMessage");
        if (alertBox) {
            alertBox.style.transition = "opacity 0.5s";
            alertBox.style.opacity = "0";
            setTimeout(() => alertBox.remove(), 300);
        }
    }, 3000);
  </script>
</body>
</html>