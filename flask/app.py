from flask import Flask, request, render_template
from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
from cryptography.hazmat.primitives import padding
from cryptography.hazmat.backends import default_backend
import base64
import os

app = Flask(__name__)  # Inisialisasi aplikasi Flask

def validate_key(key):
    # Memvalidasi panjang kunci
    if len(key) != 16:
        raise ValueError("Invalid key length. Key length should be 16 bytes (128 bits).")

def encrypt(plaintext, key, mode):
    validate_key(key)  # Memvalidasi panjang kunci
    iv = os.urandom(16)  # Menghasilkan IV acak untuk setiap enkripsi
    if mode == 'CBC':
        cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
    elif mode == 'CFB':
        cipher = Cipher(algorithms.AES(key), modes.CFB(iv), backend=default_backend())
    elif mode == 'ECB':
        cipher = Cipher(algorithms.AES(key), modes.ECB(), backend=default_backend())
    else:
        raise ValueError("Invalid mode. Supported modes: CBC, CFB, ECB")

    encryptor = cipher.encryptor()  # Membuat objek enkriptor
    padder = padding.PKCS7(128).padder()  # Menyiapkan padding data
    padded_data = padder.update(plaintext.encode()) + padder.finalize()  # Melakukan padding pada plaintext
    ciphertext = encryptor.update(padded_data) + encryptor.finalize()  # Mengenkripsi data yang sudah dipadding
    return base64.b64encode(iv + ciphertext).decode()  # Menggabungkan IV dan ciphertext, lalu meng-encode dengan base64

def decrypt(ciphertext, key, mode):
    validate_key(key)  # Memvalidasi panjang kunci
    decoded_ciphertext = base64.b64decode(ciphertext)  # Mendekode ciphertext dari base64
    iv = decoded_ciphertext[:16]  # Mengambil IV dari ciphertext
    ciphertext = decoded_ciphertext[16:]  # Mengambil ciphertext asli
    if mode == 'CBC':
        cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
    elif mode == 'CFB':
        cipher = Cipher(algorithms.AES(key), modes.CFB(iv), backend=default_backend())
    elif mode == 'ECB':
        cipher = Cipher(algorithms.AES(key), modes.ECB(), backend=default_backend())
    else:
        raise ValueError("Invalid mode. Supported modes: CBC, CFB, ECB")

    decryptor = cipher.decryptor()  # Membuat objek dekriptor
    padded_data = decryptor.update(ciphertext) + decryptor.finalize()  # Mendekripsi data
    unpadder = padding.PKCS7(128).unpadder()  # Menyiapkan unpadding data
    plaintext = unpadder.update(padded_data) + unpadder.finalize()  # Menghilangkan padding dari data yang didekripsi
    return plaintext.decode()  # Mengembalikan plaintext asli

@app.route('/')
def index():
    return render_template('index.html')  # Menampilkan halaman utama

@app.route('/encrypt', methods=['POST'])
def encrypt_message():
    plaintext = request.form['plaintext']  # Mengambil plaintext dari form
    key = request.form['key'].encode()  # Mengambil kunci dari form
    mode = request.form['mode']  # Mengambil mode enkripsi dari form
    ciphertext = encrypt(plaintext, key, mode)  # Mengenkripsi pesan
    return render_template('index.html', result=ciphertext, operation='Enkripsi')  # Menampilkan hasil enkripsi

@app.route('/decrypt', methods=['POST'])
def decrypt_message():
    ciphertext = request.form['ciphertext']  # Mengambil ciphertext dari form
    key = request.form['key'].encode()  # Mengambil kunci dari form
    mode = request.form['mode']  # Mengambil mode dekripsi dari form
    plaintext = decrypt(ciphertext, key, mode)  # Mendekripsi pesan
    return render_template('index.html', result=plaintext, operation='Dekripsi')  # Menampilkan hasil dekripsi

if __name__ == '__main__':
    app.run(debug=True)  # Menjalankan aplikasi Flask dalam mode debug
