<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            background: #e2e8f0;
            margin: 0;
            padding: 2rem 0;
            line-height: 1.5;
        }
        .container {
            background: #fff;
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 1cm 1cm 1cm 1.5cm;
            box-sizing: border-box;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 4px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-logo {
            width: 100px;
            height: auto;
        }
        .header-text {
            flex: 1;
            text-align: center;
            padding: 0 15px;
        }
        .header-title {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 20pt;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }
        .header-address {
            font-size: 11pt;
            margin: 0;
        }
        .content {
            font-size: 12pt;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            font-size: 14pt;
            margin: 20px 0 30px;
        }
        .table-data {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .table-data td {
            padding: 5px 0;
            vertical-align: top;
        }
        .td-label {
            width: 250px;
        }
        .td-colon {
            width: 20px;
        }
        .footer {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .signature {
            text-align: center;
            width: 300px;
        }
        .signature-date {
            margin-bottom: 5px;
            text-align: left;
            padding-left: 30px;
        }
        .signature-title {
            text-align: left;
            padding-left: 30px;
            margin-bottom: 80px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            text-align: left;
            padding-left: 30px;
        }
        .signature-nip {
            margin-top: 5px;
            text-align: left;
            padding-left: 30px;
        }
        @page {
            size: A4;
            margin: 1cm 1cm 1cm 1.5cm;
        }
        @media print {
            body { background: #fff; margin: 0; padding: 0; }
            .container {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <?php
        $logoUrl = !empty($profil['logo']) ? url($profil['logo']) : '';
        $replacements = [
            '[logo_sekolah]'     => e($logoUrl),
            '[nama_sekolah]'     => e($profil['nama_sekolah']),
            '[alamat]'           => e($profil['alamat']),
            '[website]'          => e($profil['website'] ?: '-'),
            '[email]'            => e($profil['email'] ?: '-'),
            '[telepon]'          => e($profil['telepon'] ?: '-'),
            '[tahun_pelajaran]'  => e($siswa['tahun_lulus'] - 1) . '/' . e($siswa['tahun_lulus']),
            '[nama_siswa]'       => e($siswa['nama']),
            '[tempat_lahir]'     => e($siswa['tempat_lahir']),
            '[tanggal_lahir]'    => format_date($siswa['tanggal_lahir']),
            '[nisn]'             => e($siswa['nisn']),
            '[jenis_kelamin]'    => e($siswa['jenis_kelamin'] === 'L' ? 'Laki-laki' : 'Perempuan'),
            '[jurusan]'          => e($siswa['jurusan']),
            '[status_kelulusan]' => $siswa['status_kelulusan'] === 'lulus' ? 'L U L U S' : 'T I D A K  L U L U S',
            '[nilai_rata_rata]'  => !empty($siswa['nilai_rata_rata']) ? number_format((float)$siswa['nilai_rata_rata'], 2) : '-',
            '[tanggal_surat]'    => format_date(date('Y-m-d')),
            '[kepala_sekolah]'   => e($profil['kepala_sekolah'] ?: '________________________'),
            '[nip_kepala_sekolah]'=> e($profil['nip_kepala_sekolah'] ?: '________________________'),
        ];
        
        $header = $profil['template_header'] ?? '';
        echo strtr($header, $replacements);
        ?>

        <div class="content">
            <div class="title">SURAT KETERANGAN KELULUSAN</div>

            <?php
            $template = $profil['template_surat'] ?? '';
            echo strtr($template, $replacements);
            ?>
        </div>

        <?php
        $footer = $profil['template_footer'] ?? '';
        echo strtr($footer, $replacements);
        ?>
    </div>
</body>
</html>
