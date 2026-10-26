<?php
// Fungsi menentukan nilai huruf dan predikat sesuai rentang materi.
// Batas atas dipakai secara berurutan supaya tidak ada celah nilai desimal.
function predikat($nilai) {
    if (!is_numeric($nilai) || !is_finite((float) $nilai) || $nilai < 0 || $nilai > 100) {
        return ["Invalid", "Invalid"];
    }

    if ($nilai <= 14.50) {
        return ["E", "Tidak Lulus"];
    } elseif ($nilai <= 34.50) {
        return ["D", "Kurang"];
    } elseif ($nilai <= 50.50) {
        return ["C", "Cukup"];
    } elseif ($nilai <= 65.50) {
        return ["B", "Memuaskan"];
    }

    return ["A", "Sangat Memuaskan"];
}
?>
