<?php
require_once('koneksi.php');

if ((isset($_POST['key'])) && ($_POST['key'] == "cuscus")) {
    // Menginisialisasi query dasar
    $sql = "SELECT `no.wr` as no_wr, customer, COUNT(*) as jumlah 
            FROM tb_process";
    
    // Cek apakah input 'cari' ada dan tidak kosong
    if (isset($_POST['cari']) && !empty($_POST['cari'])) {
        $cari = mysqli_real_escape_string($koneksi, $_POST['cari']);
        // Tambahkan klausa WHERE untuk pencarian
        $sql .= " WHERE `no.wr` LIKE '%$cari%' OR customer LIKE '%$cari%'";
    }
    
    // Melanjutkan query untuk grouping dan sorting
    $sql .= " GROUP BY `no.wr`
              ORDER BY masuk DESC"; // Mengurutkan berdasarkan kolom 'masuk' secara descending

    $result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

    $response = array();
    $response['kode'] = 200;
    $response['data'] = array(); // Menambahkan array "data"
    
    if (mysqli_num_rows($result) > 0) {
        // Menyimpan hasil per baris ke dalam array "data"
        while($row = mysqli_fetch_assoc($result)) {
            $response['data'][] = array(
                "no_wr" => $row["no_wr"],
                "customer" => $row["customer"],
                "jumlah" => $row["jumlah"]
            );
        }
    } else {
        // Menampilkan pesan "data tidak ditemukan" jika pencarian menghasilkan 0 baris
        if (isset($_POST['cari']) && !empty($_POST['cari'])) {
            $response['data'][] = array("message" => "Data tidak ditemukan");
        } else {
            $response['data'][] = array("message" => "Tidak ada hasil");
        }
    }

    // Mengirimkan respon dalam format JSON
    echo json_encode($response);

    // Menutup koneksi
    mysqli_close($koneksi);
}
?>
