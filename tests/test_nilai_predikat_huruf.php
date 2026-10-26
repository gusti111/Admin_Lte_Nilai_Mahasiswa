<?php
require_once __DIR__ . "/../nilai_predikat_huruf.php";
require_once __DIR__ . "/../_nilai_helpers.php";

$cases = [
    [0.00, ["E", "Tidak Lulus"]],
    [34.50, ["E", "Tidak Lulus"]],
    [34.51, ["D", "Kurang Memuaskan"]],
    [50.50, ["D", "Kurang Memuaskan"]],
    [50.51, ["C", "Cukup Memuaskan"]],
    [65.50, ["C", "Cukup Memuaskan"]],
    [65.51, ["B", "Memuaskan"]],
    [80.50, ["B", "Memuaskan"]],
    [80.51, ["A", "Sangat Memuaskan"]],
    [100.00, ["A", "Sangat Memuaskan"]]
];

foreach ($cases as [$score, $expected]) {
    if (predikat($score) !== $expected) {
        fwrite(STDERR, "GAGAL: nilai " . $score . PHP_EOL);
        exit(1);
    }
}

if (predikat(101) !== ["Invalid", "Invalid"]) {
    fwrite(STDERR, "GAGAL: nilai di atas 100 harus Invalid." . PHP_EOL);
    exit(1);
}
if (nilaiValidate("12345", "Gusti Faqikh", "85.25") !== []) {
    fwrite(STDERR, "GAGAL: input contoh yang valid ditolak." . PHP_EOL);
    exit(1);
}
if (!isset(nilaiValidate("12345", "Gusti", "85.255")["nilaiakhir"])) {
    fwrite(STDERR, "GAGAL: lebih dari dua angka desimal harus ditolak." . PHP_EOL);
    exit(1);
}

echo "Semua tes konversi nilai dan validasi berhasil." . PHP_EOL;
?>