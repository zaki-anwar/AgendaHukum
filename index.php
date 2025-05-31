<?php
session_start();
include "config/db.php";

$query = "SELECT p.nama_perkara, COUNT(dp.id_data) AS jumlah_data_perkara 
          FROM perkara p
          LEFT JOIN data_perkara dp ON dp.id_perkara = p.id_perkara
          GROUP BY p.id_perkara";
$result = mysqli_query($conn, $query);

if ($result && $result->num_rows > 0) {
    $data_perkara = [];
    while ($row = $result->fetch_assoc()) {
        $data_perkara[] = $row;
    }
} else {
    $data_perkara = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Dashboard</title>

  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <i class="bi bi-list toggle-sidebar-btn"></i>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) : ?>

            <a class="dropdown-item d-flex align-items-center" href="auth/logout.php">
              <i class="bi bi-box-arrow-right"></i>
            </a>
          <?php endif; ?>

        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link" href="user/index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="user/perkara.php">
          <i class="bi bi-journal-text"></i>
          <span>Data Perkara</span>
        </a>
      </li> 
      <li class="nav-item">
        <a class="nav-link collapsed" href="user/profil.php">
          <i class="bi bi-person-circle"></i>
          <span>Profil</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="user/jumlah_anggota.php">
          <i class="bi bi-person-lines-fill"></i>
          <span>Anggota Tim</span>
        </a>
      </li>
      <li class="nav-heading">__________________________________________________</li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="auth/login.php">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Login</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div>

    <section class="section dashboard">
      <?php
      if (isset($_SESSION['message']) && $_SESSION['message_section'] == "login") {
        $message = $_SESSION['message'];
        $message_type = $_SESSION['message_type'];
          echo "<div id='alertMessage' class='alert alert-$message_type' role='alert'>
                  $message
                </div>";
          unset($_SESSION['message']);
          unset($_SESSION['message_type']);
          unset($_SESSION['message_section']);
      }
      ?>
  
      <div class="card">
        <div class="card-body">
          <h5 class="card-title text-center pb-2 fs-4">Data Perkara</h5>
          <div class="row">
            <?php if (!empty($data_perkara) && is_array($data_perkara)) : ?>
            <?php foreach ($data_perkara as $perkara) : ?>
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body"><br>
                  <h6 class="card-title"><?php echo htmlspecialchars($perkara['nama_perkara'], ENT_QUOTES, 'UTF-8'); ?></h6>
                  <div class="d-flex align-items-center">
                    <div class="ps-3"><br>
                      <h5>Data Perkara : <?php echo htmlspecialchars($perkara['jumlah_data_perkara'], ENT_QUOTES, 'UTF-8'); ?></h5>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else : ?>
          <p class="text-center">Tidak ada perkara.</p>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title text-center pb-2 fs-4">Sidang Terdekat</h5>
        <?php
          $query = "SELECT p.id_perkara, p.nama_perkara, dp.id_data, dp.no_perkara, dp.nama_klien, dp.jadwal_sidang, dp.peradilan, dp.keterangan
                    FROM perkara p
                    LEFT JOIN data_perkara dp ON p.id_perkara = dp.id_perkara
                    WHERE dp.jadwal_sidang >= CURDATE()
                    ORDER BY dp.jadwal_sidang ASC
                    LIMIT 3";  
          $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
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
              echo "<h5 class='card-title pb-2 fs-4'>" . htmlspecialchars($current_perkara, ENT_QUOTES, 'UTF-8') . "</h5>";
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
          echo "</tbody></table></div></div></div>";
        } else {
          echo "<p class='text-center'>Tidak ada data perkara.</p>";
        }
        $conn->close();
        ?>
      </div>
    </div>   
  </main>

  <footer id="footer" class="footer">
    <div class="copyright">
      <strong><span>AgendaHukum</span></strong>
      <p class="small">by Kelompok_8</p>
    </div>
  </footer>
  
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="assets/js/main.js"></script>
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
