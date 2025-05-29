<?php
include "../../config/db.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../auth/login.php");
    exit();
}

// Ambil data user berdasarkan ID dari URL
if (isset($_GET['edit_anggota'])) {
    $id = $_GET['edit_anggota'];
    $query = "SELECT * FROM user WHERE id = $id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);

    if (!$data) {
        $_SESSION['message'] = "Data anggota tidak ditemukan.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "edit_anggota";
        header("Location: ../../user_admin/jumlah_anggota.php");
        exit();
    }
} else {
    $_SESSION['message'] = "ID anggota tidak valid.";
    $_SESSION['message_type'] = "danger";
    $_SESSION['message_section'] = "edit_anggota";
    header("Location: ../../user_admin/jumlah_anggota.php");
    exit();
}

// Proses simpan perubahan
if (isset($_POST['submit'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $status = htmlspecialchars($_POST['status']);
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user SET nama = '$nama', status = '$status', password = '$hashed' WHERE id = $id";
    } else {
        $sql = "UPDATE user SET nama = '$nama', status = '$status' WHERE id = $id";
    }

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Data anggota berhasil diperbarui.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Data anggota gagal diperbarui.";
        $_SESSION['message_type'] = "danger";
    }

    $_SESSION['message_section'] = "edit_anggota";
    header("Location: ../../user_admin/jumlah_anggota.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Edit Anggota</title>

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
        <a class="nav-link collapsed" href="../../user_admin/perkara.php">
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
        <a class="nav-link" href="../../user_admin/jumlah_anggota.php">
          <i class="bi bi-person-lines-fill"></i>
          <span>Anggota Tim</span>
        </a>
      </li>
    </ul>
  </aside>

  <main id="main" class="main">
    <div class="pagetitle">
      <h1>Edit Anggota</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../../user_admin/index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="../../user_admin/jumlah_anggota.php">Anggota Tim</a></li>
          <li class="breadcrumb-item active">Edit Anggota</li>
        </ol>
      </nav>
    </div>
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          
          <?php
            if (isset($_SESSION['message']) && $_SESSION['message_section'] == 'edit_anggota') {
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
              <h5 class="card-title text-center pb-2 fs-4">Edit Anggota</h5>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                    </div>
  
                <div class="col-12">
                  <label for="password" class="form-label">Kata Sandi</label>
                  <div class="input-group">
                    <input type="password" name="password" class="form-control" id="password" placeholder="Kosongkan jika tidak ingin mengubah">
                    <button type="button" class="btn btn-outline-secondary togglePassword" data-target="password">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                  </div>
                </div>
                <div class="col-12">
                  <label for="confirm_password" class="form-label">Konfirmasi Kata Sandi</label>
                  <div class="input-group">
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Kosongkan jika tidak ingin mengubah">
                    <button type="button" class="btn btn-outline-secondary togglePassword" data-target="confirm_password">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                  </div>
                </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                        <option value="admin" <?= $data['status'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="user" <?= $data['status'] == 'user' ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>

                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" type="submit" name="submit">Perbarui</button>
                        <a href="../../user_admin/jumlah_anggota.php" class="btn btn-secondary">Kembali</a>
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