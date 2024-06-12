<?php
session_start();

$ciphertext = isset($_SESSION['ciphertext']) ? $_SESSION['ciphertext'] : '';
unset($_SESSION['ciphertext']); // Hapus session setelah ditampilkan

include 'templates/form.html';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cryptik Web App</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <div class="container">
        <h1>Cryptik Web App</h1>
        <form action="encrypt.php" method="post">
            <label for="plaintext">Plaintext:</label>
            <input type="text" id="plaintext" name="plaintext" required><br>
            <div id="key-container">
                <label for="key">Key:</label>
                <input type="text" id="key" name="key" required>
            </div>
            <button type="button" id="generate-key" onclick="generateKey()">Generate Random Key</button>
            <button type="button" id="copy-key" onclick="copyKey()">Copy Key</button><br>
            <label for="mode">Mode:</label>
            <select id="mode" name="mode" required>
                <option value="ECB">ECB</option>
                <option value="CFB">CFB</option>
                <option value="CBC">CBC</option>
            </select><br>
            <button type="submit">Encrypt</button>
        </form>
        
        <?php if (!empty($ciphertext)): ?>
            <div class="container">
                <h2>Hasil Enkripsi</h2>
                <p>Ciphertext: <?= htmlspecialchars($ciphertext) ?></p>
            </div>
        <?php endif; ?>
        <br><br>

        <form action="decrypt.php" method="post">
            <label for="ciphertext">Ciphertext:</label>
            <input type="text" id="ciphertext" name="ciphertext" required><br>
            <label for="key">Key:</label>
            <input type="text" id="decrypt-key" name="key" required><br>
            <label for="mode">Mode:</label>
            <select id="decrypt-mode" name="mode" required>
                <option value="ECB">ECB</option>
                <option value="CFB">CFB</option>
                <option value="CBC">CBC</option>
            </select><br>
            <button type="submit">Decrypt</button>
        </form>
    </div>

    <script>
        function generateKey() {
            var length = 16;
            var charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            var key = "";
            for (var i = 0; i < length; i++) {
                var randomIndex = Math.floor(Math.random() * charset.length);
                key += charset[randomIndex];
            }
            document.getElementById('key').value = key;
        }

        function copyKey() {
            var keyField = document.getElementById('key');
            keyField.select();
            keyField.setSelectionRange(0, 99999); // For mobile devices
            document.execCommand('copy');
            alert("Key copied to clipboard: " + keyField.value);
        }
    </script>
</body>

</html>