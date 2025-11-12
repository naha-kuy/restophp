<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPAK NUSANTARA - Edit Pesanan</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA - Admin</div>
            <ul class="nav-links">
                <li><a href="index.php?page=admin_menu">📋 Menu</a></li>
                <li><a href="index.php?page=admin_orders" class="active">📦 Pesanan</a></li>
                <li><a href="index.php?logout=1">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <main style="padding-top: 120px;">
        <div class="admin-container">
            <div class="admin-content">
                <h1>✏️ Edit Pesanan</h1>
                <div class="form-container">
                    <form method="post" action="index.php?page=admin_order_edit&id=<?php echo $order['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label>Nama Pelanggan:</label><br>
                            <input type="text" name="customer_name" value="<?php echo htmlspecialchars($order['customer_name']); ?>" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                        </div>

                        <div class="form-group">
                            <label>Nomor WhatsApp:</label><br>
                            <input type="tel" name="whatsapp" value="<?php echo htmlspecialchars($order['whatsapp']); ?>" pattern="[0-9]+" title="Hanya angka yang diperbolehkan" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                        </div>

                        <div class="form-group">
                            <label>Alamat Pengantaran:</label><br>
                            <textarea name="address" rows="3" required style="width: 100%; padding: 10px; margin: 5px 0;"><?php echo htmlspecialchars($order['address']); ?></textarea><br><br>
                        </div>

                        <div class="form-group">
                            <label>Metode Pembayaran:</label><br>
                            <select name="payment_method" required style="width: 100%; padding: 10px; margin: 5px 0;">
                                <option value="COD" <?php echo ($order['payment_method'] == 'COD') ? 'selected' : ''; ?>>💵 COD (Tunai Langsung)</option>
                                <option value="Shopeepay" <?php echo ($order['payment_method'] == 'Shopeepay') ? 'selected' : ''; ?>>🟠 Shopeepay</option>
                                <option value="Gopay" <?php echo ($order['payment_method'] == 'Gopay') ? 'selected' : ''; ?>>🟢 Gopay</option>
                            </select><br><br>
                        </div>

                        <div class="form-group">
                            <label>Catatan Khusus:</label><br>
                            <textarea name="notes" rows="3" style="width: 100%; padding: 10px; margin: 5px 0;"><?php echo htmlspecialchars($order['notes'] ?? ''); ?></textarea><br><br>
                        </div>

                        <div class="form-actions">
                            <input type="submit" value="Update Pesanan" class="btn" style="background: var(--bg-button-green); color: white;">
                            <a href="index.php?page=admin_orders" class="btn" style="background: var(--bg-button-yellow); color: black; margin-left: 10px;">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA - Admin Panel</p>
    </footer>

    <style>
        .admin-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .admin-content {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 2px solid var(--primary-yellow);
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            color: var(--dark-green);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 16px;
            transition: var(--transition-fast);
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
        }

        .form-actions {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</body>
</html>