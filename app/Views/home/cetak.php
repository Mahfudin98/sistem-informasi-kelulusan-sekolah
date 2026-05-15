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
            background: #fff;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }
        .container {
            background: #fff;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .header {
            width: 100%;
            border-bottom: 4px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-logo {
            width: 80px;
            text-align: left;
        }
        .header-logo img {
            max-width: 80px;
            height: auto;
        }
        .header-text {
            text-align: center;
            padding: 0 10px;
        }
        .header-title {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .header-subtitle {
            font-size: 18pt;
            font-weight: bold;
            margin: 2px 0;
            text-transform: uppercase;
        }
        .header-address {
            font-size: 10pt;
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
            width: 200px;
        }
        .td-colon {
            width: 20px;
        }
        .footer-table {
            width: 100%;
            margin-top: 30px;
        }
        .signature-cell {
            width: 40%;
            text-align: left;
            padding-left: 50px;
        }
        .signature-date {
            margin-bottom: 5px;
        }
        .signature-title {
            margin-bottom: 60px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-nip {
            margin-top: 5px;
        }
        @page {
            size: A4;
            margin: 2cm;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $logoUrl = '';
        $logoPath = !empty($profil['logo']) ? PUBLIC_PATH . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $profil['logo']) : '';
        
        if ($logoPath && file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoUrl = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }

        $replacements = [
            '[logo_sekolah]'     => $logoUrl,
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
