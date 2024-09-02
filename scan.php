<?php
require_once('koneksi.php');

if (isset($_POST['key']) && $_POST['key'] === 'cuscus') {
    $response = array();

    // Check if required input parameters are set
    if (isset($_POST['no_wr']) && isset($_POST['no_chasis']) && isset($_POST['posisi'])) {
        $no_wr = $_POST['no_wr'];
        $no_chasis = $_POST['no_chasis'];
        $posisi = $_POST['posisi'];

        // Validate the 'posisi' input
        if (!is_numeric($posisi) || $posisi < 1 || $posisi > 9) {
            $response['kode'] = 400; // Bad request
            $response['message'] = 'Invalid value for posisi';
        } else {
            // Query to check if the record exists
            $check_sql = "SELECT * FROM tb_process WHERE `no.wr` = '$no_wr' AND `no.chasis` = '$no_chasis'";
            $check_result = mysqli_query($koneksi, $check_sql);

            if (mysqli_num_rows($check_result) > 0) {
                // Update the 'Posisi' column based on the input
                $update_sql = "UPDATE tb_process SET `Posisi` = '$posisi' WHERE `no.wr` = '$no_wr' AND `no.chasis` = '$no_chasis'";
                if (mysqli_query($koneksi, $update_sql)) {
                    $response['kode'] = 200; // Success
                    $response['message'] = 'Posisi updated successfully';
                } else {
                    $response['kode'] = 500; // Internal server error
                    $response['message'] = 'Failed to update Posisi';
                }
            } else {
                $response['kode'] = 404; // Not found
                $response['message'] = 'Record not found';
            }
        }
    } else {
        $response['kode'] = 400; // Bad request
        $response['message'] = 'Missing input parameters';
    }

    // Send the response in JSON format
    echo json_encode($response);

    // Close the database connection
    mysqli_close($koneksi);
}
?>
