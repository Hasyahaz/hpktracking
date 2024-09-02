<?php
require_once('koneksi.php');

if ((isset($_POST['key']))) {
    $response = array();
    $response['data'] = array(); // Menambahkan array "data"

    // Menginisialisasi query untuk mengambil data termasuk timestamp dari tabel "masuk"
    $sql = "SELECT `no.wr` as no_wr, customer, COUNT(*) as jumlah, `masuk` 
            FROM tb_customer 
            GROUP BY `no.wr`, `masuk`
            ORDER BY `masuk` ASC"; // Mengurutkan berdasarkan kolom 'masuk' secara ascending

    $result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

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

    // Memeriksa nilai key untuk menentukan kode respon dan proses yang dilakukan
    if ($_POST['key'] == "cuscus") {
        $response['kode'] = 200;
    } elseif ($_POST['key'] == "tampilkan" && isset($_POST['cari']) && is_numeric($_POST['cari'])) {
        $response['kode'] = 100;
        $cari = intval($_POST['cari']); // Mengonversi 'cari' ke integer untuk digunakan sebagai index

        // Mengecek apakah index berada dalam rentang array 'data'
        if ($cari > 0 && $cari <= count($response['data'])) {
            $response['data'] = array($response['data'][$cari - 1]); // Menampilkan data sesuai dengan index 'cari'
        } else {
            $response['data'] = array("message" => "Index 'cari' di luar jangkauan");
        }
    } else {
        $response['kode'] = 400;
        $response['data'][] = array("message" => "Key atau inputan tidak valid");
    }

    // Mengirimkan respon dalam format JSON
    echo json_encode($response);

    // Menutup koneksi
    mysqli_close($koneksi);
}
?>
