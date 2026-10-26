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

$nimLama = nilaiInput($_POST["nim_lama"] ?? "");
$nim = nilaiInput($_POST["nim"] ?? "");
$namamhsw = nilaiInput($_POST["namamhsw"] ?? "");
$nilaiakhir = nilaiInput($_POST["nilaiakhir"] ?? "");
$values = ["nim_lama" => $nimLama, "nim" => $nim, "namamhsw" => $namamhsw, "nilaiakhir" => $nilaiakhir];
$errors = nilaiValidate($nim, $namamhsw, $nilaiakhir);
if ($nimLama === "" || !preg_match("/^[0-9]+$/", $nimLama)) $errors["database"] = "NIM lama tidak valid. Buka kembali form edit dari daftar nilai.";
if ($errors) nilaiSetFormError($errors, $values, "edit");

[$nilaihuruf, $predikatNama] = predikat((float)$nilaiakhir);
if ($nilaihuruf === "Invalid") nilaiSetFormError(["nilaiakhir" => "Nilai tidak sesuai dengan rentang predikat."], $values, "edit");

try {
    // Cegah NIM baru bertabrakan sebelum menjalankan UPDATE (juga berguna bila indeks UNIQUE belum dibuat).
    $check = mysqli_prepare($conn, "SELECT nim FROM nilaimhsw WHERE nim = ? AND nim <> ? LIMIT 1");
    mysqli_stmt_bind_param($check, "ss", $nim, $nimLama);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    if (mysqli_stmt_num_rows($check) > 0) {
        mysqli_stmt_close($check);
        nilaiSetFormError(["database" => "NIM tersebut sudah dipakai mahasiswa lain."], $values, "edit");
    }
    mysqli_stmt_close($check);

    $sql = "UPDATE nilaimhsw SET nim = ?, namamhsw = ?, nilaiakhir = ?, nilaihuruf = ?, predikat = ?, modified_date = CURRENT_TIMESTAMP WHERE nim = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $nilaiNumerik = (float)$nilaiakhir;
    mysqli_stmt_bind_param($stmt, "ssdsss", $nim, $namamhsw, $nilaiNumerik, $nilaihuruf, $predikatNama, $nimLama);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    if ($affected < 0) nilaiRedirect("Perubahan data gagal disimpan.", "danger");
    nilaiRedirect("Data nilai mahasiswa berhasil diperbarui.", "success");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    if ((int)$e->getCode() === 1062) {
        nilaiSetFormError(["database" => "NIM tersebut sudah terdaftar."], $values, "edit");
    }
    nilaiSetFormError(["database" => "Data gagal diperbarui. Periksa struktur tabel nilaimhsw."], $values, "edit");
}
?>
