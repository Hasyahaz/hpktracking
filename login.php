<?php require_once('koneksi.php');

if ((isset($_POST['key'])) && ($_POST['key'] == "formLogin")) {
    $loginUsername = $_POST['username'];
    $password = $_POST['password'];

    $LoginRS__query = sprintf(
        "SELECT `username`, `password`,`Posisi`,`fullname`,`level` FROM `tb_users` WHERE `username`=%s AND `password`=%s",
        app($koneksi, $loginUsername, "text"),
        app($koneksi, $password, "text")
    );
    $LoginRS = mysqli_query($koneksi, $LoginRS__query) or die(mysqli_error($koneksi));
    $row_rs_LoginRS = mysqli_fetch_assoc($LoginRS);
    $loginFoundUser = mysqli_num_rows($LoginRS);

    if ($loginFoundUser) {
        $response['kode'] = 200;
        $response['pesan'] = "Data ditemukan";
        $response['username'] = $row_rs_LoginRS['username'];
        $response['fullname'] = $row_rs_LoginRS['fullname'];
        $response['level'] = $row_rs_LoginRS ['level'];
        $response['Posisi'] = $row_rs_LoginRS ['Posisi'];
        
    } else {
        $response['kode'] = 404;
        $response['pesan'] = "Anda belum terdaftar";
    }

    echo json_encode($response);
    mysqli_close($koneksi);
}