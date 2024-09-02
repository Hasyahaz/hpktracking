<?php
require_once('koneksi.php');

if (isset($_POST['key'])) {
    $response = array();
    $response['data'] = array(); // Initialize the "data" array

    // Handle the case for 'key' = "cuscus"
    if ($_POST['key'] == "cuscus") {
        // Initialize the base query
        $sql = "SELECT `no.wr` AS no_wr, customer, COUNT(*) AS jumlah 
                FROM tb_process";
        
        // Check if 'cari' input exists and is not empty
        if (isset($_POST['cari']) && !empty($_POST['cari'])) {
            $cari = mysqli_real_escape_string($koneksi, $_POST['cari']);
            // Add WHERE clause for search
            $sql .= " WHERE `no.wr` LIKE '%$cari%' OR customer LIKE '%$cari%'";
        }
        
        // Continue the query for grouping and sorting
        $sql .= " GROUP BY `no.wr` ORDER BY masuk DESC"; // Sort by 'masuk' column descending

        $result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
        $response['kode'] = 200;

        if (mysqli_num_rows($result) > 0) {
            // Store each row result in the "data" array
            while ($row = mysqli_fetch_assoc($result)) {
                $response['data'][] = array(
                    "no_wr" => $row["no_wr"],
                    "customer" => $row["customer"],
                    "jumlah" => $row["jumlah"]
                );
            }

            // Check if 'pilih' input exists and is numeric
            if (isset($_POST['pilih']) && is_numeric($_POST['pilih'])) {
                $pilih = intval($_POST['pilih']); // Convert 'pilih' to an integer for indexing

                // Set the output code to 100 when 'pilih' is set
                $response['kode'] = 100;

                // Check if the 'pilih' index is within the range of the data array
                if ($pilih > 0 && $pilih <= count($response['data'])) {
                    // Fetch detailed information for the selected index
                    $selectedData = $response['data'][$pilih - 1]; // Get selected item
                    $no_wr = $selectedData['no_wr'];

                    // Query to get counts of each 'posisi' and additional details for the selected 'no_wr'
                    $detailSql = "SELECT customer, spesifikasi, masuk, `no.chasis` AS no_chasis, posisi 
                                  FROM tb_process 
                                  WHERE `no.wr` = '$no_wr'";
                    $detailResult = mysqli_query($koneksi, $detailSql) or die(mysqli_error($koneksi));

                    // Initialize variables to count positions
                    $counts = array(
                        "Parts" => 0,
                        "Body" => 0,
                        "Subframe" => 0,
                        "PTO" => 0,
                        "Assy" => 0,
                        "Ketrik & Painting" => 0,
                        "QC Check" => 0,
                        "Repair" => 0,
                        "QC Final" => 0
                    );

                    $detailInfo = array(); // Array to store additional details

                    if (mysqli_num_rows($detailResult) > 0) {
                        while ($detailRow = mysqli_fetch_assoc($detailResult)) {
                            // Count positions
                            switch ($detailRow['posisi']) {
                                case 1: $counts["Parts"]++; break;
                                case 2: $counts["Body"]++; break;
                                case 3: $counts["Subframe"]++; break;
                                case 4: $counts["PTO"]++; break;
                                case 5: $counts["Assy"]++; break;
                                case 6: $counts["Ketrik & Painting"]++; break;
                                case 7: $counts["QC Check"]++; break;
                                case 8: $counts["Repair"]++; break;
                                case 9: $counts["QC Final"]++; break;
                            }

                            // Collect additional detail information
                            $detailInfo[] = array(
                                "customer" => $detailRow["customer"],
                                "spesifikasi" => $detailRow["spesifikasi"],
                                "Masuk" => $detailRow["masuk"],
                                "no_chasis" => $detailRow["no_chasis"]
                            );
                        }

                        // Update the response for the selected index with details
                        $response['data'] = array(
                            array_merge($selectedData, $counts, array("details" => $detailInfo))
                        );
                    } else {
                        $response['data'] = array(
                            array_merge($selectedData, array("message" => "Data detail tidak ditemukan"))
                        );
                    }
                } else {
                    $response['data'] = array("message" => "Index 'pilih' di luar jangkauan");
                }
            }
        } else {
            // Display a message if no data is found
            if (isset($_POST['cari']) && !empty($_POST['cari'])) {
                $response['data'][] = array("message" => "Data tidak ditemukan");
            } else {
                $response['data'][] = array("message" => "Tidak ada hasil");
            }
        }
    } else {
        $response['kode'] = 400;
        $response['data'][] = array("message" => "Key atau inputan tidak valid");
    }

    // Send response in JSON format
    echo json_encode($response);

    // Close the connection
    mysqli_close($koneksi);
}
?>
