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
$nimLama = nilaiInput($_POST["nim_lama"] ?? "");
$nim = nilaiInput($_POST["nim"] ?? "");
$namamhsw = nilaiInput($_POST["namamhsw"] ?? "");
$nilaiakhir = nilaiInput($_POST["nilaiakhir"] ?? "");
$fotoLamaForm = nilaiInput($_POST["foto_lama"] ?? "");
$values = ["nim_lama" => $nimLama, "nim" => $nim, "namamhsw" => $namamhsw, "nilaiakhir" => $nilaiakhir, "foto_lama" => $fotoLamaForm];

$errors = nilaiValidate($nim, $namamhsw, $nilaiakhir);
if ($nimLama === "" || !preg_match("/^[0-9]{1,30}$/D", $nimLama)) {
    $errors["database"] = "NIM lama tidak valid. Buka kembali formulir edit dari tabel.";
}
if ($errors) {
    mysqli_close($conn);
    nilaiSetFormError($errors, $values, "edit");
}

[$nilaihuruf, $predikatNama] = predikat((float)$nilaiakhir);
if ($nilaihuruf === "Invalid") {
    mysqli_close($conn);
    nilaiSetFormError(["nilaiakhir" => "Nilai tidak sesuai dengan rentang predikat."], $values, "edit");
}

$fotoBaru = null;
$fotoLamaDatabase = null;
try {
    $find = mysqli_prepare($conn, "SELECT nim, namamhsw, nilaiakhir, foto FROM nilaimhsw WHERE nim = ? LIMIT 1");
    mysqli_stmt_bind_param($find, "s", $nimLama);
    mysqli_stmt_execute($find);
    mysqli_stmt_bind_result($find, $currentNim, $currentNama, $currentNilai, $currentFoto);
    $found = mysqli_stmt_fetch($find);
    mysqli_stmt_close($find);

    if (!$found) {
        mysqli_close($conn);
        nilaiRedirect("Data yang akan diperbarui tidak ditemukan. Muat ulang tabel dan coba kembali.", "warning");
    }
    $fotoLamaDatabase = is_string($currentFoto) ? $currentFoto : null;

    $check = mysqli_prepare($conn, "SELECT nim FROM nilaimhsw WHERE nim = ? AND nim <> ? LIMIT 1");
    mysqli_stmt_bind_param($check, "ss", $nim, $nimLama);
    mysqli_stmt_execute($check);
    mysqli_stmt_store_result($check);
    $nimDipakai = mysqli_stmt_num_rows($check) > 0;
    mysqli_stmt_close($check);
    if ($nimDipakai) {
        mysqli_close($conn);
        nilaiSetFormError(["database" => "NIM tersebut sudah dipakai mahasiswa lain."], $values, "edit");
    }

    $fotoBaru = nilaiUploadFoto($_FILES["foto"] ?? null);
    $fotoUntukSimpan = $fotoBaru !== null ? $fotoBaru : $fotoLamaDatabase;
    $nilaiLama = number_format((float)$currentNilai, 2, ".", "");
    $nilaiBaru = number_format((float)$nilaiakhir, 2, ".", "");
    $isUnchanged = ((string)$currentNim === $nim)
        && ((string)$currentNama === $namamhsw)
        && ($nilaiLama === $nilaiBaru)
        && ($fotoBaru === null);

    if ($isUnchanged) {
        mysqli_close($conn);
        nilaiRedirect("Tidak ada perubahan data. Isian sama dengan data sebelumnya.", "info");
    }

    $sql = "UPDATE nilaimhsw
            SET nim = ?, namamhsw = ?, nilaiakhir = ?, nilaihuruf = ?, predikat = ?, foto = ?, modified_date = CURRENT_TIMESTAMP
            WHERE nim = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    $nilaiNumerik = (float)$nilaiakhir;
    mysqli_stmt_bind_param($stmt, "ssdssss", $nim, $namamhsw, $nilaiNumerik, $nilaihuruf, $predikatNama, $fotoUntukSimpan, $nimLama);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    if ($affected === 1) {
        if ($fotoBaru !== null && $fotoLamaDatabase !== null && $fotoLamaDatabase !== $fotoBaru) {
            nilaiHapusFoto($fotoLamaDatabase);
        }
        nilaiRedirect("Data nilai mahasiswa berhasil diperbarui.", "success");
    }
    if ($fotoBaru !== null) nilaiHapusFoto($fotoBaru);
    nilaiRedirect("Perubahan tidak tersimpan. Muat ulang tabel dan coba kembali.", "warning");
} catch (InvalidArgumentException $e) {
    if ($fotoBaru !== null) nilaiHapusFoto($fotoBaru);
    if (isset($conn) && $conn instanceof mysqli) mysqli_close($conn);
    nilaiSetFormError(["foto" => $e->getMessage()], $values, "edit");
} catch (NilaiPhotoStorageException $e) {
    if ($fotoBaru !== null) nilaiHapusFoto($fotoBaru);
    error_log($e->getMessage());
    if (isset($conn) && $conn instanceof mysqli) mysqli_close($conn);
    nilaiSetFormError(["foto" => "Foto tidak dapat disimpan. Periksa folder unggahan dan coba kembali."], $values, "edit");
} catch (mysqli_sql_exception $e) {
    error_log($e->getMessage());
    if ($fotoBaru !== null) nilaiHapusFoto($fotoBaru);
    if (isset($conn) && $conn instanceof mysqli) mysqli_close($conn);
    if ((int)$e->getCode() === 1062) {
        nilaiSetFormError(["database" => "NIM tersebut sudah terdaftar."], $values, "edit");
    }
    nilaiSetFormError(["database" => "Data gagal diperbarui. Pastikan skema tabel tersedia."], $values, "edit");
}
?>