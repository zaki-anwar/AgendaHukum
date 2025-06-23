<?php
include "../../config/db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = isset($_POST['nama']) ? mysqli_real_escape_string($conn, $_POST['nama']) : '';
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : 'user'; 
    $allowed_status = ['admin', 'user'];


    if (empty($nama) || empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['message'] = "<div class='text-center'>Semua field wajib diisi!</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "tambah_anggota";
     } elseif (!in_array($status, $allowed_status)) {
        $_SESSION['message'] = "<div class='text-center'>Status tidak valid!</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "tambah_anggota";
        header("Location: tambah_anggota.php");
        exit();
    } elseif ($password !== $confirm_password) {
        $_SESSION['message'] = "<div class='text-center'>Konfirmasi kata sandi tidak cocok!</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "tambah_anggota";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query_check = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($query_check);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['message'] = "<div class='text-center'>Username <b>$username</b> sudah terdaftar!</div>";
            $_SESSION['message_type'] = "danger";
            $_SESSION['message_section'] = "tambah_anggota";
        } else {
            $query = "INSERT INTO user (nama, username, password, status) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $nama, $username, $hashed_password, $status);

            if ($stmt->execute()) {
                $_SESSION['message'] = "<div class='text-center'><b>$username</b> berhasil ditambah sebagai <b>$status</b>!</div>";
                $_SESSION['message_type'] = "success";
                $_SESSION['message_section'] = "tambah_anggota";
                header("Location: ../../user_admin/jumlah_anggota.php");
                exit();
            } else {
                $_SESSION['message'] = "<div class='text-center'>Terjadi kesalahan saat menambah!</div>";
                $_SESSION['message_type'] = "danger";
                $_SESSION['message_section'] = "tambah_anggota";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Tambah Admin</title>
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
        <a class="nav-link collapsed" href="../../user_admin/perkara.php">
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
        <a class="nav-link" href="../../user_admin/jumlah_anggota.php">
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
      <h1>Tambah Anggota</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../../user_admin/index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="../../user_admin/jumlah_anggota.php">Anggota Tim</a></li>
          <li class="breadcrumb-item active">Tambah Anggota</li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <?php
          if (isset($_SESSION['message']) && isset($_SESSION['message_section']) && $_SESSION['message_section'] == 'tambah_anggota') {
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
              <h5 class="card-title text-center pb-0 fs-4">Tambah Anggota</h5>
              <p class="text-center">Masukkan data yang diperlukan</p>
              <form action="tambah_anggota.php" method="POST" class="row g-3 needs-validation" novalidate>
                <div class="col-12">
                  <label for="nama" class="form-label">Nama</label>
                  <input type="text" name="nama" class="form-control" id="nama" required>
                </div>
                <div class="col-12">
                  <label for="username" class="form-label">Username</label>
                  <div class="input-group has-validation">
                    <input type="text" name="username" class="form-control" id="username" required>
                  </div>
                </div>
                <div class="col-12">
                  <label for="password" class="form-label">Kata Sandi</label>
                  <div class="input-group">
                    <input type="password" name="password" class="form-control" id="password" required>
                    <button type="button" class="btn btn-outline-secondary togglePassword" data-target="password">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                  </div>
                </div>
                <div class="col-12">
                  <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi</label>
                  <div class="input-group">
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                    <button type="button" class="btn btn-outline-secondary togglePassword" data-target="confirm_password">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                  </div>
                </div>
                <div class="col-12">
                  <label for="status" class="col-sm-2 col-form-label">Status</label>
                  <select name="status" id="status" class="form-select" aria-label="Default select example" required>
                    <option value="" selected>--Pilih Status--</option>
                    <?php
                      $query = "SHOW COLUMNS FROM user LIKE 'status'";
                      $result = mysqli_query($conn, $query);
                      $row = mysqli_fetch_assoc($result);
                      $type = $row['Type'];
                      preg_match("/^enum\('(.*)'\)$/", $type, $matches);
                      $enum_values = explode("','", $matches[1]);
                      foreach ($enum_values as $status_option) {
                        echo "<option value='$status_option'>" . ucfirst($status_option) . "</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary w-100" type="submit">Tambah Anggota</button><br><br>
                    <a href="../../user_admin/jumlah_anggota.php" class="btn btn-secondary w-100" type="submit">Kembali</a>
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
<script>
    document.querySelectorAll('.togglePassword').forEach(button => {
        button.addEventListener('click', function () {
            let targetId = this.getAttribute('data-target');
            let passwordField = document.getElementById(targetId);
            let icon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
            }
        });
    });
</script>
</body>
</html>