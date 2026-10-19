<?php
$host = 'localhost';
$user = 'root';      // Default user Laragon
$pass = '';          // Default password Laragon (kosong)
$dbname = 'nacsa';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Sambungan ke pangkalan data gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Fungsi bantu untuk tukar tarikh biasa ke format Bahasa Melayu (Contoh: 16 Oktober 2026)
function formatTarikhMelayu($tarikh) {
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Mac', 4 => 'April',
        5 => 'Mei', 6 => 'Jun', 7 => 'Julai', 8 => 'Ogos',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Disember'
    ];
    $pecah = explode('-', $tarikh);
    if (count($pecah) == 3) {
        $hari = (int)$pecah[2];
        $idx_bulan = (int)$pecah[1];
        $tahun = $pecah[0];
        return $hari . ' ' . $bulan[$idx_bulan] . ' ' . $tahun;
    }
    return $tarikh;
}
?>