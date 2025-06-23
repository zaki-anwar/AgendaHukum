<?php
include "../../config/db.php";
session_start(); 

if (!isset($_SESSION['username'])) {
    header("Location: ../../auth/login.php");
    exit(); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_perkara = $_POST['id_perkara'];
    $no_perkara = trim($_POST['no_perkara']);
    $nama_klien = trim($_POST['nama_klien']);
    $jadwal_sidang = trim($_POST['jadwal_sidang']);
    $peradilan = $_POST['peradilan'] ?: '-';
    $keterangan = $_POST['keterangan'] ?: '-';
    if (empty($id_perkara) || empty($no_perkara) || empty($nama_klien) || empty($jadwal_sidang)) {
        $_SESSION['message'] = "<b>Jenis Perkara</b>,<b>No Perkara</b> dan <b>Nama Klien</b> Harus diisi.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: tambah_dataperkara.php");
        exit();
    }

    $cek_query = "SELECT id_data FROM data_perkara WHERE no_perkara = ?";
    $stmt_cek = $conn->prepare($cek_query);
    $stmt_cek->bind_param("s", $no_perkara);
    $stmt_cek->execute();
    $stmt_cek->store_result();

    if ($stmt_cek->num_rows > 0) {
        $_SESSION['message'] = "Nomor perkara <b>$no_perkara</b> sudah terdaftar.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: tambah_dataperkara.php"); 
        exit();
    }
    
    $insert_query = "INSERT INTO data_perkara (id_perkara, no_perkara, nama_klien, jadwal_sidang, peradilan, keterangan) 
                     VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($insert_query);
    $stmt->bind_param("isssss", $id_perkara, $no_perkara, $nama_klien, $jadwal_sidang, $peradilan, $keterangan);

    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='text-center'>Data perkara <b>$no_perkara</b> berhasil ditambahkan!.</div>";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "data_perkara";
        header("Location: ../../user_admin/perkara.php");
        exit();
    } else {
        $_SESSION['message'] = "<div class='text-center'>Terjadi kesalahan saat menambahkan data <b>$no_perkara</b>.</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "data_perkara";
    }

    $stmt_cek->close();
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Tambah Data</title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../../assets/css/style.css" rel="stylesheet">
</head>
<body>
  <header id="header" class="header fixed-top d-flex align-items-center">
    <div class="d-flex align-items-center justify-content-between">
      <a href="../../user_admin/index.php" class="logo d-flex align-items-center">
        <img src="../../assets/img/logo.jpg" alt="">
        <h6 class="card-logo">LEMBAGA BANTUAN HUKUM<br>TARETAN LEGAL JUSTITIA</h6>
      </a>
    </div>
    
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">
        
        <li class="nav-item dropdown pe-3">
            <i class="bi bi-list toggle-sidebar-btn"></i>
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
              <h5 class="card-title text-center pb-2 fs-4">Masukkan data perkara</h5>
              <form method="POST" action="" class="row g-3 needs-validation" novalidate>
                <div class="col-12">
                  <label for="id_perkara" class="col-sm-2 col-form-label">Jenis Perkara</label>
                  <select name="id_perkara" id="id_perkara" class="form-select" aria-label="Default select example" required>
                    <option selected>--Pilih Perkara--</option>
                    <?php
                      $query = "SELECT id_perkara, nama_perkara FROM perkara";
                      $result = mysqli_query($conn, $query);

                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='" . $row['id_perkara'] . "'>" . $row['nama_perkara'] . "</option>";
                    }
                    ?>
                  </select>
                </div>
                <div class="col-12">
                  <label for="no_perkara" class="form-label">No Perkara</label>
                  <input type="text" name="no_perkara" class="form-control" id="no_perkara" required>
                </div>
                <div class="col-12">
                  <label for="nama_klien" class="form-label">Nama Klien</label>
                  <input type="text" name="nama_klien" class="form-control" id="nama_klien" required>
                </div>
                <div class="col-12">
                  <label for="jadwal_sidang" class="col-sm-2 col-form-label">Jadwal Sidang</label>
                  <input type="datetime-local" name="jadwal_sidang" class="form-control" id="jadwal_sidang">
                </div>
                <div class="col-12">
                  <label for="peradilan" class="form-label">Peradilan</label>
                  <input type="text" name="peradilan" class="form-control" id="peradilan">
                </div>
                <div class="col-12">
                  <label for="keterangan" class="col-sm-2 col-form-label">Keterangan</label>
                  <textarea class="form-control" name="keterangan" id="keterangan" style="height: 100px"></textarea>
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
      <p class="small">by Zaki_Anwar</p>
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