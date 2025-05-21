<?php
include "../config/db.php";
session_start();

if (isset($_GET['hapus_admin'])) {
    $id = $_GET['hapus_admin'];

    if (!filter_var($id, FILTER_VALIDATE_INT)) {
        $_SESSION['message'] = "ID anggota perkara tidak valid.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_admin";
        header("Location: ../user_admin/jumlah_admin.php");
        exit();
    }

    $check_query = "SELECT COUNT(*) FROM user WHERE id = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($jumlah);
    $stmt->fetch();
    $stmt->close();

    if ($jumlah == 0) {
        $_SESSION['message'] = "Data anggota tidak ditemukan.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "tambah_admin";
        header("Location: ../user_admin/jumlah_admin.php");
        exit();
    }

    $delete_query = "DELETE FROM user WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Anggota berhasil dihapus.";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "hapus_admin";
    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat menghapus admin.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "hapus_admin";
    }
    $stmt->close();
    header("Location: ../user_admin/jumlah_admin.php");
    exit();
} else {
    $_SESSION['message'] = "ID anggota tidak ditemukan.";
    $_SESSION['message_type'] = "danger";
    $_SESSION['message_section'] = "hapus_admin";
    header("Location: ../user_admin/jumlah_admin.php");
    exit();
}
?>