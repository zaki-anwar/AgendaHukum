<?php
include "../../config/db.php";
session_start();

if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
    header("Location: ../../auth/login.php");
    exit();
}

$id = $_SESSION['id_user'];

// Ambil data user yang sedang login
$query = "SELECT * FROM user WHERE id = $id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    $_SESSION['message'] = "Data tidak ditemukan.";
    $_SESSION['message_type'] = "danger";
    $_SESSION['message_section'] = "edit_profil";
    header("Location: ../../index.php");
    exit();
}
if (isset($_POST['submit'])) {
    $nama = htmlspecialchars($_POST['nama']);
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password'];
    $email = htmlspecialchars($_POST['email']);
    $no_telp = htmlspecialchars($_POST['no_telp']);


    $cekUsername = "SELECT id FROM user WHERE username = '$username' AND id != $id";
    $cekResult = mysqli_query($conn, $cekUsername);

    if (mysqli_num_rows($cekResult) > 0) {
        $_SESSION['message'] = "Username sudah digunakan oleh pengguna lain.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "edit_profil";
        header("Location: edit_profil.php");
        exit();
    }

    $fotoBaru = '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fotoName = basename($_FILES['foto']['name']);
        $fotoTmp = $_FILES['foto']['tmp_name'];
        $fotoExt = pathinfo($fotoName, PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($fotoExt), $allowed)) {
            $fotoBaru = uniqid() . '.' . $fotoExt;
            $uploadPath = "../../assets/img/" . $fotoBaru;
            if (move_uploaded_file($fotoTmp, $uploadPath)) {
                if (!empty($data['foto']) && file_exists("../../assets/img/" . $data['foto'])) {
                    unlink("../../assets/img/" . $data['foto']);
                }
            } else {
                $_SESSION['message'] = "Gagal mengupload foto.";
                $_SESSION['message_type'] = "danger";
                $_SESSION['message_section'] = "edit_profil";
                header("Location: edit_profil.php");
                exit();
            }
        } else {
            $_SESSION['message'] = "Format foto tidak didukung. Gunakan jpg, jpeg, png, atau gif.";
            $_SESSION['message_type'] = "danger";
            $_SESSION['message_section'] = "edit_profil";
            header("Location: edit_profil.php");
            exit();
        }
    }

     $sql = "UPDATE user SET nama = '$nama', username = '$username', email = '$email', no_telp = '$no_telp'";
    if (!empty($password)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql .= ", password = '$hashed'";
    }
    if (!empty($fotoBaru)) {
        $sql .= ", foto = '$fotoBaru'";
    }
    $sql .= " WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Profil berhasil diperbarui.";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "profil";
        header("Location: ../../user_admin/profil.php");
    } else {
        $_SESSION['message'] = "Gagal memperbarui profil.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "edit_profil";
        header("Location: edit_profil.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Edit Profil</title>
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
        <a class="nav-link" href="../../user_admin/profil.php">
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
      <h1>Edit Profil</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="../../user_admin/index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="../../user_admin/profil.php">Profil</a></li>
          <li class="breadcrumb-item active">Edit Profil</li>
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
              <h5 class="card-title text-center pb-2 fs-4">Edit Profil</h5>

              <?php if (!empty($data['foto'])): ?>
                <div class="text-center mb-3">
                  <img src="../../assets/img/<?= htmlspecialchars($data['foto']) ?>" class="rounded-circle border border-3 border-black" style="width: 120px; height: 120px; object-fit: cover;">
                </div>
              <?php endif; ?>
              <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                  <label class="form-label">Nama</label>
                  <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($data['username']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($data['email']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">No. Telp</label>
                  <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($data['no_telp']) ?>" required>
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
                    <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
                    <button type="button" class="btn btn-outline-secondary togglePassword" data-target="confirm_password">
                      <i class="bi bi-eye-fill"></i>
                    </button>
                  </div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Foto Profil</label>
                  <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
                <div class="d-grid gap-2">
                  <button class="btn btn-primary" type="submit" name="submit">Perbarui</button>
                  <a href="../../user_admin/profil.php" class="btn btn-secondary w-100">Kembali</a>
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
