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
        $_SESSION['message'] = "ID perkara tidak valid.";
        $_SESSION['message_type'] = "danger";
        header("Location: ../../user_admin/perkara.php");
        exit();
    }

    $check_query = "SELECT COUNT(*) AS jumlah FROM data_perkara WHERE id_perkara = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("i", $id_perkara);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['jumlah'] > 0) {
        $_SESSION['message'] = "Gagal menghapus, masih ada data di perkara ini.";
        $_SESSION['message_type'] = "danger";
        $_SESSION['message_section'] = "perkara";
        header("Location: ../../user_admin/perkara.php");
        exit();
    } else {
        $delete_query = "DELETE FROM perkara WHERE id_perkara = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $id_perkara);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Perkara berhasil dihapus.";
            $_SESSION['message_type'] = "success";
            $_SESSION['message_section'] = "perkara";
            header("Location: ../../user_admin/perkara.php");
            exit();

        } else {
            $_SESSION['message'] = "Terjadi kesalahan saat menghapus perkara.";
            $_SESSION['message_type'] = "danger";
            $_SESSION['message_section'] = "perkara";
            header("Location: ../../user_admin/perkara.php");
            exit();
        }
    }
    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = "ID perkara tidak ditemukan.";
    $_SESSION['message_type'] = "danger";
    header("Location: ../../user_admin/perkara.php");
    exit();
}
?>
