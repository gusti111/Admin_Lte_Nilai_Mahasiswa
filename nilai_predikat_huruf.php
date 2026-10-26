<?php
// Konversi nilai sesuai rentang materi dosen.
// Form memvalidasi masukan agar maksimal dua angka desimal.
function predikat($nilai) {
    if (!is_numeric($nilai) || !is_finite((float)$nilai) || (float)$nilai < 0 || (float)$nilai > 100) {
        return ["Invalid", "Invalid"];
    }

    $nilai = (float)$nilai;
    if ($nilai <= 34.50) {
        return ["E", "Tidak Lulus"];
    } elseif ($nilai <= 50.50) {
        return ["D", "Kurang Memuaskan"];
    } elseif ($nilai <= 65.50) {
        return ["C", "Cukup Memuaskan"];
    } elseif ($nilai <= 80.50) {
        return ["B", "Memuaskan"];
    }
    return ["A", "Sangat Memuaskan"];
}
?>