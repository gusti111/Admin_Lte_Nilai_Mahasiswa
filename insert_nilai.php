<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once __DIR__ . "/connect.php";
require_once __DIR__ . "/nilai_predikat_huruf.php";
require_once __DIR__ . "/_nilai_helpers.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php");
    exit;
}

$nim = nilaiInput($_POST["nim"] ?? "");
$namamhsw = nilaiInput($_POST["namamhsw"] ?? "");
$nilaiakhir = nilaiInput($_POST["nilaiakhir"] ?? "");
$values = ["nim" => $nim, "namamhsw" => $namamhsw, "nilaiakhir" => $nilaiakhir, "nim_lama" => ""];
$errors = nilaiValidate($nim, $namamhsw, $nilaiakhir);
if ($errors) nilaiSetFormError($errors, $values, "add");

[$nilaihuruf, $predikatNama] = predikat((float)$nilaiakhir);
if ($nilaihuruf === "Invalid") nilaiSetFormError(["nilaiakhir" => "Nilai tidak sesuai dengan rentang predikat."], $values, "add");

try {
    // Pastikan NIM tidak duplikat meskipun tabel belum memiliki indeks UNIQUE.
    $cek = mysqli_prepare($conn, "SELECT nim FROM nilaimhsw WHERE nim = ? LIMIT 1");
    mysqli_stmt_bind_param($cek, "s", $nim);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);
    if (mysqli_stmt_num_rows($cek) > 0) {
        mysqli_stmt_close($cek);
        nilaiSetFormError(["database" => "NIM tersebut sudah terdaftar."], $values, "add");
    }
    mysqli_stmt_close($cek);

    $sql = "INSERT INTO nilaimhsw (nim, namamhsw, nilaiakhir, nilaihuruf, predikat) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    $nilaiNumerik = (float)$nilaiakhir;
    mysqli_stmt_bind_param($stmt, "ssdss", $nim, $namamhsw, $nilaiNumerik, $nilaihuruf, $predikatNama);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    nilaiRedirect("Data nilai mahasiswa berhasil disimpan.", "success");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    if ((int)$e->getCode() === 1062) {
        nilaiSetFormError(["database" => "NIM tersebut sudah terdaftar."], $values, "add");
    }
    nilaiSetFormError(["database" => "Data gagal disimpan. Periksa struktur tabel nilaimhsw dan koneksi database."], $values, "add");
}
?>
