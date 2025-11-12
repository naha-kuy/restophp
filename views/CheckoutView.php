<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAPAK NUSANTARA - Checkout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA</div>
            <ul class="nav-links">
                <li><a href="index.php?page=home">🏠 Home</a></li>
                <li><a href="index.php?page=menu">🍽️ Menu</a></li>
                <li><a href="index.php?page=contact">📞 Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="checkout">
            <div class="checkout-header">
                <h1>🛒 Checkout Pesanan</h1>
                <p class="checkout-subtitle">Lengkapi data untuk menyelesaikan pesanan Anda</p>
            </div>

            <?php if (isset($error_message) && !empty($error_message)): ?>
                <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 2px solid #ffcdd2; text-align: center;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <div class="checkout-container">
                <div class="checkout-card">
                    <div class="form-header">
                        <h2>📝 Data Pelanggan</h2>
                    </div>


                    <form id="checkout-form" method="POST" action="index.php?page=checkout">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label for="name">👤 Nama Pelanggan:</label>
                            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap Anda" required>
                        </div>

                        <div class="form-group">
                            <label for="whatsapp">📱 Nomor WhatsApp:</label>
                            <input type="tel" id="whatsapp" name="whatsapp" placeholder="08xxxxxxxxxx" pattern="[0-9]+" title="Hanya angka yang diperbolehkan" required>
                        </div>

                        <div class="form-group">
                            <label for="address">🏠 Alamat Pengantaran:</label>
                            <textarea id="address" name="address" placeholder="Masukkan alamat lengkap untuk pengantaran" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="notes">📋 Catatan Khusus:</label>
                            <textarea id="notes" name="notes" placeholder="Tambahkan catatan khusus (opsional)"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="payment">💳 Metode Pembayaran:</label>
                            <select id="payment" name="payment" required>
                                <option value="">Pilih Metode Pembayaran</option>
                                <option value="COD">💵 COD (Tunai Langsung)</option>
                                <option value="Shopeepay">🟠 Shopeepay</option>
                                <option value="Gopay">🟢 Gopay</option>
                            </select>
                        </div>

                        <div class="checkout-actions">
                            <button type="submit" class="confirm-btn">
                                <span>✅ Konfirmasi Pesanan</span>
                            </button>
                            <a href="index.php?page=menu" class="back-btn">
                                <span>⬅️ Kembali ke Menu</span>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="checkout-info">
                    <div class="info-card">
                        <h3>🚚 Informasi Pengantaran</h3>
                        <ul>
                            <li>✅ Gratis ongkir untuk pengantaran disekitar Universitas Negeri Malang</li>
                            <li>⏰ Waktu pengantaran didiskusikan melalui WhatsApp</li>
                            <li>📞 Mari kita berinteraksi via WhatsApp</li>
                        </ul>
                    </div>

                    <div class="info-card">
                        <h3>💰 Metode Pembayaran</h3>
                        <ul>
                            <li>💵 COD: Bayar saat pesanan tiba</li>
                            <li>🟠 Shopeepay: Transfer digital</li>
                            <li>🟢 Gopay: E-wallet praktis</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA</p>
    </footer>

    <script>
        // WhatsApp number validation
        document.addEventListener('DOMContentLoaded', function() {
            const whatsappInput = document.getElementById('whatsapp');
            if (whatsappInput) {
                // Allow only numeric input
                whatsappInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

                // Prevent non-numeric characters from being typed
                whatsappInput.addEventListener('keypress', function(e) {
                    if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                        (e.keyCode === 65 && e.ctrlKey === true) ||
                        (e.keyCode === 67 && e.ctrlKey === true) ||
                        (e.keyCode === 86 && e.ctrlKey === true) ||
                        (e.keyCode === 88 && e.ctrlKey === true)) {
                        return;
                    }
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });

                // Additional validation on paste
                whatsappInput.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    }, 10);
                });
            }

            // Form validation
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    const whatsappValue = whatsappInput.value;
                    if (!/^[0-9]+$/.test(whatsappValue)) {
                        alert('Nomor WhatsApp harus berupa angka saja tanpa karakter lain!');
                        e.preventDefault();
                        return;
                    }

                    if (whatsappValue.length < 10 || whatsappValue.length > 15) {
                        alert('Nomor WhatsApp harus antara 10-15 digit!');
                        e.preventDefault();
                        return;
                    }
                });
            }
        });
    </script>
</body>
</html>