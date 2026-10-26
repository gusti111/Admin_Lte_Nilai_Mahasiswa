<?php
// Koneksi database tugas phph_db.
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db5a";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Koneksi database gagal. Periksa MySQL, database db5a, dan konfigurasi connect.php.");
}
?>
