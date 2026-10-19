<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_templat = !empty($_POST['id_templat']) ? (int)$_POST['id_templat'] : null;
    $no_rujukan_kami = !empty(trim($_POST['no_rujukan_kami'])) ? trim($_POST['no_rujukan_kami']) : null;
    $tarikh_surat = $_POST['tarikh_surat'];
    $tarikh_paparan = formatTarikhMelayu($tarikh_surat);

    $panggilan_hormat_atas = trim($_POST['panggilan_hormat_atas']);
    $nama_penerima = trim($_POST['nama_penerima']);
    $jawatan_penerima = trim($_POST['jawatan_penerima']);
    $nama_agensi = trim($_POST['nama_agensi']);
    $alamat_penerima = trim($_POST['alamat_penerima']);
    $panggilan_hormat_bawah = trim($_POST['panggilan_hormat_bawah']);

    $tajuk_surat = mb_strtoupper(trim($_POST['tajuk_surat']), 'UTF-8');
    $ada_highlight_tajuk = isset($_POST['ada_highlight_tajuk']) ? 1 : 0;
    $mukaddimah = trim($_POST['mukaddimah']);

    $ayat_penutup = trim($_POST['ayat_penutup']);
    $frasa_amanah = trim($_POST['frasa_amanah']);
    $salinan_kepada = trim($_POST['salinan_kepada']);

    // Data Penandatangan Manual daripada Borang
    $manual_nama = trim($_POST['manual_nama_pegawai']);
    $manual_jawatan = trim($_POST['manual_jawatan_pegawai']);
    $manual_bahagian = trim($_POST['manual_bahagian_unit']);
    $manual_bp = trim($_POST['manual_bp_jawatan']);
    $manual_agensi = trim($_POST['manual_agensi_induk']);
    $manual_tel = trim($_POST['manual_no_tel']);
    $manual_emel = trim($_POST['manual_emel']);

    // 1. Simpan ke jadual utama (surat_rasmi)
    $stmt = $conn->prepare("INSERT INTO surat_rasmi (
        id_templat, no_rujukan_kami, tarikh_surat, tarikh_paparan,
        panggilan_hormat_atas, nama_penerima, jawatan_penerima, nama_agensi, alamat_penerima, panggilan_hormat_bawah,
        tajuk_surat, ada_highlight_tajuk, mukaddimah,
        ayat_penutup, frasa_amanah, 
        manual_nama_pegawai, manual_jawatan_pegawai, manual_bahagian_unit, manual_bp_jawatan, manual_agensi_induk, manual_no_tel, manual_emel,
        salinan_kepada
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "issssssssssisssssssssss",
        $id_templat, $no_rujukan_kami, $tarikh_surat, $tarikh_paparan,
        $panggilan_hormat_atas, $nama_penerima, $jawatan_penerima, $nama_agensi, $alamat_penerima, $panggilan_hormat_bawah,
        $tajuk_surat, $ada_highlight_tajuk, $mukaddimah,
        $ayat_penutup, $frasa_amanah,
        $manual_nama, $manual_jawatan, $manual_bahagian, $manual_bp, $manual_agensi, $manual_tel, $manual_emel,
        $salinan_kepada
    );

    if ($stmt->execute()) {
        $id_surat_baru = $conn->insert_id;

        // 2. Simpan setiap perenggan dinamik (Perenggan 2, 3, dst.)
        if (isset($_POST['teks_perenggan']) && is_array($_POST['teks_perenggan'])) {
            $stmt_butiran = $conn->prepare("INSERT INTO butiran_kandungan_surat (
                id_surat, nombor_perenggan, jenis_kandungan, teks_perenggan,
                butiran_tarikh, butiran_masa, butiran_tempat, butiran_pengerusi_atau_terlibat
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            foreach ($_POST['teks_perenggan'] as $idx => $teks) {
                $nombor_perenggan = $idx + 2; 
                $jenis = $_POST['jenis_kandungan'][$idx] ?? 'teks_biasa';
                $tarikh_b = $_POST['butiran_tarikh'][$idx] ?? null;
                $masa_b = $_POST['butiran_masa'][$idx] ?? null;
                $tempat_b = $_POST['butiran_tempat'][$idx] ?? null;
                $pengerusi_b = $_POST['butiran_pengerusi'][$idx] ?? null;

                $stmt_butiran->bind_param(
                    "iissssss",
                    $id_surat_baru, $nombor_perenggan, $jenis, $teks,
                    $tarikh_b, $masa_b, $tempat_b, $pengerusi_b
                );
                $stmt_butiran->execute();
            }
        }

        // Bawa pengguna ke paparan cetakan surat rasmi
        header("Location: cetak_surat.php?id=" . $id_surat_baru);
        exit;
    } else {
        echo "Ralat menyimpan surat: " . $stmt->error;
    }
}
?>