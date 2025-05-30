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
        $_SESSION['message'] = "<div class='text-center'>ID data tidak valid.</div>";
        $_SESSION['message_type'] = "danger";
        header("Location: ../../user_admin/perkara.php");
        exit();
    }

    $get_no_query = "SELECT no_perkara FROM data_perkara WHERE id_data = ?";
    $stmt = $conn->prepare($get_no_query);
    $stmt->bind_param("i", $id_data);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $no_perkara = $row['no_perkara'] ?? "Data";

    if (!$row) {
        $_SESSION['message'] = "<div class='text-center'>Data <b>$no_perkara</b> tidak ditemukan.</div>";
        $_SESSION['message_type'] = "danger";
        header("Location: ../../user_admin/perkara.php");
        exit();
    }

    $delete_query = "DELETE FROM data_perkara WHERE id_data = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id_data);

    if ($stmt->execute()) {
        $_SESSION['message'] = "<div class='text-center'>Data <b>$no_perkara</b> berhasil dihapus.</div>";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "data_perkara";
    } else {
        $_SESSION['message'] = "<div class='text-center'>Terjadi kesalahan saat menghapus data <b>$no_perkara</b>.</div>";
        $_SESSION['message_type'] = "danger";
    }

    $stmt->close();
    $conn->close();
    header("Location: ../../user_admin/perkara.php");
    exit();
} else {
    $_SESSION['message'] = "<div class='text-center'>ID <b>$no_perkara</b> tidak ditemukan.</div>";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../user_admin/perkara.php");
    exit();
}
?>
