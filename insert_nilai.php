<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
require_once __DIR__ . "/_nilai_helpers.php";
require_once __DIR__ . "/nilai_predikat_huruf.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: users.php", true, 303);
    exit;
}
if (!nilaiCsrfValid($_POST["csrf_token"] ?? null)) {
    nilaiRedirect("Sesi formulir tidak valid atau telah kedaluwarsa. Muat ulang halaman dan coba kembali.", "danger");
}

require_once __DIR__ . "/connect.php";

$nim = nilaiInput($_POST["nim"] ?? "");
$namamhsw = nilaiInput($_POST["namamhsw"] ?? "");
$nilaiakhir = nilaiInput($_POST["nilaiakhir"] ?? "");
$values = ["nim" => $nim, "namamhsw" => $namamhsw, "nilaiakhir" => $nilaiakhir, "nim_lama" => "", "foto_lama" => ""];
$errors = nilaiValidate($nim, $namamhsw, $nilaiakhir);
if ($errors) {
    mysqli_close($conn);
    nilaiSetFormError($errors, $values, "add");
}

[$nilaihuruf, $predikatNama] = predikat((float)$nilaiakhir);
if ($nilaihuruf === "Invalid") {
    mysqli_close($conn);
    nilaiSetFormError(["nilaiakhir" => "Nilai tidak sesuai dengan rentang predikat."], $values, "add");
}

$fotoBaru = null;
try {
    // Cek awal agar pesan NIM duplikat lebih jelas; indeks UNIQUE tetap disarankan.
    $cek = mysqli_prepare($conn, "SELECT nim FROM nilaimhsw WHERE nim = ? LIMIT 1");
    mysqli_stmt_bind_param($cek, "s", $nim);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);
    $nimAda = mysqli_stmt_num_rows($cek) > 0;
    mysqli_stmt_close($cek);
    if ($nimAda) {
        mysqli_close($conn);
        nilaiSetFormError(["database" => "NIM tersebut sudah terdaftar."], $values, "add");
    }

    $fotoBaru = nilaiUploadFoto($_FILES["foto"] ?? null);
    $sql = "INSERT INTO nilaimhsw
            (nim, namamhsw, nilaiakhir, nilaihuruf, predikat, foto, created_date, modified_date)
            VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NULL)";
    $stmt = mysqli_prepare($conn, $sql);
    $nilaiNumerik = (float)$nilaiakhir;
    mysqli_stmt_bind_param($stmt, "ssdsss", $nim, $namamhsw, $nilaiNumerik, $nilaihuruf, $predikatNama, $fotoBaru);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    nilaiRedirect("Data nilai mahasiswa berhasil disimpan.", "success");
} catch (InvalidArgumentException $e) {
    if ($fotoBaru !== null) nilaiHapusFoto($fotoBaru);
    if (isset($conn) && $conn instanceof mysqli) mysqli_close($conn);
    nilaiSetFormError(["foto" => $e->getMessage()], $values, "add");
} catch (NilaiPhotoStorageException $e) {
    if ($fotoBaru !== null) nilaiHapusFoto($fotoBaru);
    error_log($e->getMessage());
    if (isset($conn) && $conn instanceof mysqli) mysqli_close($conn);
    nilaiSetFormError(["foto" => "Foto tidak dapat disimpan. Periksa folder unggahan dan coba kembali."], $values, "add");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    if ($fotoBaru !== null) nilaiHapusFoto($fotoBaru);
    if (isset($conn) && $conn instanceof mysqli) mysqli_close($conn);
    if ((int)$e->getCode() === 1062) {
        nilaiSetFormError(["database" => "NIM tersebut sudah terdaftar."], $values, "add");
    }
    nilaiSetFormError(["database" => "Data gagal disimpan. Pastikan skema tabel telah dimigrasikan."], $values, "add");
}
?>