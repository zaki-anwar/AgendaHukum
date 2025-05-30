<?php
include "../../config/db.php";
session_start();

if (isset($_GET['hapus_anggota'])) {
    $id = $_GET['hapus_anggota'];

    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        $_SESSION['message'] = "<div class='text-center'>ID tidak valid.</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_anggota";
        header("Location: ../../user_admin/jumlah_anggota.php");
        exit();
    }

    $check_query = "SELECT username FROM user WHERE id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $_SESSION['message'] = "<div class='text-center'>User dengan ID tersebut tidak ditemukan.</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_anggota";
        $stmt->close();
        header("Location: ../../user_admin/jumlah_anggota.php");
        exit();
    }

    $stmt->bind_result($username);
    $stmt->fetch();
    $stmt->close();

    // Lanjut hapus
    $delete_query = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='text-center'><b>$username</b> berhasil dihapus.</div>";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "hapus_anggota";
    } else {
        $_SESSION['message'] = "<div class='text-center'>Terjadi kesalahan saat menghapus <b>$username</b>.</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_anggota";
    }

    $stmt->close();
    header("Location: ../../user_admin/jumlah_anggota.php");
    exit();
} else {
    $_SESSION['message'] = "<div class='text-center'>Permintaan tidak valid.</div>";
    $_SESSION['message_type'] = "danger";
    $_SESSION['message_section'] = "hapus_anggota";
    header("Location: ../../user_admin/jumlah_anggota.php");
    exit();
}
?>
