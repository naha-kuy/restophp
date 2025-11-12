<?php
// Admin Layout - Contains header, footer, and main content area
$currentPage = $_GET['page'] ?? 'admin_dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPAK NUSANTARA - Admin Panel</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA - Admin</div>
            <div style="display: flex; align-items: center; gap: 20px;">
                <span style="color: white;">👨‍💼 Admin Panel</span>
                <a href="index.php?logout=1" style="color: #ffc107; text-decoration: none; font-weight: bold;">🚪 Logout</a>
            </div>
        </nav>
    </header>

    <main style="padding-top: 120px;">
        <div class="admin-container">
            <!-- Admin Sidebar -->
            <div class="admin-sidebar">
                <h3>📊 Menu Admin</h3>
                <ul>
                    <li><a href="index.php?page=admin_dashboard" class="<?php echo ($currentPage === 'admin_dashboard') ? 'active' : ''; ?>">📋 Dashboard</a></li>
                    <li><a href="index.php?page=admin_menu_create">➕ Tambah Menu</a></li>
                </ul>
            </div>

            <!-- Admin Main Content -->
            <div class="admin-content">
                <?php if ($currentPage === 'admin_dashboard'): ?>
                    <!-- Dashboard Content -->
                    <h1>📊 Admin Dashboard</h1>

                    <!-- Menu Management Section -->
                    <div class="dashboard-section">
                        <h2>🍽️ Manajemen Menu</h2>
                        <a href="index.php?page=admin_menu_create" class="btn" style="margin-bottom: 20px;">➕ Tambah Menu Baru</a>

                        <div class="table-responsive">
                            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                                <tr style="background-color: var(--primary-green); color: white;">
                                    <th>ID</th>
                                    <th>Nama Menu</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Gambar</th>
                                    <th>Aksi</th>
                                </tr>
                                <?php
                                if (isset($menus) && $menus) {
                                    while($row = $menus->fetch_assoc()){
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['nama_menu']); ?></td>
                                            <td><?php echo htmlspecialchars($row['kategori']); ?></td>
                                            <td>Rp <?php echo number_format($row['harga']); ?></td>
                                            <td>
                                                <?php if ($row['gambar']): ?>
                                                    <img src="<?php echo htmlspecialchars($row['gambar']); ?>" alt="Menu" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    No Image
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="index.php?page=admin_menu_edit&id=<?php echo $row['id']; ?>" style="color: #2196F3; margin-right: 10px;">✏️ Edit</a>
                                                <a href="index.php?page=admin_menu_delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus menu ini?')" style="color: #f44336;">🗑️ Delete</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align: center;'>Tidak ada data menu.</td></tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>

                    <!-- Orders Management Section -->
                    <div class="dashboard-section">
                        <h2>📦 Daftar Pesanan</h2>

                        <div class="table-responsive">
                            <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                                <tr style="background-color: var(--primary-green); color: white;">
                                    <th>ID</th>
                                    <th>Pelanggan</th>
                                    <th>WhatsApp</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                                <?php
                                if (isset($orders) && $orders) {
                                    while($row = $orders->fetch_assoc()){
                                        $statusColor = '';
                                        switch($row['status']) {
                                            case 'Dipesan': $statusColor = '#ff9800'; break;
                                            case 'Diproses': $statusColor = '#2196f3'; break;
                                            case 'Selesai': $statusColor = '#4caf50'; break;
                                        }
                                        ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                            <td><?php echo htmlspecialchars($row['whatsapp']); ?></td>
                                            <td>Rp <?php echo number_format($row['total_amount']); ?></td>
                                            <td>
                                                <form method="POST" action="index.php?page=admin_order_status" style="display: inline;">
                                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                                    <select name="status" onchange="this.form.submit()" style="background: <?php echo $statusColor; ?>; color: white; border: none; padding: 5px; border-radius: 3px;">
                                                        <option value="Dipesan" <?php echo ($row['status'] === 'Dipesan') ? 'selected' : ''; ?>>Dipesan</option>
                                                        <option value="Diproses" <?php echo ($row['status'] === 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                                        <option value="Selesai" <?php echo ($row['status'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <a href="index.php?page=admin_print_receipt&order_id=<?php echo $row['id']; ?>" target="_blank" style="color: #4caf50; margin-right: 10px;">🖨️ Cetak</a>
                                                <a href="index.php?page=admin_menu_delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus pesanan ini?')" style="color: #f44336;">🗑️ Hapus</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' style='text-align: center;'>Tidak ada data pesanan.</td></tr>";
                                }
                                ?>
                            </table>
                        </div>
                    </div>

                <?php elseif ($currentPage === 'admin_menu_create'): ?>
                    <!-- Create Menu Form -->
                    <h1>➕ Tambah Menu Baru</h1>
                    <div class="form-container">
                        <form method="post" action="index.php?page=admin_menu_create" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Nama Menu:</label><br>
                                <input type="text" name="nama_menu" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                            </div>

                            <div class="form-group">
                                <label>Kategori:</label><br>
                                <select name="kategori" required style="width: 100%; padding: 10px; margin: 5px 0;">
                                    <option value="Makanan">Makanan</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="Snack">Snack</option>
                                </select><br><br>
                            </div>

                            <div class="form-group">
                                <label>Harga:</label><br>
                                <input type="number" name="harga" step="0.01" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                            </div>

                            <div class="form-group">
                                <label>Gambar (URL):</label><br>
                                <input type="text" name="gambar" placeholder="image/nama_file.jpg" style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi:</label><br>
                                <textarea name="deskripsi" rows="4" style="width: 100%; padding: 10px; margin: 5px 0;"></textarea><br><br>
                            </div>

                            <div class="form-actions">
                                <input type="submit" value="Simpan Menu" class="btn" style="background: var(--bg-button-green); color: white;">
                                <a href="index.php?page=admin_dashboard" class="btn" style="background: var(--bg-button-yellow); color: black; margin-left: 10px;">Kembali</a>
                            </div>
                        </form>
                    </div>

                <?php elseif ($currentPage === 'admin_menu_edit' && isset($data)): ?>
                    <!-- Edit Menu Form -->
                    <h1>✏️ Edit Menu</h1>
                    <div class="form-container">
                        <form method="post" action="index.php?page=admin_menu_edit&id=<?php echo $data['id']; ?>">
                            <div class="form-group">
                                <label>Nama Menu:</label><br>
                                <input type="text" name="nama_menu" value="<?php echo htmlspecialchars($data['nama_menu']); ?>" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                            </div>

                            <div class="form-group">
                                <label>Kategori:</label><br>
                                <select name="kategori" required style="width: 100%; padding: 10px; margin: 5px 0;">
                                    <option value="Makanan" <?php echo ($data['kategori'] == 'Makanan') ? 'selected' : ''; ?>>Makanan</option>
                                    <option value="Minuman" <?php echo ($data['kategori'] == 'Minuman') ? 'selected' : ''; ?>>Minuman</option>
                                    <option value="Snack" <?php echo ($data['kategori'] == 'Snack') ? 'selected' : ''; ?>>Snack</option>
                                </select><br><br>
                            </div>

                            <div class="form-group">
                                <label>Harga:</label><br>
                                <input type="number" name="harga" value="<?php echo $data['harga']; ?>" step="0.01" required style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                            </div>

                            <div class="form-group">
                                <label>Gambar (URL):</label><br>
                                <input type="text" name="gambar" value="<?php echo htmlspecialchars($data['gambar']); ?>" style="width: 100%; padding: 10px; margin: 5px 0;"><br><br>
                            </div>

                            <div class="form-group">
                                <label>Deskripsi:</label><br>
                                <textarea name="deskripsi" rows="4" style="width: 100%; padding: 10px; margin: 5px 0;"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea><br><br>
                            </div>

                            <div class="form-actions">
                                <input type="submit" value="Update Menu" class="btn" style="background: var(--bg-button-green); color: white;">
                                <a href="index.php?page=admin_dashboard" class="btn" style="background: var(--bg-button-yellow); color: black; margin-left: 10px;">Kembali</a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA - Admin Panel</p>
    </footer>

    <style>
        .admin-container {
            display: flex;
            gap: 30px;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .admin-sidebar {
            flex: 0 0 250px;
            background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
            padding: 20px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 2px solid var(--primary-yellow);
            height: fit-content;
        }

        .admin-sidebar h3 {
            color: var(--dark-green);
            margin-bottom: 15px;
            text-align: center;
        }

        .admin-sidebar ul {
            list-style: none;
            padding: 0;
        }

        .admin-sidebar li {
            margin-bottom: 10px;
        }

        .admin-sidebar a {
            display: block;
            padding: 10px 15px;
            text-decoration: none;
            color: var(--dark-green);
            border-radius: var(--radius-md);
            transition: var(--transition-fast);
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: var(--primary-green);
            color: white;
        }

        .admin-content {
            flex: 1;
            background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
            padding: 30px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 2px solid var(--primary-yellow);
        }

        .dashboard-section {
            margin-bottom: 40px;
        }

        .dashboard-section h2 {
            color: var(--dark-green);
            margin-bottom: 20px;
            border-bottom: 2px solid var(--primary-yellow);
            padding-bottom: 10px;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table-responsive table {
            min-width: 800px;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-actions {
            text-align: center;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .admin-container {
                flex-direction: column;
            }

            .admin-sidebar {
                flex: none;
            }

            .admin-content {
                padding: 20px;
            }
        }
    </style>
</body>
</html>