<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPAK NUSANTARA - Edit Menu</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA - Admin</div>
            <ul class="nav-links">
                <li><a href="index.php?page=admin_menu">📋 Menu</a></li>
                <li><a href="index.php?page=admin_orders">📦 Pesanan</a></li>
                <li><a href="index.php?logout=1">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <main style="padding-top: 120px;">
        <div class="admin-container">
            <div class="admin-content">
                <h1>✏️ Edit Menu</h1>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 2px solid #ffcdd2; text-align: center;">
                        <?php echo htmlspecialchars($_SESSION['error_message']); ?>
                        <?php unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>

                <div class="form-container">
                    <form method="post" action="index.php?page=admin_menu_edit&id=<?php echo $data['id']; ?>" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <div class="form-group">
                            <label>Nama Menu:</label><br>
                            <input type="text" name="nama_menu" value="<?php echo htmlspecialchars($data['nama_menu']); ?>" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                        </div>

                        <div class="form-group">
                            <label>Kategori:</label><br>
                            <select name="kategori" required style="width: 100%; padding: 10px; margin: 5px 0;">
                                <?php
                                // Koneksi database untuk mengambil kategori
                                require_once __DIR__ . '/../config/Database.php';
                                $db = new Database();
                                $conn = $db->connect();
                                $result = $conn->query("SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
                                while ($row = $result->fetch_assoc()) {
                                    $selected = ($row['id'] == $data['category_id']) ? 'selected' : '';
                                    echo '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['name']) . '</option>';
                                }
                                ?>
                            </select><br><br>
                        </div>

                        <div class="form-group">
                            <label>Harga:</label><br>
                            <input type="number" name="harga" value="<?php echo $data['harga']; ?>" step="0.01" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                        </div>

                        <div class="form-group">
                            <label>Gambar:</label><br>
                            <input type="file" name="gambar" accept=".jpg,.jpeg,.png" style="width: 100%; padding: 10px; margin: 5px 0;"><br>
                            <?php if ($data['gambar']): ?>
                                <small>Gambar saat ini: <?php echo htmlspecialchars($data['gambar']); ?></small><br>
                            <?php endif; ?>
                            <small>Biarkan kosong jika tidak ingin mengubah gambar</small><br><br>
                        </div>

                        <div class="form-group">
                            <label>Deskripsi:</label><br>
                            <textarea name="deskripsi" rows="4" style="width: 100%; padding: 10px; margin: 5px 0;"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea><br><br>
                        </div>

                        <div class="form-actions">
                            <input type="submit" value="Update Menu" class="btn" style="background: var(--bg-button-green); color: white;">
                            <a href="index.php?page=admin_menu" class="btn" style="background: var(--bg-button-yellow); color: black; margin-left: 10px;">Kembali</a>
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