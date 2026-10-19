<?php
require_once 'db.php';

$templat_res =$conn->query("SELECT * FROM templat_surat");
$sejarah_res =$conn->query("SELECT id_surat, tajuk_surat, nama_penerima, tarikh_paparan FROM surat_rasmi ORDER BY id_surat DESC LIMIT 8");
?>
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Penjana Surat Rasmi MKN / NACSA</title>
    <style>
        :root {
            --primary: #111c44;
            --accent: #2b6cb0;
            --bg: #f0f4f8;
            --card-bg: #ffffff;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg);
            margin: 0;
            padding: 25px 15px;
            color: #2d3748;
        }
        .container { max-width: 850px; margin: 0 auto; }
        .header-banner {
            background: var(--primary);
            color: #fff;
            padding: 24px 30px;
            border-radius: 10px 10px 0 0;
            border-bottom: 5px solid #ecc94b;
        }
        .header-banner h1 { margin: 0 0 6px 0; font-size: 21px; }
        .header-banner p { margin: 0; opacity: 0.9; font-size: 13.5px; }
        
        .card {
            background: var(--card-bg);
            padding: 24px 30px;
            margin-bottom: 18px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.06);
            border-left: 5px solid var(--accent);
        }
        .card.first-card { border-radius: 0 0 8px 8px; }
        .card h3 {
            margin-top: 0;
            color: var(--primary);
            font-size: 16.5px;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: 600; margin-bottom: 5px; font-size: 13.5px; }
        .hint { font-size: 12px; color: #718096; margin-top: -2px; margin-bottom: 6px; }
        input[type="text"], input[type="date"], select, textarea {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #cbd5e0;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: inherit;
        }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .perenggan-box {
            background: #f8fafc;
            border: 1px solid #cbd5e0;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .perenggan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .toolbar { display: flex; gap: 8px; margin-bottom: 8px; flex-wrap: wrap; }
        .btn-tool {
            background: #edf2f7;
            border: 1px solid #cbd5e0;
            padding: 5px 11px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-tool:hover { background: #e2e8f0; }
        .btn-tool.highlight { background: #fefcbf; border-color: #ecc94b; }
        .btn-remove {
            background: #fed7d7;
            color: #c53030;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }
        .btn-add {
            background: #ebf8ff;
            color: var(--accent);
            border: 1px dashed var(--accent);
            padding: 12px;
            width: 100%;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }
        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 28px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        .btn-submit:hover { background: #1e2e6e; }
        .jadual-sub {
            background: #ffffff;
            border: 1px solid #cbd5e0;
            padding: 12px;
            border-radius: 6px;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-banner">
        <div>
            <h1>Sistem Borang Surat Rasmi (MKN / NACSA)</h1>
            <p>Isi butiran di bawah — Sistem akan jana surat mengikut format rasmi MKN & DKN 2026-2030.</p>
        </div>
    </div>

    <form action="simpan_surat.php" method="POST">
        
        <!-- 1. RUJUKAN & TARIKH -->
        <div class="card first-card">
            <h3>1. Rujukan & Tarikh Surat</h3>
            <div class="row-2">
                <div class="form-group">
                    <label>Ruj. Kami (Biarkan kosong untuk isi manual)</label>
                    <input type="text" name="no_rujukan_kami" placeholder="Contoh: MKN.500-8/9/1 (   ) atau biarkan kosong">
                </div>
                <div class="form-group">
                    <label>Tarikh Surat *</label>
                    <input type="date" name="tarikh_surat" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>
        </div>

        <!-- 2. PENERIMA SURAT -->
        <div class="card">
            <h3>2. Maklumat Penerima (Bahagian Atas Kiri)</h3>
            <div class="form-group">
                <label>Baris Pertama Penerima (Akan di-BOLD) *</label>
                <input type="text" name="nama_penerima" placeholder="Contoh: Penyelaras Latihan Industri" required>
            </div>

            <div class="row-2">
                <div class="form-group">
                    <label>Jawatan / Fakulti / Bahagian (Baris Ke-2)</label>
                    <input type="text" name="jawatan_penerima" placeholder="Contoh: Fakulti Teknologi Maklumat dan Komunikasi">
                </div>
                <div class="form-group">
                    <label>Nama Agensi / Universiti (Baris Ke-3)</label>
                    <input type="text" name="nama_agensi" placeholder="Contoh: Universiti Teknikal Malaysia Melaka (UTeM)">
                </div>
            </div>

            <div class="form-group">
                <label>Alamat Penerima *</label>
                <textarea name="alamat_penerima" rows="2" placeholder="Hang Tuah Jaya&#10;76100 Durian Tunggal, Melaka" required></textarea>
            </div>

            <div class="row-2">
                <div class="form-group">
                    <label>Panggilan Hormat Sebelum Tajuk *</label>
                    <select name="panggilan_hormat_bawah" required>
                        <option value="Tuan/Puan,">Tuan/Puan,</option>
                        <option value="YBhg. Datuk/Dato'/Tuan/Puan,">YBhg. Datuk/Dato'/Tuan/Puan,</option>
                        <option value="YBhg. Tan Sri/Datuk/Dato',">YBhg. Tan Sri/Datuk/Dato',</option>
                        <option value="Tuan,">Tuan,</option>
                        <option value="Puan,">Puan,</option>
                        <option value="Saudara/Saudari,">Saudara/Saudari,</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Panggilan Hormat Atas (Pilihan)</label>
                    <input type="text" name="panggilan_hormat_atas" placeholder="Biarkan kosong jika tiada">
                </div>
            </div>
        </div>

        <!-- 3. TAJUK & MUKADDIMAH -->
        <div class="card">
            <h3>3. Tajuk & Mukaddimah Surat</h3>
            <div class="form-group">
                <label>Tajuk Surat (Automatik HURUF BESAR & BOLD) *</label>
                <textarea name="tajuk_surat" rows="2" placeholder="PEMBERITAHUAN PERMOHONAN MENJALANI LATIHAN INDUSTRI DI AGENSI KESELAMATAN SIBER NEGARA (NACSA)..." required></textarea>
                <label style="font-weight:normal; margin-top:6px;">
                    <input type="checkbox" name="ada_highlight_tajuk" value="1"> 🖍️ Highlight kuning pada tajuk surat
                </label>
            </div>

            <div class="form-group">
                <label>Mukaddimah (Perenggan Pertama Tanpa Nombor) *</label>
                <textarea name="mukaddimah" rows="2" required>Dengan hormatnya saya diarah merujuk kepada perkara tersebut di atas.</textarea>
            </div>
        </div>

        <!-- 4. ISI PERENGGAN DINAMIK -->
        <div class="card">
            <h3>4. Kandungan Perenggan Surat (Perenggan 2, 3, dan seterusnya)</h3>
            <div class="hint" style="margin-bottom:14px;">
                Tip: <i>Highlight</i> perkataan dan klik butang <b>[B Bold]</b>, <b>[<u>U</u> Garis Bawah]</b>, atau <b>[🖍️ Highlight]</b>.
            </div>

            <div id="senarai-perenggan">
                <div class="perenggan-box">
                    <div class="perenggan-header">
                        <strong class="label-perenggan">Perenggan #2</strong>
                        <select name="jenis_kandungan[]" onchange="tukarJenisPerenggan(this)" style="width:auto; padding:5px 10px;">
                            <option value="teks_biasa">Perenggan Teks Biasa</option>
                            <option value="jadual_butiran">Perenggan + Jadual (Tarikh / Masa / Tempat / Siapa)</option>
                        </select>
                    </div>

                    <div class="toolbar">
                        <button type="button" class="btn-tool" onclick="formatTeks(this, 'b')"><b>B</b> Bold</button>
                        <button type="button" class="btn-tool" onclick="formatTeks(this, 'u')"><u>U</u> Garis Bawah</button>
                        <button type="button" class="btn-tool highlight" onclick="formatTeks(this, 'mark')">🖍️ Highlight Kuning</button>
                    </div>

                    <textarea name="teks_perenggan[]" rows="4" placeholder="Contoh: Sukacita dimaklumkan bahawa..." required></textarea>

                    <div class="jadual-sub">
                        <div class="row-2">
                            <div class="form-group">
                                <label>Tarikh</label>
                                <input type="text" name="butiran_tarikh[]" placeholder="Contoh: 28 September 2026 (Isnin)">
                            </div>
                            <div class="form-group">
                                <label>Masa</label>
                                <input type="text" name="butiran_masa[]" placeholder="Contoh: 9.00 pagi">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tempat / Lokasi</label>
                            <input type="text" name="butiran_tempat[]" placeholder="Contoh: Aras 3, NACSA Cyberjaya">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Pegawai / Siapa Terlibat / Sasaran</label>
                            <input type="text" name="butiran_pengerusi[]" placeholder="Contoh: Pasukan Audit Pematuhan NACSA">
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn-add" onclick="tambahPerenggan()">+ Tambah Perenggan Seterusnya</button>
        </div>

        <!-- 5. PENUTUP & TANDATANGAN MANUAL -->
        <div class="card">
            <h3>5. Ayat Penutup & Butiran Pegawai Penandatangan</h3>
            <div class="form-group">
                <label>Ayat Penutup (Sebelum "Sekian, terima kasih.")</label>
                <textarea name="ayat_penutup" rows="2">Kerjasama dan perhatian Tuan/Puan mengenai perkara ini amatlah dihargai.</textarea>
            </div>

            <div class="row-2">
                <div class="form-group">
                    <label>Nama Pegawai Penandatangan *</label>
                    <input type="text" name="manual_nama_pegawai" placeholder="Contoh: MUHAMAD HAFIZ BIN AZMAN" required>
                </div>
                <div class="form-group">
                    <label>Jawatan Pegawai (Jika ada)</label>
                    <input type="text" name="manual_jawatan_pegawai" placeholder="Contoh: Ketua Unit Audit / Kosongkan jika tiada">
                </div>
            </div>

            <div class="row-2">
                <div class="form-group">
                    <label>Bahagian / Unit Pegawai *</label>
                    <input type="text" name="manual_bahagian_unit" value="Unit Pematuhan Khas Audit (BAP)" required>
                </div>
                <div class="form-group">
                    <label>Atas Perintah (b.p.) *</label>
                    <input type="text" name="manual_bp_jawatan" value="b.p. Ketua Eksekutif NACSA" required>
                </div>
            </div>

            <div class="form-group">
                <label>Agensi Induk / Jabatan *</label>
                <textarea name="manual_agensi_induk" rows="2" required>Agensi Keselamatan Siber Negara (NACSA)
Majlis Keselamatan Negara</textarea>
            </div>

            <div class="row-2">
                <div class="form-group">
                    <label>No. Telefon Pegawai</label>
                    <input type="text" name="manual_no_tel" placeholder="Contoh: 03-8872 6830">
                </div>
                <div class="form-group">
                    <label>Emel Pegawai</label>
                    <input type="text" name="manual_emel" placeholder="Contoh: hafizazman@mkn.gov.my">
                </div>
            </div>

            <div class="form-group" style="margin-top: 15px;">
                <label>Frasa Amanah</label>
                <input type="text" name="frasa_amanah" value="Saya yang menjalankan amanah,">
            </div>

            <div class="form-group">
                <label>Salinan Kepada / s.k. (Jika ada)</label>
                <textarea name="salinan_kepada" rows="2" placeholder="Biarkan kosong jika tiada"></textarea>
            </div>
        </div>

        <button type="submit" class="btn-submit">Jana Surat Rasmi MKN / NACSA Sekarang ➔</button>
    </form>

    <?php if($sejarah_res &&$sejarah_res->num_rows > 0): ?>
    <div class="card" style="margin-top: 25px; border-left-color: #38a169;">
        <h3>Rekod Surat Yang Telah Dijana</h3>
        <ul style="padding-left: 20px; margin: 0;">
            <?php while($s =$sejarah_res->fetch_assoc()): ?>
                <li style="margin-bottom: 8px;">
                    <a href="cetak_surat.php?id=<?= $s['id_surat'] ?>" target="_blank" style="color: var(--accent); font-weight: 600; text-decoration: none;">
                        [Buka Surat #<?= $s['id_surat'] ?>] <?= htmlspecialchars(mb_strimwidth($s['tajuk_surat'], 0, 65, '...')) ?>
                    </a>
                    <span style="font-size: 12px; color: #718096;"> — <?= htmlspecialchars($s['nama_penerima']) ?> (<?=$s['tarikh_paparan'] ?>)</span>
                </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<script>
function tukarJenisPerenggan(selectElem) {
    const box = selectElem.closest('.perenggan-box');
    const sub = box.querySelector('.jadual-sub');
    sub.style.display = (selectElem.value === 'jadual_butiran') ? 'block' : 'none';
}

function formatTeks(btn, tag) {
    const box = btn.closest('.perenggan-box');
    const textarea = box.querySelector('textarea');
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);

    if (!selectedText) {
        alert("Sila highlight / pilih perkataan di dalam kotak teks terlebih dahulu.");
        return;
    }

    const replacement = `<${tag}>${selectedText}</${tag}>`;
    textarea.value = textarea.value.substring(0, start) + replacement + textarea.value.substring(end);
}

function tambahPerenggan() {
    const container = document.getElementById('senarai-perenggan');
    const kiraanSemasa = container.querySelectorAll('.perenggan-box').length;
    const nomborPerenggan = kiraanSemasa + 2;

    const div = document.createElement('div');
    div.className = 'perenggan-box';
    div.innerHTML = `
        <div class="perenggan-header">
            <strong class="label-perenggan">Perenggan #${nomborPerenggan}</strong>
            <div style="display:flex; gap:10px; align-items:center;">
                <select name="jenis_kandungan[]" onchange="tukarJenisPerenggan(this)" style="width:auto; padding:5px 10px;">
                    <option value="teks_biasa">Perenggan Teks Biasa</option>
                    <option value="jadual_butiran">Perenggan + Jadual (Tarikh / Masa / Tempat / Siapa)</option>
                </select>
                <button type="button" class="btn-remove" onclick="buangPerenggan(this)">✕ Padam</button>
            </div>
        </div>

        <div class="toolbar">
            <button type="button" class="btn-tool" onclick="formatTeks(this, 'b')"><b>B</b> Bold</button>
            <button type="button" class="btn-tool" onclick="formatTeks(this, 'u')"><u>U</u> Garis Bawah</button>
            <button type="button" class="btn-tool highlight" onclick="formatTeks(this, 'mark')">🖍️ Highlight Kuning</button>
        </div>

        <textarea name="teks_perenggan[]" rows="4" placeholder="Masukkan isi perenggan #${nomborPerenggan}..." required></textarea>

        <div class="jadual-sub">
            <div class="row-2">
                <div class="form-group">
                    <label>Tarikh</label>
                    <input type="text" name="butiran_tarikh[]" placeholder="Contoh: 28 September 2026 (Isnin)">
                </div>
                <div class="form-group">
                    <label>Masa</label>
                    <input type="text" name="butiran_masa[]" placeholder="Contoh: 9.00 pagi">
                </div>
            </div>
            <div class="form-group">
                <label>Tempat / Lokasi</label>
                <input type="text" name="butiran_tempat[]" placeholder="Contoh: Aras 3, NACSA Cyberjaya">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>Pegawai / Siapa Terlibat / Sasaran</label>
                <input type="text" name="butiran_pengerusi[]" placeholder="Contoh: Pasukan Audit Pematuhan NACSA">
            </div>
        </div>
    `;
    container.appendChild(div);
    kemaskiniNomborPerenggan();
}

function buangPerenggan(btn) {
    btn.closest('.perenggan-box').remove();
    kemaskiniNomborPerenggan();
}

function kemaskiniNomborPerenggan() {
    const boxes = document.querySelectorAll('#senarai-perenggan .perenggan-box');
    boxes.forEach((box, idx) => {
        box.querySelector('.label-perenggan').textContent = `Perenggan #${idx + 2}`;
    });
}
</script>

</body>
</html>