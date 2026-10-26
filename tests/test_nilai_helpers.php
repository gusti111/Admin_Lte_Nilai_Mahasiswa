<?php
require_once __DIR__ . "/../_nilai_helpers.php";
function assertCheck($condition, $message) {
    if (!$condition) {
        fwrite(STDERR, "GAGAL: " . $message . PHP_EOL);
        exit(1);
    }
}
assertCheck(nilaiFotoNamaValid(str_repeat("a", 32) . ".jpg"), "nama JPG upload valid");
assertCheck(!nilaiFotoNamaValid("../script.php"), "path traversal ditolak");
assertCheck(nilaiFotoDisplayValid("demo-user1.jpg"), "alias avatar demo valid");
assertCheck(nilaiFotoUrl("demo-user1.jpg") === "./assets/img/user1-128x128.jpg", "alias diarahkan ke aset AdminLTE");
assertCheck(nilaiFotoUrl("../fake.php") === "./assets/img/default-150x150.png", "foto invalid pakai fallback");
assertCheck(nilaiValidate("12345", "Gusty Faqikh", "85.50") === [], "input valid diterima");
assertCheck(isset(nilaiValidate("", "Nama", "80")["nim"]), "NIM kosong ditolak");
assertCheck(isset(nilaiValidate("123", "Nama", "100.001")["nilaiakhir"]), "desimal berlebih ditolak");
assertCheck(isset(nilaiValidate("123", "Nama", "101")["nilaiakhir"]), "nilai >100 ditolak");
assertCheck(isset(nilaiValidate("123", str_repeat("A", 151), "80")["namamhsw"]), "nama terlalu panjang ditolak");
echo "Semua tes helper nilai dan foto berhasil." . PHP_EOL;
?>