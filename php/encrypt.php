<?php
session_start();

function encrypt($plaintext, $key, $mode) {
    // CFB dan CBC membutuhkan IV, ECB tidak
    if ($mode === 'aes-128-ecb') {
        $iv = "";
    } else {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($mode));
    }

    $ciphertext = openssl_encrypt($plaintext, $mode, $key, 0, $iv);
    return base64_encode($iv . $ciphertext);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plaintext = $_POST['plaintext'];
    $key = $_POST['key'];
    $mode = $_POST['mode'];

    switch ($mode) {
        case 'ECB':
            $cipher_mode = 'aes-128-ecb';
            break;
        case 'CFB':
            $cipher_mode = 'aes-128-cfb';
            break;
        case 'CBC':
            $cipher_mode = 'aes-128-cbc';
            break;
        default:
            echo "Mode tidak valid!";
            exit();
    }

    $ciphertext = encrypt($plaintext, $key, $cipher_mode);

    // Simpan ciphertext ke dalam session
    $_SESSION['ciphertext'] = $ciphertext;

    // Redirect kembali ke index.php
    header("Location: index.php");
    exit();
}
?>
