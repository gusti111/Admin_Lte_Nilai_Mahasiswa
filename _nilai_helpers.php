<?php
/**
 * Helper validasi, CSRF, flash message, dan unggah foto untuk Nilai Mahasiswa.
 */
class NilaiPhotoStorageException extends RuntimeException {}

function nilaiInput($value) {
    return is_string($value) || is_numeric($value) ? trim((string)$value) : "";
}

function nilaiRedirect($message, $type = "success") {
    $_SESSION["nilai_flash"] = ["message" => $message, "type" => $type];
    header("Location: users.php", true, 303);
    exit;
}

function nilaiSetFormError($errors, $values, $modal = "add") {
    $_SESSION["nilai_form_errors"] = $errors;
    $_SESSION["nilai_form_values"] = $values;
    $_SESSION["nilai_open_modal"] = $modal;
    header("Location: users.php", true, 303);
    exit;
}

function nilaiCsrfToken() {
    if (empty($_SESSION["nilai_csrf_token"]) || !is_string($_SESSION["nilai_csrf_token"])) {
        $_SESSION["nilai_csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["nilai_csrf_token"];
}

function nilaiCsrfValid($token) {
    return is_string($token)
        && $token !== ""
        && isset($_SESSION["nilai_csrf_token"])
        && is_string($_SESSION["nilai_csrf_token"])
        && hash_equals($_SESSION["nilai_csrf_token"], $token);
}

function nilaiCharacterLength($value) {
    $count = preg_match_all('/./us', (string)$value, $matches);
    return $count === false ? PHP_INT_MAX : $count;
}

function nilaiValidate($nim, $nama, $nilai) {
    $errors = [];

    if ($nim === "") {
        $errors["nim"] = "NIM tidak boleh kosong.";
    } elseif (!preg_match("/^[0-9]+$/D", $nim)) {
        $errors["nim"] = "NIM hanya boleh berisi angka.";
    } elseif (nilaiCharacterLength($nim) > 30) {
        $errors["nim"] = "NIM maksimal 30 digit.";
    }

    if ($nama === "") {
        $errors["namamhsw"] = "Nama mahasiswa tidak boleh kosong.";
    } elseif (
        !preg_match("/^[\p{L}\p{M}\p{Zs}.'-]+$/u", $nama)
        || !preg_match("/[\p{L}\p{M}]/u", $nama)
    ) {
        $errors["namamhsw"] = "Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.";
    } elseif (nilaiCharacterLength($nama) > 150) {
        $errors["namamhsw"] = "Nama mahasiswa maksimal 150 karakter.";
    }

    if ($nilai === "") {
        $errors["nilaiakhir"] = "Nilai akhir tidak boleh kosong.";
    } elseif (!preg_match('/^[0-9]{1,3}(?:\.[0-9]{1,2})?$/D', $nilai)) {
        $errors["nilaiakhir"] = "Nilai akhir harus berupa angka dengan maksimal dua angka desimal.";
    } elseif (!is_finite((float)$nilai) || (float)$nilai < 0 || (float)$nilai > 100) {
        $errors["nilaiakhir"] = "Nilai akhir harus berada pada rentang 0 sampai 100.";
    }

    return $errors;
}

/** Berkas upload memiliki nama acak 32 karakter heksadesimal; input asli tidak dipakai sebagai path. */
function nilaiFotoNamaValid($filename) {
    return is_string($filename)
        && preg_match('/^[a-f0-9]{32}\.(?:jpg|png|webp)$/D', $filename) === 1;
}

function nilaiFotoDemoMap() {
    return [
        "demo-user1.jpg" => "./assets/img/user1-128x128.jpg",
        "demo-user2.jpg" => "./assets/img/user2-160x160.jpg",
        "demo-user3.jpg" => "./assets/img/user3-128x128.jpg",
        "demo-user4.jpg" => "./assets/img/user4-128x128.jpg",
        "demo-user5.jpg" => "./assets/img/user5-128x128.jpg",
        "demo-user6.jpg" => "./assets/img/user6-128x128.jpg",
        "demo-user7.jpg" => "./assets/img/user7-128x128.jpg",
        "demo-user8.jpg" => "./assets/img/user8-128x128.jpg",
    ];
}

function nilaiFotoDisplayValid($filename) {
    return nilaiFotoNamaValid($filename)
        || array_key_exists((string)$filename, nilaiFotoDemoMap());
}

function nilaiFotoUrl($filename) {
    if (nilaiFotoNamaValid($filename)) {
        return "./uploads/foto-mahasiswa/" . rawurlencode($filename);
    }
    $demo = nilaiFotoDemoMap();
    if (isset($demo[(string)$filename])) {
        return $demo[(string)$filename];
    }
    return "./assets/img/default-150x150.png";
}

function nilaiFotoDirectory() {
    return __DIR__ . DIRECTORY_SEPARATOR . "uploads" . DIRECTORY_SEPARATOR . "foto-mahasiswa";
}

/**
 * Upload foto opsional (JPG/PNG/WEBP, 2 MiB), memeriksa MIME nyata dan dimensi gambar.
 * Return null jika file tidak dipilih.
 */
function nilaiUploadFoto($upload) {
    if (!is_array($upload) || !isset($upload["error"]) || (int)$upload["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $error = (int)$upload["error"];
    if ($error !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => "Ukuran foto melebihi batas upload server.",
            UPLOAD_ERR_FORM_SIZE => "Ukuran foto terlalu besar.",
            UPLOAD_ERR_PARTIAL => "Foto hanya terunggah sebagian. Silakan coba lagi.",
            UPLOAD_ERR_NO_TMP_DIR => "Folder sementara upload tidak tersedia.",
            UPLOAD_ERR_CANT_WRITE => "Server gagal menyimpan foto.",
            UPLOAD_ERR_EXTENSION => "Upload dihentikan oleh konfigurasi server."
        ];
        $message = $messages[$error] ?? "Foto gagal diunggah.";
        if (in_array($error, [UPLOAD_ERR_NO_TMP_DIR, UPLOAD_ERR_CANT_WRITE, UPLOAD_ERR_EXTENSION], true)) {
            throw new NilaiPhotoStorageException($message);
        }
        throw new InvalidArgumentException($message);
    }

    $tmp = (string)($upload["tmp_name"] ?? "");
    if ($tmp === "" || !is_uploaded_file($tmp)) {
        throw new InvalidArgumentException("Berkas foto tidak valid. Silakan pilih ulang.");
    }
    $size = filesize($tmp);
    if ($size === false || $size <= 0) {
        throw new InvalidArgumentException("Berkas foto kosong atau tidak dapat dibaca.");
    }
    if ($size > 2 * 1024 * 1024) {
        throw new InvalidArgumentException("Ukuran foto maksimal 2 MiB.");
    }

    if (!class_exists("finfo")) {
        throw new NilaiPhotoStorageException("Ekstensi PHP fileinfo tidak aktif di server.");
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($tmp);
    $allowed = ["image/jpeg" => "jpg", "image/png" => "png", "image/webp" => "webp"];
    if (!isset($allowed[$mime])) {
        throw new InvalidArgumentException("Format foto harus JPG, PNG, atau WEBP.");
    }
    $image = @getimagesize($tmp);
    if ($image === false || ($image["mime"] ?? "") !== $mime) {
        throw new InvalidArgumentException("Isi berkas bukan gambar yang valid.");
    }
    $width = (int)($image[0] ?? 0);
    $height = (int)($image[1] ?? 0);
    if ($width < 1 || $height < 1 || $width > 8000 || $height > 8000 || $width * $height > 25000000) {
        throw new InvalidArgumentException("Dimensi foto terlalu besar. Maksimal 8000 × 8000 piksel.");
    }

    $directory = nilaiFotoDirectory();
    if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
        throw new NilaiPhotoStorageException("Folder penyimpanan foto tidak dapat dibuat.");
    }
    $filename = bin2hex(random_bytes(16)) . "." . $allowed[$mime];
    $destination = $directory . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($tmp, $destination)) {
        throw new NilaiPhotoStorageException("Server gagal menyimpan foto. Periksa izin folder uploads/foto-mahasiswa.");
    }
    return $filename;
}

function nilaiHapusFoto($filename) {
    if (!nilaiFotoNamaValid($filename)) return;
    $path = nilaiFotoDirectory() . DIRECTORY_SEPARATOR . $filename;
    if (is_file($path) && !@unlink($path)) {
        error_log("Tidak dapat menghapus foto mahasiswa: " . $filename);
    }
}
?>