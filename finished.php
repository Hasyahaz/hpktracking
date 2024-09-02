<?php
require_once('koneksi.php');

if ((isset($_POST['key'])) && ($_POST['key'] == "cuscus")) {
    // Menginisialisasi query untuk mengambil data termasuk timestamp dari tabel "masuk"
    $sql = "SELECT `no.wr` as no_wr, customer, COUNT(*) as jumlah, `masuk` 
            FROM tb_customer 
            GROUP BY `no.wr`, `masuk`
            ORDER BY `masuk` ASC"; // Mengurutkan berdasarkan kolom 'masuk' secara descending

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
                "jumlah" => $row["jumlah"],
                "masuk" => $row["masuk"] // Menambahkan timestamp ke dalam hasil
            );
        }
    } else {
        $response['data'][] = array("message" => "Tidak ada hasil");
    }

    // Mengirimkan respon dalam format JSON
    echo json_encode($response);

    // Menutup koneksi
    mysqli_close($koneksi);
}
?>
