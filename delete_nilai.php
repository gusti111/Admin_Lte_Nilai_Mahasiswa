<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once __DIR__ . "/connect.php";
require_once __DIR__ . "/_nilai_helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$nim = nilaiInput($_POST["nim"] ?? "");
if ($nim === "" || !preg_match("/^[0-9]+$/", $nim)) {
    mysqli_close($conn);
    nilaiRedirect("NIM untuk penghapusan tidak valid.", "danger");
}

try {
    $stmt = mysqli_prepare($conn, "DELETE FROM nilaimhsw WHERE nim = ?");
    mysqli_stmt_bind_param($stmt, "s", $nim);
    mysqli_stmt_execute($stmt);
    $deleted = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    if ($deleted > 0) {
        nilaiRedirect("Data nilai dengan NIM " . $nim . " berhasil dihapus.", "success");
    }
    nilaiRedirect("Data nilai tidak ditemukan atau sudah dihapus.", "warning");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    nilaiRedirect("Data nilai gagal dihapus. Silakan coba kembali.", "danger");
}
?>
