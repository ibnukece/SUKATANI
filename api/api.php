<?php
include_once __DIR__ . '/koneksi.php';
/* ============================================================
   api/api.php — Handler BPS API untuk SUKATANI
   Dipanggil dari api/dashboard.php via include 'api.php'
   ============================================================ */

// ══════════════════════════════════════════════════════
//  KONFIGURASI BPS
//  Endpoint: list/model/data — var=2310 (Alat Pertanian)
//  th=126 = kode tahun
// ══════════════════════════════════════════════════════
define('BPS_API_KEY', '79049d2b76e264a8a2df2388f80213db');
define('BPS_API_URL',
    'https://webapi.bps.go.id/v1/api/list/model/data'
    . '/lang/ind/domain/0000/var/2310/th/126'
    . '/key/79049d2b76e264a8a2df2388f80213db'
);

/**
 * Ambil data alat pertanian dari BPS API (dynamic data endpoint).
 */
function getBpsAlatPertanian(): ?array {
    $ch = curl_init(BPS_API_URL);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT      => 'SUKATANI-Dashboard/1.0',
    ]);
    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if (!$response || $http_code !== 200) return null;

    $decoded = json_decode($response, true);
    if (!$decoded || ($decoded['status'] ?? '') !== 'OK') return null;

    return $decoded;
}

/**
 * Ubah respons mentah BPS menjadi array baris siap tampil.
 */
function parseBpsRows(array $raw): array {
    $rows    = [];
    $vervar  = $raw['vervar']      ?? [];
    $var     = $raw['var']         ?? [];
    $content = $raw['datacontent'] ?? [];

    if (empty($vervar) || empty($content)) return $rows;

    $unit = $var[0]['unit'] ?? '';

    foreach ($vervar as $v) {
        $prefix = (string) $v['val'];
        $found  = null;

        foreach ($content as $key => $nilai) {
            if (str_starts_with((string) $key, $prefix)) {
                $found = $nilai;
                break;
            }
        }

        if ($found === null) continue;

        $rows[] = [
            'label' => $v['label'],
            'nilai' => $found,
            'unit'  => $unit,
        ];
    }

    return $rows;
}

// ── Jalankan & siapkan variabel siap pakai untuk dashboard ──
$bps_raw    = getBpsAlatPertanian();
$bps_ok     = false;
$bps_title  = 'Data Alat Pertanian Nasional';
$bps_rows   = [];
$bps_note   = '';
$bps_satuan = '';

if ($bps_raw) {
    $bps_title  = $bps_raw['var'][0]['label'] ?? $bps_title;
    $bps_satuan = $bps_raw['var'][0]['unit']  ?? '';
    $bps_note   = $bps_raw['var'][0]['def']   ?? '';
    $bps_rows   = parseBpsRows($bps_raw);
    $bps_ok     = !empty($bps_rows);
}
