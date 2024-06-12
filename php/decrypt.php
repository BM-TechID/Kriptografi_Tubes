<?php
function decrypt($ciphertext, $key, $mode) {
    $ciphertext = base64_decode($ciphertext);
    $iv_length = openssl_cipher_iv_length($mode);

    // CFB dan CBC membutuhkan IV, ECB tidak
    if ($mode === 'aes-128-ecb') {
        $iv = "";
        $ciphertext_without_iv = $ciphertext;
    } else {
        $iv = substr($ciphertext, 0, $iv_length);
        $ciphertext_without_iv = substr($ciphertext, $iv_length);
    }

    $plaintext = openssl_decrypt($ciphertext_without_iv, $mode, $key, 0, $iv);
    return $plaintext;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ciphertext = $_POST['ciphertext'];
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

    $plaintext = decrypt($ciphertext, $key, $cipher_mode);
    echo "Plaintext: " . $plaintext;
}
?>
