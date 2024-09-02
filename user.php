<?php
require_once('koneksi.php');

//INSERT DATA
if ((isset($_POST['MM_insert'])) && ($_POST['MM_insert'] == "oiaya")) {

    $insertSQL = sprintf(
        "INSERT INTO `tb_users` (`username`, `password`, `fullname`, `level`) VALUES (%s, %s, %s, %s)",
        app($koneksi, $_POST['username'], "text"),
        app($koneksi, $_POST['password'], "text"),
        app($koneksi, $_POST['fullname'], "text"),
        app($koneksi, $_POST['level'], "text")
    );

    $Result1 = mysqli_query($koneksi, $insertSQL) or die(mysqli_error($koneksi));


    if ($Result1) {
        $response['kode'] = 1;
        $response['pesan'] = "Data berhasil disimpan";
    } else {
        $response['kode'] = 0;
        $response['pesan'] = "Data gagal disimpan";
    }

    echo json_encode($response);
    mysqli_close($koneksi);
}


//VIEW DATA
if ((isset($_GET['MM_view'])) && ($_GET['MM_view'] == "oiaya")) {

    $query = "SELECT * FROM tb_users";
    $data = mysqli_query($koneksi, $query) or die(mysqli_error($koneksi));
    $rs_data = mysqli_fetch_assoc($data);
    $ResultData = mysqli_num_rows($data);

    if ($ResultData > 0) {

        $response['kode'] = 1;
        $response['pesan'] = "Data Tersedia";
        $response['data'] = array();
        foreach ($data as $user) {
            $arr['id'] = $user['id'];
            $arr['username'] = $user['username'];
            $arr['password'] = $user['password'];
            $arr['fullname'] = $user['fullname'];
            array_push($response['data'], $arr);
        }
    } else {
        $response['kode'] = 0;
        $response['pesan'] = "Data tidak ditemukan!";
    }

    echo json_encode($response);
    mysqli_close($koneksi);
}



//EDIT DATA
if ((isset($_POST['MM_update'])) && ($_POST['MM_update'] == "oiaya")) {

    $id = $_POST['id'];
    $cari_query = sprintf(
        "SELECT id FROM tb_users WHERE id=%s",
        app($koneksi, $id, "int")
    );
    $cari = mysqli_query($koneksi, $cari_query) or die(mysqli_error($koneksi));
    $ResultCari = mysqli_num_rows($cari);

    if ($ResultCari > 0) {
        $updateSQL = sprintf(
            "UPDATE `tb_users` SET `username`=%s,`password`=%s,`level`=%s,`fullname`=%s WHERE `id`=%s",
            app($koneksi, $_POST['username'], "text"),
            app($koneksi, $_POST['password'], "text"),
            app($koneksi, $_POST['level'], "text"),
            app($koneksi, $_POST['fullname'], "text"),
            app($koneksi, $_POST['id'], "int")
        );

        $Result1 = mysqli_query($koneksi, $updateSQL) or die(mysqli_error($koneksi));

        if ($Result1) {
            $response['kode'] = 1;
            $response['pesan'] = "Data berhasil diubah";
        }
    } else {
        $response['kode'] = 0;
        $response['pesan'] = "ID tidak ditemukan!";
    }
    echo json_encode($response);
    mysqli_close($koneksi);
}

//DELETE DATA
if ((isset($_POST['MM_delete'])) && ($_POST['MM_delete'] == "oiaya")) {

    $id = $_POST['id'];
    $cari_query = sprintf(
        "SELECT id FROM tb_users WHERE id=%s",
        app($koneksi, $id, "int")
    );
    $cari = mysqli_query($koneksi, $cari_query) or die(mysqli_error($koneksi));
    $ResultCari = mysqli_num_rows($cari);

    if ($ResultCari > 0) {

        $deleteSQL = sprintf(
            "DELETE FROM `tb_users` WHERE id = %s",
            app($koneksi, $_POST['id'], "int")
        );

        $Result1 = mysqli_query($koneksi, $deleteSQL) or die(mysqli_error($koneksi));

        if ($Result1) {
            $response['kode'] = 1;
            $response['pesan'] = "Data berhasil dihapus!";
        }
    } else {
        $response['kode'] = 0;
        $response['pesan'] = "ID tidak ditemukan!";
    }


    echo json_encode($response);
    mysqli_close($koneksi);
}