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
  <title>Jumlah Admin</title>

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
        <a class="nav-link collapsed" href="perkara.php">
          <i class="bi bi-journal-text"></i>
          <span>Data Perkara</span>
        </a>
      </li> 
      <li class="nav-heading">__________________________________________________</li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="profil.php">
          <i class="bi bi-person-lines-fill"></i>
          <span>Profil</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="jumlah_anggota.php">
          <i class="bi bi-person-lines-fill"></i>
          <span>Anggota Tim</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Jumlah Anggota</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Jumlah Anggota</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <?php
          if (isset($_SESSION['message']) && isset($_SESSION['message_section']) && $_SESSION['message_section'] == 'tambah_admin') {
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
          if (isset($_SESSION['message']) && isset($_SESSION['message_section']) && $_SESSION['message_section'] == 'hapus_admin') {
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
              <h5 class="card-title">Daftar Anggota</h5>
              <div class="card mb-4">
                <div class="card-body">
                  <h5 class="card-title"></h5>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama</th>
                          <th>Username</th>
                          <th>Status</th>
                          <th>email</th>
                          <th>No. Tlp</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $query = "SELECT * FROM user ORDER BY 
                                    CASE WHEN status = 'admin' THEN 0 ELSE 1 END, 
                                    nama ASC";
                          $result = mysqli_query($conn, $query);
                          $no = 1;
                          while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $no++ . "</td>";
                            echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email'] ?? '') . "</td>";
                            $raw_nomor = $row['no_telp'] ?? '';
                            $nomor_bersih = preg_replace('/[^0-9]/', '', $raw_nomor);
                            if (str_starts_with($nomor_bersih, '0')) {
                                $nomor_wa = '62' . substr($nomor_bersih, 1);
                            } else {
                                $nomor_wa = $nomor_bersih;
                            }
                            echo "<td><a href='https://wa.me/" . htmlspecialchars($nomor_wa, ENT_QUOTES, 'UTF-8') . "' target='_blank'>" . htmlspecialchars($raw_nomor, ENT_QUOTES, 'UTF-8') . "</a></td>";
                            echo "</tr>";
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
      </div>
    </section>
  </main>

  <footer id="footer" class="footer">
    <div class="copyright">
      <strong><span>AgendaHukum</span></strong>
      <p class="small">by Kelompok_8</p>
    </div>
  </footer>

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