<?php
include "../../config/db.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../auth/login.php");
    exit(); 
}

if (isset($_GET['id_data'])) {
    $id_data = $_GET['id_data'];

    if (!filter_var($id_data, FILTER_VALIDATE_INT)) {
        $_SESSION['message'] = "ID data perkara tidak valid.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../../user_admin/perkara.php");
        exit();
    }

    $query = "SELECT dp.*, p.nama_perkara 
              FROM data_perkara dp
              JOIN perkara p ON dp.id_perkara = p.id_perkara
              WHERE dp.id_data = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_data);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        $_SESSION['message'] = "Data perkara tidak ditemukan.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: ../../user_admin/perkara.php");
        exit();
    }

    $data_perkara = $result->fetch_assoc();
} else {
    $_SESSION['message'] = "ID data perkara tidak ditemukan.";
    $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
    header("Location: ../../user_admin/perkara.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_perkara = $_POST['no_perkara'];
    $nama_klien = $_POST['nama_klien'];
    $jadwal_sidang = $_POST['jadwal_sidang'];
    $peradilan = $_POST['peradilan'] ?: '-';
    $keterangan = $_POST['keterangan'] ?: '-';

    if (empty($no_perkara) || empty($nama_klien)) {
        $_SESSION['message'] = "Nomor perkara dan nama klien harus diisi.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: edit_dataperkara.php?id_data=" . $id_data);
        exit();
    }

    // Cek apakah nomor perkara sudah digunakan oleh data lain
    $cek_query = "SELECT id_data FROM data_perkara WHERE no_perkara = ? AND id_data != ?";
    $cek_stmt = $conn->prepare($cek_query);
    $cek_stmt->bind_param("si", $no_perkara, $id_data);
    $cek_stmt->execute();
    $cek_result = $cek_stmt->get_result();

    if ($cek_result->num_rows > 0) {
        $_SESSION['message'] = "Nomor perkara sudah digunakan oleh data lain.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: edit_dataperkara.php?id_data=" . $id_data);
        exit();
    }

    // Proses update
    $update_query = "UPDATE data_perkara 
                     SET no_perkara = ?, nama_klien = ?, jadwal_sidang = ?, peradilan = ?, keterangan = ? 
                     WHERE id_data = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sssssi", $no_perkara, $nama_klien, $jadwal_sidang, $peradilan, $keterangan, $id_data);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data perkara berhasil diperbarui.";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: ../../user_admin/perkara.php");
        exit();
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat memperbarui data perkara.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: edit_dataperkara.php?id_data=" . $id_data);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Edit Data</title>
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
          <a class="dropdown-item d-flex align-items-center" href="../../auth/logout.php">
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
      <li class="nav-heading">__________________________________________________</li>
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
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Tambah Data Perkara</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../../user_admin/index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="../../user_admin/perkara.php">Data Perkara</a></li>
          <li class="breadcrumb-item active">Tambah Data Perkara</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <?php
            if (isset($_SESSION['message'])) {
              echo "<div  id='alertMessage' class='alert alert-{$_SESSION['message_type']}'>" . $_SESSION['message'] . "</div>";
              unset($_SESSION['message']);
              unset($_SESSION['message_type']);
            }
          ?>
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-center pb-2 fs-4">Edit Data <?php echo htmlspecialchars($data_perkara['nama_perkara']); ?></h5>
              <form method="POST" action="edit_dataperkara.php?id_data=<?php echo $id_data; ?>" class="row g-3 needs-validation" novalidate>
                <div class="col-12">
                  <label for="no_perkara" class="form-label">No Perkara</label>
                  <input type="text" name="no_perkara" class="form-control" id="no_perkara" value="<?php echo htmlspecialchars($data_perkara['no_perkara']); ?>">
                </div>
                <div class="col-12">
                  <label for="nama_klien" class="form-label">Nama Klien</label>
                  <input type="text" name="nama_klien" class="form-control" id="nama_klien" value="<?php echo htmlspecialchars($data_perkara['nama_klien']); ?>">
                </div>
                <div class="col-12">
                  <label for="jadwal_sidang" class="col-sm-2 col-form-label">Jadwal Sidang</label>
                  <input type="datetime-local" name="jadwal_sidang" class="form-control" id="jadwal_sidang" value="<?php echo htmlspecialchars(date('Y-m-d\TH:i', strtotime($data_perkara['jadwal_sidang']))); ?>">
                </div>
                <div class="col-12">
                  <label for="peradilan" class="form-label">Peradilan</label>
                  <input type="text" name="peradilan" class="form-control" id="peradilan" value="<?php echo htmlspecialchars($data_perkara['peradilan']); ?>">
                </div>
                <div class="col-12">
                  <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                  <textarea class="form-control" name="keterangan" id="keterangan" style="height: 100px"><?php echo htmlspecialchars($data_perkara['keterangan']); ?></textarea>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Perbarui Data</button><br><br>
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


