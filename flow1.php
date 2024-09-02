<?php
require_once('koneksi.php');

if (isset($_POST['key']) && $_POST['key'] === 'cuscus') {
    $response = array();
    
    // Mapping nilai 'posisi' ke deskripsi
    $posisi_map = array(
        1 => "Parts",
        2 => "Body",
        3 => "Subframe",
        4 => "PTO",
        5 => "Assy",
        6 => "Ketrik & Painting",
        7 => "QC Check",
        8 => "Repair",
        9 => "QC Final"
    );

    if (!isset($_POST['check']) || !in_array($_POST['check'], $posisi_map)) {
        $response['kode'] = 200;

        // Menginisialisasi setiap posisi dengan nilai 0
        foreach ($posisi_map as $desc) {
            $response['meta'][$desc] = 0;
        }

        // Query untuk mengambil jumlah berdasarkan posisi
        $sql = "SELECT `posisi`, COUNT(*) as jumlah FROM tb_process GROUP BY `posisi` ORDER BY `posisi` ASC";
        $result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $posisi_desc = isset($posisi_map[$row["posisi"]]) ? $posisi_map[$row["posisi"]] : "Unknown";
                $response['meta'][$posisi_desc] = $row["jumlah"];
            }
        }
    } else {
        // If 'check' is present and valid, redirect to the appropriate file
        include('handle_key_cuscus_check.php');
        exit();
    }

    // Mengirimkan respon dalam format JSON
    echo json_encode($response);

    // Menutup koneksi
    mysqli_close($koneksi);
}
?>
