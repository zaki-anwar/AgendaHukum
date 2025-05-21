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

    $check_query = "SELECT COUNT(*) AS jumlah FROM data_perkara WHERE id_data = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id_data);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['jumlah'] == 0) {
        $_SESSION['message'] = "Data perkara tidak ditemukan.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../../user_admin/perkara.php");
        exit();
    }

    $delete_query = "DELETE FROM data_perkara WHERE id_data = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $id_data);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data perkara berhasil dihapus.";
        $_SESSION['message_type'] = "success";
        $_SESSION['message_section'] = "data_perkara";


    } else {
        $_SESSION['message'] = "Terjadi kesalahan saat menghapus data perkara.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: ../../user_admin/perkara.php");
    exit();
} else {
    $_SESSION['message'] = "ID data perkara tidak ditemukan.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../user_admin/perkara.php");
    exit();
}
?>
