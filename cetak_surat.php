<?php
require_once 'db.php';

$id_surat = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM surat_rasmi WHERE id_surat = ?");
$stmt->bind_param("i", $id_surat);
$stmt->execute();
$surat = $stmt->get_result()->fetch_assoc();

if (!$surat) {
    die("Rekod surat tidak dijumpai.");
}

$stmt_b = $conn->prepare("SELECT * FROM butiran_kandungan_surat WHERE id_surat = ? ORDER BY nombor_perenggan ASC");
$stmt_b->bind_param("i", $id_surat);
$stmt_b->execute();
$perenggan_list = $stmt_b->get_result()->fetch_all(MYSQLI_ASSOC);

// Cari gambar di root atau folder assets/
function cariGambar($senarai_nama) {
    foreach ($senarai_nama as $nama) {
        if (file_exists($nama)) return $nama;
        if (file_exists('assets/' . $nama)) return 'assets/' . $nama;
    }
    return '';
}

$path_jata = cariGambar(['logo_mkn.png', 'logo_mkn.jpg', 'jata_negara.png', 'logo mkn - majlis kselamatan negara - png format.png', 'logo mkn - majlis kselamatan negara - jpg format.jpg']);
$path_dkn = cariGambar(['logo_dkn.png', 'unnamed.png', 'logo_dsn.png']);

// Fungsi Auto-Trim Ruang Putih Gambar & Laras Saiz Tepat (Supaya tidak penyek & selari dalam Browser/Word)
function imejTightBase64($path, $targetW = 0, $targetH = 0) {
    if (!$path || !file_exists($path)) return '';
    $data = file_get_contents($path);
    
    if (function_exists('imagecreatefromstring')) {
        $src = @imagecreatefromstring($data);
        if ($src !== false) {
            $w = imagesx($src);
            $h = imagesy($src);
            
            // Letak atas kanvas putih untuk kesan sempadan sebenar logo
            $canvas = imagecreatetruecolor($w, $h);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagealphablending($canvas, true);
            imagecopy($canvas, $src, 0, 0, 0, 0, $w, $h);
            
            $minX = $w; $minY = $h; $maxX = 0; $maxY = 0;
            $step = max(1, (int)floor(min($w, $h) / 300));
            
            for ($y = 0; $y < $h; $y += $step) {
                for ($x = 0; $x < $w; $x += $step) {
                    $rgb = imagecolorat($canvas, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    // Jika bukan warna putih kosong
                    if ($r < 242 || $g < 242 || $b < 242) {
                        if ($x < $minX) $minX = $x;
                        if ($x > $maxX) $maxX = $x;
                        if ($y < $minY) $minY = $y;
                        if ($y > $maxY) $maxY = $y;
                    }
                }
            }
            
            if ($maxX > $minX && $maxY > $minY) {
                $pad = 0;
                $minX = max(0, $minX - $pad);
                $minY = max(0, $minY - $pad);
                $maxX = min($w - 1, $maxX + $pad);
                $maxY = min($h - 1, $maxY + $pad);
                
                $cropW = $maxX - $minX + 1;
                $cropH = $maxY - $minY + 1;
                
                // Jika saiz sasaran ditetapkan, skala pada resolusi tinggi (3x) mengikut nisbah tepat surat rasmi
                $outW = ($targetW > 0) ? $targetW * 3 : $cropW;
                $outH = ($targetH > 0) ? $targetH * 3 : $cropH;
                
                $cropped = imagecreatetruecolor($outW, $outH);
                $white2 = imagecolorallocate($cropped, 255, 255, 255);
                imagefill($cropped, 0, 0, $white2);
                imagecopyresampled($cropped, $canvas, 0, 0, $minX, $minY, $outW, $outH, $cropW, $cropH);
                
                ob_start();
                imagepng($cropped);
                $pngData = ob_get_clean();
                
                imagedestroy($src);
                imagedestroy($canvas);
                imagedestroy($cropped);
                
                return 'data:image/png;base64,' . base64_encode($pngData);
            }
            imagedestroy($src);
            imagedestroy($canvas);
        }
    }
    
    $type = pathinfo($path, PATHINFO_EXTENSION);
    return 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// Kunci nisbah saiz Jata Negara (115x95) dan DKN (105x68) supaya sebiji seperti surat asal MKN
$base64_jata = imejTightBase64($path_jata, 115, 95);
$base64_dkn = imejTightBase64($path_dkn, 105, 68);

function formatTeksSelamat($teks) {
    $teks_escaped = htmlspecialchars($teks, ENT_QUOTES, 'UTF-8');
    $teks_escaped = str_replace(['&lt;b&gt;', '&lt;/b&gt;'], ['<b>', '</b>'], $teks_escaped);
    $teks_escaped = str_replace(['&lt;u&gt;', '&lt;/u&gt;'], ['<u>', '</u>'], $teks_escaped);
    $teks_escaped = str_replace(['&lt;mark&gt;', '&lt;/mark&gt;'], ['<mark style="background-color:#fef08a; padding:0 2px;">', '</mark>'], $teks_escaped);
    return nl2br($teks_escaped);
}
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <title>Surat Rasmi MKN - <?= htmlspecialchars($surat['nama_penerima']) ?></title>
    <style>
        @page { size: A4; margin: 14mm 18mm 18mm 20mm; }
        body { background: #4a5568; margin: 0; padding: 20px 0; font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.35; color: #000; }
        .action-bar { max-width: 210mm; margin: 0 auto 15px auto; display: flex; justify-content: space-between; align-items: center; background: #111c44; padding: 12px 20px; border-radius: 8px; color: #fff; box-sizing: border-box; }
        .btn-action { background: #ecc94b; color: #1a202c; border: none; padding: 8px 15px; border-radius: 5px; font-weight: bold; cursor: pointer; text-decoration: none; font-size: 13px; margin-left: 6px; }
        .btn-action.secondary { background: #edf2f7; }
        .kertas-a4 { background: #fff; width: 210mm; min-height: 297mm; margin: 0 auto; padding: 14mm 18mm 18mm 20mm; box-sizing: border-box; box-shadow: 0 4px 15px rgba(0,0,0,0.35); }
        
        @media print {
            body { background: #fff; padding: 0; }
            .action-bar { display: none !important; }
            .kertas-a4 { box-shadow: none; width: 100%; min-height: auto; padding: 0; margin: 0; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <div>
        <strong>Pratonton Surat Rasmi MKN / NACSA</strong>
        <span style="font-size:12px; opacity:0.85; display:block;">Susunan Header telah diselaraskan mengikut format rasmi MKN.</span>
    </div>
    <div>
        <a href="index.php" class="btn-action secondary">← Borang Baru</a>
        <button onclick="exportKeWord()" class="btn-action secondary">📄 Muat Turun Word (.doc)</button>
        <button onclick="window.print()" class="btn-action">🖨️ Cetak / Simpan PDF</button>
    </div>
</div>

<!-- KERTAS A4 -->
<div class="kertas-a4" id="kandungan-surat" contenteditable="true" spellcheck="false">

    <!-- HEADER RASMI MKN: 100% Mengikut Nisbah & Alignment Surat Sebenar -->
    <table width="100%" cellpadding="0" cellspacing="0" style="border-bottom: 1.5px solid #000; margin-bottom: 6px; font-family: Arial, sans-serif;">
        <tr>
            <!-- 1. KIRI: Logo Jata Negara (Saiz 115x95: Selari tepat dari baris 'MAJLIS' hingga 'MALAYSIA') -->
            <td width="122" valign="top" align="left" style="padding-top: 1px; padding-bottom: 8px;">
                <?php if ($base64_jata): ?>
                    <img src="<?= $base64_jata ?>" width="115" height="95" style="width: 115px; height: 95px; display: block; margin: 0;">
                <?php else: ?>
                    <div style="font-size:9px; border:1px dashed red; padding:5px;">[Tiada logo_mkn]</div>
                <?php endif; ?>
            </td>
            
            <!-- 2. TENGAH: Alamat MKN (6 Baris Tetap, Tanpa Terpotong 'Putra', Sebaris dengan Puncak Logo) -->
            <td valign="top" align="left" style="padding-left: 8px; padding-top: 0px; padding-bottom: 8px; font-size: 10pt; line-height: 1.22; color: #000;">
                <div style="font-weight: bold; font-size: 10pt; white-space: nowrap; margin: 0;">MAJLIS KESELAMATAN NEGARA</div>
                <div style="white-space: nowrap; margin: 0;">Jabatan Perdana Menteri</div>
                <div style="white-space: nowrap; margin: 0;">Aras G &amp; LG, Blok Barat, Bangunan Perdana Putra</div>
                <div style="white-space: nowrap; margin: 0;">Pusat Pentadbiran Kerajaan Persekutuan</div>
                <div style="white-space: nowrap; margin: 0;">62502 PUTRAJAYA</div>
                <div style="white-space: nowrap; margin: 0;">MALAYSIA</div>
            </td>
            
            <!-- 3. KANAN: Logo DKN (Sebaris di atas) + Tel/Faks/Portal Rasmi/Emel (Rapat ke garisan bawah) -->
            <td width="185" valign="top" align="right" style="padding-top: 1px; padding-bottom: 2px;">
                <table width="185" cellpadding="0" cellspacing="0" align="right" style="font-family: Arial, sans-serif;">
                    <tr>
                        <!-- Logo DKN berada tepat di atas bahagian nombor telefon/laman web -->
                        <td align="right" style="padding-right: 6px; padding-bottom: 3px;">
                            <?php if ($base64_dkn): ?>
                                <img src="<?= $base64_dkn ?>" width="105" height="68" style="width: 105px; height: 68px; display: block; margin: 0;">
                            <?php else: ?>
                                <div style="font-size:9px; border:1px dashed red; padding:5px;">[Tiada logo_dkn]</div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td align="left">
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 7pt; line-height: 1.15; color: #000; font-family: Arial, sans-serif;">
                                <tr>
                                    <td width="58" valign="top" style="white-space: nowrap;">Tel</td>
                                    <td width="6" valign="top">:</td>
                                    <td valign="top" style="white-space: nowrap;">03-8872 6817</td>
                                </tr>
                                <tr>
                                    <td valign="top" style="white-space: nowrap;">Faks</td>
                                    <td valign="top">:</td>
                                    <td valign="top" style="white-space: nowrap;">03-8888 3021</td>
                                </tr>
                                <tr>
                                    <td valign="top" style="white-space: nowrap;">Portal Rasmi</td>
                                    <td valign="top">:</td>
                                    <td valign="top" style="white-space: nowrap;">www.mkn.gov.my</td>
                                </tr>
                                <tr>
                                    <td valign="top" style="white-space: nowrap;">Emel</td>
                                    <td valign="top">:</td>
                                    <td valign="top" style="white-space: nowrap;">webmaster@mkn.gov.my</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- RUJUKAN KAMI & TARIKH -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 18px; margin-top: 4px; font-family: Arial, sans-serif;">
        <tr>
            <td width="55%"></td>
            <td width="45%">
                <table width="100%" cellpadding="1" cellspacing="0" style="font-size: 10.5pt; color: #000;">
                    <tr>
                        <td width="72" style="font-style: italic; white-space: nowrap;">Ruj Kami:</td>
                        <td><?= !empty($surat['no_rujukan_kami']) ? htmlspecialchars($surat['no_rujukan_kami']) : '' ?></td>
                    </tr>
                    <tr>
                        <td style="font-style: italic; white-space: nowrap;">Tarikh :</td>
                        <td><?= htmlspecialchars($surat['tarikh_paparan']) ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- MAKLUMAT PENERIMA -->
    <div style="margin-bottom: 16px; line-height: 1.3;">
        <?php if (!empty($surat['panggilan_hormat_atas'])): ?>
            <div><?= htmlspecialchars($surat['panggilan_hormat_atas']) ?></div>
        <?php endif; ?>
        <div><strong><?= htmlspecialchars($surat['nama_penerima']) ?></strong></div>
        <?php if (!empty($surat['jawatan_penerima'])): ?>
            <div><?= htmlspecialchars($surat['jawatan_penerima']) ?></div>
        <?php endif; ?>
        <?php if (!empty($surat['nama_agensi'])): ?>
            <div><?= htmlspecialchars($surat['nama_agensi']) ?></div>
        <?php endif; ?>
        <div><?= nl2br(htmlspecialchars($surat['alamat_penerima'])) ?></div>
    </div>

    <div style="margin-bottom: 14px;">
        <?= htmlspecialchars($surat['panggilan_hormat_bawah']) ?>
    </div>

    <!-- TAJUK SURAT -->
    <div style="font-weight: bold; text-transform: uppercase; text-align: justify; margin-bottom: 14px; line-height: 1.3;">
        <?php if ($surat['ada_highlight_tajuk']): ?>
            <mark style="background-color:#fef08a; padding: 0 2px;"><?= nl2br(htmlspecialchars($surat['tajuk_surat'])) ?></mark>
        <?php else: ?>
            <?= nl2br(htmlspecialchars($surat['tajuk_surat'])) ?>
        <?php endif; ?>
    </div>

    <!-- MUKADDIMAH -->
    <div style="text-align: justify; margin-bottom: 13px;">
        <?= formatTeksSelamat($surat['mukaddimah']) ?>
    </div>

    <!-- PERENGGAN DINAMIK -->
    <?php foreach ($perenggan_list as $p): ?>
        <div style="text-align: justify; margin-bottom: 13px;">
            <?= $p['nombor_perenggan'] ?>.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= formatTeksSelamat($p['teks_perenggan']) ?>
        </div>

        <?php if ($p['jenis_kandungan'] === 'jadual_butiran'): ?>
            <table width="85%" cellpadding="3" cellspacing="0" style="margin-left: 38px; margin-bottom: 15px; font-weight: bold;">
                <?php if (!empty($p['butiran_tarikh'])): ?>
                <tr>
                    <td width="135" valign="top">Tarikh</td>
                    <td width="15" valign="top">:</td>
                    <td valign="top"><?= htmlspecialchars($p['butiran_tarikh']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($p['butiran_masa'])): ?>
                <tr>
                    <td valign="top">Masa</td>
                    <td valign="top">:</td>
                    <td valign="top"><?= htmlspecialchars($p['butiran_masa']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($p['butiran_tempat'])): ?>
                <tr>
                    <td valign="top">Tempat</td>
                    <td valign="top">:</td>
                    <td valign="top"><?= htmlspecialchars($p['butiran_tempat']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($p['butiran_pengerusi_atau_terlibat'])): ?>
                <tr>
                    <td valign="top">Pegawai / Sasaran</td>
                    <td valign="top">:</td>
                    <td valign="top"><?= htmlspecialchars($p['butiran_pengerusi_atau_terlibat']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>

    <!-- PENUTUP & SLOGAN -->
    <?php if (!empty($surat['ayat_penutup'])): ?>
        <div style="text-align: justify; margin-bottom: 13px;">
            <?= formatTeksSelamat($surat['ayat_penutup']) ?>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 14px;">Sekian, terima kasih.</div>

    <div style="font-weight: bold; margin-bottom: 14px; line-height: 1.35;">
        <div>"MALAYSIA MADANI"</div>
        <div style="margin-top: 10px;">"BERKHIDMAT UNTUK NEGARA"</div>
        <div style="margin-top: 10px;">"KESELAMATAN NEGARA TANGGUNGJAWAB BERSAMA"</div>
    </div>

    <div style="margin-bottom: 50px;">
        <?= htmlspecialchars($surat['frasa_amanah']) ?>
    </div>

    <!-- TANDATANGAN MANUAL -->
    <div style="line-height: 1.3;">
        <div><strong>(<?= htmlspecialchars($surat['manual_nama_pegawai']) ?>)</strong></div>
        <?php if (!empty($surat['manual_jawatan_pegawai'])): ?>
            <div><?= htmlspecialchars($surat['manual_jawatan_pegawai']) ?></div>
        <?php endif; ?>
        <?php if (!empty($surat['manual_bahagian_unit'])): ?>
            <div><?= htmlspecialchars($surat['manual_bahagian_unit']) ?></div>
        <?php endif; ?>
        <?php if (!empty($surat['manual_bp_jawatan'])): ?>
            <div><?= htmlspecialchars($surat['manual_bp_jawatan']) ?></div>
        <?php endif; ?>
        <?php if (!empty($surat['manual_agensi_induk'])): ?>
            <div><?= nl2br(htmlspecialchars($surat['manual_agensi_induk'])) ?></div>
        <?php endif; ?>

        <?php if (!empty($surat['manual_no_tel']) || !empty($surat['manual_emel'])): ?>
            <table cellpadding="0" cellspacing="0" style="font-size: 9pt; font-style: italic; margin-top: 2px;">
                <?php if (!empty($surat['manual_no_tel'])): ?>
                <tr>
                    <td width="35">Tel</td>
                    <td width="10">:</td>
                    <td><?= htmlspecialchars($surat['manual_no_tel']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($surat['manual_emel'])): ?>
                <tr>
                    <td>Emel</td>
                    <td>:</td>
                    <td><?= htmlspecialchars($surat['manual_emel']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        <?php endif; ?>
    </div>

    <?php if (!empty($surat['salinan_kepada'])): ?>
        <div style="margin-top: 20px; font-size: 10pt;">
            <strong>s.k.:</strong><br>
            <?= nl2br(htmlspecialchars($surat['salinan_kepada'])) ?>
        </div>
    <?php endif; ?>

</div>

<script>
function exportKeWord() {
    const header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' " +
        "xmlns:w='urn:schemas-microsoft-com:office:word' " +
        "xmlns='http://www.w3.org/TR/REC-html40'>" +
        "<head><meta charset='utf-8'><title>Surat Rasmi MKN</title></head><body>";
    const footer = "</body></html>";
    const sourceHTML = header + document.getElementById("kandungan-surat").innerHTML + footer;
    
    const blob = new Blob(['\ufeff', sourceHTML], { type: 'application/msword' });
    const url = URL.createObjectURL(blob);
    
    const fileDownload = document.createElement("a");
    document.body.appendChild(fileDownload);
    fileDownload.href = url;
    fileDownload.download = 'Surat_Rasmi_MKN_<?= $id_surat ?>.doc';
    fileDownload.click();
    
    document.body.removeChild(fileDownload);
    URL.revokeObjectURL(url);
}
</script>

</body>
</html>