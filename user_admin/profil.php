<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || !isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Ambil data user termasuk foto
$stmt = $conn->prepare("SELECT username, nama, status, foto FROM user WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$profil = $result->fetch_assoc();

if (!$profil) {
    echo "Data pengguna tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Profil Saya</title>
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
        <a class="nav-link active" href="profil.php">
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
    </ul>
  </aside>

  <main id="main" class="main">
  <div class="pagetitle">
    <h1>Profil Saya</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item active">Profil</li>
      </ol>
    </nav>
  </div>

  <?php
    if (isset($_SESSION['message']) && $_SESSION['message_section'] == 'profil') {
        echo "<div id='alertMessage' class='alert alert-{$_SESSION['message_type']} alert-dismissible fade show' role='alert'>
                " . htmlspecialchars($_SESSION['message']) . "
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
        unset($_SESSION['message'], $_SESSION['message_type'], $_SESSION['message_section']);
    }
  ?>

  <section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow">
          <div class="card-body">
            <h5 class="card-title text-center pb-3 fs-4">Informasi Pengguna</h5>
            <?php
              $fotoPath = !empty($profil['foto']) ? "../assets/img/" . htmlspecialchars($profil['foto']) : "../assets/img/profil.jpg";
            ?>
            <div class="text-center mb-3">
              <img src="<?= $fotoPath ?>" alt="Foto Profil"
              class="rounded-circle border border-3 border-black"
              style="width: 120px; height: 120px; object-fit: cover;">
            </div>
            <form action="" method="post" enctype="multipart/form-data" id="profilForm">
              <div class="mb-3">
                <label class="form-label"><strong>Nama</strong></label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($profil['nama']) ?>" readonly>
              </div>

              <div class="mb-3">
                <label class="form-label"><strong>Username</strong></label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($profil['username']) ?>" readonly>
              </div>
              <div class="mb-3">
                <label class="form-label"><strong>Status</strong></label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($profil['status']) ?>" readonly>
              </div>
              <div class="d-grid gap-2 mt-3">
                <a href="../crud/user_admin/edit_profil.php" class="btn btn-primary">Edit Profil</a>
                <a href="../auth/logout.php" class="btn btn-danger">Logout</a>
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

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
