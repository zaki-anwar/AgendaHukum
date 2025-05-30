<?php
include "../../config/db.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../../auth/login.php");
    exit(); 
}

if (isset($_GET['hapus'])) {
    $id_perkara = $_GET['hapus'];

    if (!filter_var($id_perkara, FILTER_VALIDATE_INT)) {
        $_SESSION['message'] = "<div class='text-center'>ID <b>$nama_perkara</b> tidak valid.</div>";
        $_SESSION['message_type'] = "danger";
        header("Location: ../../user_admin/perkara.php");
        exit();
    }

    $get_name_query = "SELECT nama_perkara FROM perkara WHERE id_perkara = ?";
    $stmt = $conn->prepare($get_name_query);
    $stmt->bind_param("i", $id_perkara);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $nama_perkara = $row['nama_perkara'] ?? "Perkara";

    $check_query = "SELECT COUNT(*) AS jumlah FROM data_perkara WHERE id_perkara = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id_perkara);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['jumlah'] > 0) {
        $_SESSION['message'] = "<div class='text-center'>Gagal, masih ada data di <b>$nama_perkara</b>.</div>";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "perkara";
    } else {
        $delete_query = "DELETE FROM perkara WHERE id_perkara = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $id_perkara);

        if ($stmt->execute()) {
            $_SESSION['message'] = "<div class='text-center'><b>$nama_perkara</b> berhasil dihapus.</div>";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "<div class='text-center'>Terjadi kesalahan saat menghapus <b>$nama_perkara</b>.</div>";
            $_SESSION['message_type'] = "danger";
        }
        $_SESSION['message_section'] = "perkara";
    }

    $stmt->close();
    $conn->close();

    header("Location: ../../user_admin/perkara.php");
    exit();
} else {
    $_SESSION['message'] = "<div class='text-center'>ID <b>$nama_perkara</b> tidak ditemukan.</div>";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../user_admin/perkara.php");
    exit();
}
?>
