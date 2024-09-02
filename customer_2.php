<?php
require_once('koneksi.php');

if (isset($_POST['key'])) {
    $response = array();
    $response['data'] = array(); // Initialize the "data" array

    // Unified query to group by 'no.wr' and select minimum 'no.chasis' and 'masuk'
    $sql = "SELECT `no.wr` as no_wr, 
                   MIN(`no.chasis`) as no_chasis, 
                   customer, 
                   COUNT(*) as jumlah, 
                   MIN(`masuk`) as masuk 
            FROM tb_customer
            GROUP BY `no.wr`, customer
            ORDER BY `no.wr` ASC"; // Order by 'no.wr' in ascending order

    $result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));

    if (mysqli_num_rows($result) > 0) {
        // Store each row of results into the "data" array
        while ($row = mysqli_fetch_assoc($result)) {
            $response['data'][] = array(
                "no_wr" => $row["no_wr"],
                "customer" => $row["customer"],
                "jumlah" => $row["jumlah"],
                "masuk" => $row["masuk"], // Add timestamp to results
                "no_chasis" => $row["no_chasis"] // Correct field name
            );
        }
    } else {
        $response['data'][] = array("message" => "Tidak ada hasil");
    }

    // Check key value to determine response code and process
    if ($_POST['key'] == "cuscus") {
        $response['kode'] = 200;
    } elseif ($_POST['key'] == "tampilkan") {
        $response['kode'] = 100;

        // Additional logic for 'tampilkan' when 'cari' is provided
        if (isset($_POST['cari']) && is_numeric($_POST['cari'])) {
            $cari = intval($_POST['cari']); // Convert 'cari' to integer for use as an index

            // Check if index is within the range of 'data' array
            if ($cari > 0 && $cari <= count($response['data'])) {
                $response['data'] = array($response['data'][$cari - 1]); // Display data according to 'cari' index
            } else {
                $response['data'] = array("message" => "Index 'cari' di luar jangkauan");
            }
        }

        // Check additional conditions for 'cetak' and 't_cetak'
        if (isset($_POST['cetak']) && $_POST['cetak'] == 'ya' && isset($_POST['t_cetak']) && is_numeric($_POST['t_cetak'])) {
            $t_cetak = intval($_POST['t_cetak']); // Convert 't_cetak' to integer

            // Initialize an array to store moved data
            $movedData = array();

            // Insert data to tb_process before deleting from tb_customer
            $insert_sql = "INSERT INTO tb_process (`no.wr`, customer, `no.chasis`, spesifikasi, masuk)
                           SELECT `no.wr`, customer, `no.chasis`, spesifikasi, masuk 
                           FROM tb_customer 
                           WHERE `no.wr` = ? AND customer = ? 
                           LIMIT ?";
            $stmt_insert = mysqli_prepare($koneksi, $insert_sql);

            foreach ($response['data'] as $item) {
                mysqli_stmt_bind_param($stmt_insert, 'ssi', $item['no_wr'], $item['customer'], $t_cetak);
                mysqli_stmt_execute($stmt_insert);

                // Store the moved data into the array
                $movedData[] = array(
                    "no_wr" => $item['no_wr'],
                    "customer" => $item['customer'],
                    "no_chasis" => $item['no_chasis']
                );
            }

            mysqli_stmt_close($stmt_insert);

            // Delete data from tb_customer
            $delete_sql = "DELETE FROM tb_customer 
                           WHERE `no.wr` = ? AND customer = ? 
                           LIMIT ?";
            $stmt_delete = mysqli_prepare($koneksi, $delete_sql);
            $response['kode'] = 150;
            foreach ($response['data'] as $item) {
                mysqli_stmt_bind_param($stmt_delete, 'ssi', $item['no_wr'], $item['customer'], $t_cetak);
                mysqli_stmt_execute($stmt_delete);
            }

            mysqli_stmt_close($stmt_delete);

            // Add moved data to the response
            $response['data'][] = array("message" => "Data berhasil dipindahkan dan dihapus", "moved_data" => $movedData);
        }
    } else {
        $response['kode'] = 400;
        $response['data'][] = array("message" => "Key atau inputan tidak valid");
    }

    // Send response in JSON format
    echo json_encode($response);

    // Close connection
    mysqli_close($koneksi);
}
?>
