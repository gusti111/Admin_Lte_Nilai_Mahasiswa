<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once __DIR__ . "/_nilai_helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php", true, 303);
    exit;
}
if (!nilaiCsrfValid($_POST["csrf_token"] ?? null)) {
    nilaiRedirect("Sesi formulir tidak valid atau telah kedaluwarsa. Muat ulang halaman dan coba kembali.", "danger");
}

require_once __DIR__ . "/connect.php";
$nim = nilaiInput($_POST["nim"] ?? "");
if ($nim === "" || !preg_match("/^[0-9]{1,30}$/D", $nim)) {
    mysqli_close($conn);
    nilaiRedirect("NIM untuk penghapusan tidak valid.", "danger");
}

try {
    $check = mysqli_prepare($conn, "SELECT COUNT(*) FROM nilaimhsw WHERE nim = ?");
    mysqli_stmt_bind_param($check, "s", $nim);
    mysqli_stmt_execute($check);
    mysqli_stmt_bind_result($check, $count);
    mysqli_stmt_fetch($check);
    mysqli_stmt_close($check);

    if ((int)$count === 0) {
        mysqli_close($conn);
        nilaiRedirect("Data nilai tidak ditemukan atau sudah dihapus.", "warning");
    }
    if ((int)$count > 1) {
        mysqli_close($conn);
        nilaiRedirect("Penghapusan dibatalkan karena ditemukan NIM duplikat. Rapikan data terlebih dahulu.", "danger");
    }

    $photoQuery = mysqli_prepare($conn, "SELECT foto FROM nilaimhsw WHERE nim = ? LIMIT 1");
    mysqli_stmt_bind_param($photoQuery, "s", $nim);
    mysqli_stmt_execute($photoQuery);
    mysqli_stmt_bind_result($photoQuery, $fotoLama);
    mysqli_stmt_fetch($photoQuery);
    mysqli_stmt_close($photoQuery);

    $stmt = mysqli_prepare($conn, "DELETE FROM nilaimhsw WHERE nim = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $nim);
    mysqli_stmt_execute($stmt);
    $deleted = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if ($deleted === 1) {
        nilaiHapusFoto(is_string($fotoLama) ? $fotoLama : null);
        nilaiRedirect("Data nilai dengan NIM " . $nim . " berhasil dihapus.", "success");
    }
    nilaiRedirect("Data nilai tidak ditemukan atau sudah dihapus.", "warning");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    if (isset($conn) && $conn instanceof mysqli) mysqli_close($conn);
    nilaiRedirect("Data nilai gagal dihapus. Periksa skema database lalu coba kembali.", "danger");
}
?>