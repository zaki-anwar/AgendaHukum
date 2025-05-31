<?php
session_start();
include "../../config/db.php";

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_perkara = trim($_POST['nama_perkara']);

    if (empty($nama_perkara)) {
        $_SESSION['message'] = "<div class='text-center'>Nama perkara tidak boleh kosong.</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "perkara";
        header("Location: tambah_perkara.php");
        exit();
    }

    $cek_query = "SELECT id_perkara FROM perkara WHERE nama_perkara = ?";
    $stmt_cek = $conn->prepare($cek_query);
    $stmt_cek->bind_param("s", $nama_perkara);
    $stmt_cek->execute();
    $stmt_cek->store_result();

    if ($stmt_cek->num_rows > 0) {
        $_SESSION['message'] = "<div class='text-center'><b>$nama_perkara</b> sudah ada.</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "perkara";
        header("Location: tambah_perkara.php");
        exit();
    }

    $insert_query = "INSERT INTO perkara (nama_perkara) VALUES (?)";
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("s", $nama_perkara);

    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='text-center'><b>$nama_perkara</b> berhasil ditambahkan!</div>";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "perkara";
    } else {
        $_SESSION['message'] = "<div class='text-center'>Gagal menambahkan <b>$nama_perkara</b>. </div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "perkara";
    }

    header("Location: ../../user_admin/perkara.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Tambah Perkara</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

  <link href="../../assets/css/style.css" rel="stylesheet">

</head>

<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <i class="bi bi-list toggle-sidebar-btn"></i>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        <li class="nav-item dropdown pe-3">
          <a class="dropdown-item d-flex align-items-center" href="auth/logout.php">
            <i class="bi bi-box-arrow-right"></i>
          </a>
        </li>
      </ul>
    </nav>
  </header>

  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">
      <li class="nav-item">
        <a class="nav-link collapsed" href="../../user_admin/index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="../../user_admin/perkara.php">
          <i class="bi bi-journal-text"></i>
          <span>Data Perkara</span>
        </a>
      </li> 
      <li class="nav-item">
        <a class="nav-link collapsed" href="../../user_admin/profil.php">
          <i class="bi bi-person-circle"></i>
          <span>Profil</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../../user_admin/jumlah_anggota.php">
          <i class="bi bi-person-lines-fill"></i>
          <span>Anggota Tim</span>
        </a>
      </li>
      <li class="nav-heading">__________________________________________________</li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="../../auth/logout.php">
          <i class="bi bi-box-arrow-right"></i>
          <span>Logout</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Tambah Perkara</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../../user_admin/index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="../../user_admin/perkara.php">Data Perkara</a></li>
          <li class="breadcrumb-item active">Tambah Perkara</li>
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

          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center pb-2 fs-4">Masukkan nama perkara</h5>
              <p></p>
              <form method="POST" action="" class="row g-3 needs-validation" novalidate>
                <div class="col-12">
                  <label for="nama_perkara" class="form-label">Nama Perkara</label>
                  <input type="text" name="nama_perkara" class="form-control" id="nama_perkara" required>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Tambah Perkara</button><br><br>
                    <a href="../../user_admin/perkara.php" class="btn btn-secondary w-100" type="submit">Kembali</a>
                </div>
              </form>
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

  <script src="../../assets/js/main.js"></script>
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