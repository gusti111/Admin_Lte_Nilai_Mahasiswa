<?php
function nilaiInput($value) {
    return is_string($value) || is_numeric($value) ? trim((string)$value) : "";
}
function nilaiRedirect($message, $type = "success") {
    $_SESSION["nilai_flash"] = ["message" => $message, "type" => $type];
    header("Location: users.php");
    exit;
}
function nilaiSetFormError($errors, $values, $modal = "add") {
    $_SESSION["nilai_form_errors"] = $errors;
    $_SESSION["nilai_form_values"] = $values;
    $_SESSION["nilai_open_modal"] = $modal;
    header("Location: users.php");
    exit;
}
function nilaiValidate($nim, $nama, $nilai) {
    $errors = [];
    if ($nim === "") {
        $errors["nim"] = "NIM tidak boleh kosong.";
    } elseif (!preg_match("/^[0-9]+$/", $nim)) {
        $errors["nim"] = "NIM hanya boleh berisi angka.";
    }
    if ($nama === "") {
        $errors["namamhsw"] = "Nama mahasiswa tidak boleh kosong.";
    } elseif (!preg_match("/^[\p{L}\s.'-]+$/u", $nama)) {
        $errors["namamhsw"] = "Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.";
    }
    if ($nilai === "" || !is_numeric($nilai) || !is_finite((float)$nilai) || (float)$nilai < 0 || (float)$nilai > 100) {
        $errors["nilaiakhir"] = "Nilai akhir harus berupa angka antara 0 sampai 100.";
    }
    return $errors;
}
?>
