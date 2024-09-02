<?php
require_once('koneksi.php');

$response = array();

// Mapping nilai 'posisi' ke deskripsi
$posisi_map = array(
    1 => "Parts",
    2 => "Body",
    3 => "Subframe",
    4 => "PTO",
    5 => "Assy",
    6 => "KP",
    7 => "QC Check",
    8 => "Repair",
    9 => "QC Final"
);

if (isset($_POST['check']) && in_array($_POST['check'], $posisi_map)) {
    $response['kode'] = 150;

    // Mendapatkan kunci posisi berdasarkan nilai 'check' dari input
    $posisi_key = array_search($_POST['check'], $posisi_map);

    // Query untuk mendapatkan semua data dari posisi yang sesuai dengan input 'check'
    $sql = "SELECT * FROM tb_process WHERE posisi = $posisi_key";
    $result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

    if (mysqli_num_rows($result) > 0) {
        $meta = [];

        // Menyimpan data dalam array
        while ($row = mysqli_fetch_assoc($result)) {
            $meta[] = $row;
        }

        // Mengurutkan data berdasarkan 'no.wr'
        usort($meta, function($a, $b) {
            return strcmp($a['no.wr'], $b['no.wr']);
        });

        // Menambahkan nomor urut dan ID unik berdasarkan kesamaan 'no.wr'
        $last_wr = null;
        $order_number = 0;
        $unique_id = 1; // Inisialisasi ID unik
        foreach ($meta as &$row) {
            if ($row['no.wr'] !== $last_wr) {
                $order_number = 1;
                $last_wr = $row['no.wr'];
            } else {
                $order_number++;
            }
            $row['order'] = $order_number;
            //$row['id'] = $unique_id++;  Menambahkan ID unik untuk setiap baris
            $response['meta'][] = $row;
        }
    } else {
        $response['meta'] = array("message" => "Tidak ada hasil");
    }
} else {
    $response['kode'] = 400; // Kode default jika 'check' tidak valid
    $response['meta'] = array("message" => "Invalid check");
}

// Mengirimkan respon dalam format JSON
echo json_encode($response);

// Menutup koneksi
mysqli_close($koneksi);
?>
