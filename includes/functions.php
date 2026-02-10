<?php
// includes/functions.php

// 1. Fungsi Hitung Berat Badan Ideal (Broca)
if (!function_exists('hitungBBI')) {
    function hitungBBI($tb, $jk) {
        if ($jk == 'L') {
            return ($tb - 100) - (($tb - 100) * 0.10);
        } else {
            return ($tb - 100) - (($tb - 100) * 0.15);
        }
    }
}

// 2. Status IMT (Standar Kemenkes RI)

if (!function_exists('getStatusIMT')) {
    function getStatusIMT($imt) {
        if ($imt < 17.0) return "Sangat Kurus";
        if ($imt >= 17.0 && $imt < 18.5) return "Kurus";
        if ($imt >= 18.5 && $imt < 25.0) return "Normal";
        if ($imt >= 25.0 && $imt < 27.0) return "Gemuk (Overweight)";
        return "Obesitas";
    }
}

// 3. Rumus Mifflin-St Jeor (RMR)
if (!function_exists('hitungMifflin')) {
    function hitungMifflin($bb, $tb, $usia, $jk) {
        if ($jk == 'L') {
            return (10 * $bb) + (6.25 * $tb) - (5 * $usia) + 5;
        } else {
            return (10 * $bb) + (6.25 * $tb) - (5 * $usia) - 161;
        }
    }
}

// 4. Fungsi TEE (Total Energy Expenditure)
if (!function_exists('hitungTEE')) {
    function hitungTEE($rmr, $f_akt, $f_str) {
        $akt = ($f_akt <= 0) ? 1.2 : $f_akt;
        $str = ($f_str <= 0) ? 1.0 : $f_str;
        return $rmr * $akt * $str;
    }
}

// 5. Diagnosa Gizi Otomatis (Deskriptif Manusiawi)
if (!function_exists('getDiagnosisGizi')) {
    function getDiagnosisGizi($imt, $gd, $td) {
        $diagnosis = [];
        
        // Diagnosa Berat Badan
        if ($imt < 18.5) {
            $diagnosis[] = "Kondisi berat badan kurang (Malnutrisi) yang membutuhkan peningkatan asupan energi harian secara bertahap.";
        } elseif ($imt >= 25 && $imt < 27) {
            $diagnosis[] = "Kondisi berat badan berlebih (Overweight) yang memerlukan penyesuaian pola makan rendah kalori dan aktivitas fisik.";
        } elseif ($imt >= 27) {
            $diagnosis[] = "Kondisi Obesitas yang berisiko memicu komplikasi metabolik, disarankan pembatasan asupan lemak jenuh dan karbohidrat sederhana.";
        }

        // Diagnosa Gula Darah
        if ($gd > 200) {
            $diagnosis[] = "Kadar gula darah di atas batas normal (Hiperglikemia), diperlukan pembatasan konsumsi gula dan pengaturan jadwal makan yang ketat.";
        }

        // Diagnosa Tekanan Darah
        $tensi_parts = explode('/', $td);
        $sistolik = isset($tensi_parts[0]) ? (int)$tensi_parts[0] : 0;
        if ($sistolik > 130) {
            $diagnosis[] = "Tekanan darah tinggi (Hipertensi), disarankan diet rendah natrium/garam untuk menjaga stabilitas sirkulasi darah.";
        }

        return count($diagnosis) > 0 ? implode("<br><br>", $diagnosis) : "Status gizi saat ini dalam batas normal. Pertahankan pola makan gizi seimbang.";
    }
}

// 6. Fungsi Rekomendasi Diet
if (!function_exists('getRekomendasiDiet')) {
    function getRekomendasiDiet($imt, $gd, $td) {
        $diet = ["Diet Lambung / Umum"]; 
        
        if ($gd > 200) $diet[] = "Diet Diabetes Mellitus (DM)";
        if ($imt >= 25) $diet[] = "Diet Rendah Kalori";

        $tensi_parts = explode('/', $td);
        $sistolik = isset($tensi_parts[0]) ? (int)$tensi_parts[0] : 0;
        if ($sistolik > 130) $diet[] = "Diet Rendah Garam (RG)";
        
        return implode(", ", array_unique($diet));
    }
}
?>