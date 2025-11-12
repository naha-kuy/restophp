<?php
// View untuk halaman Manajemen Menu Admin
$currentPage = 'admin_menu';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPAK NUSANTARA - Admin Menu Management</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA - Admin</div>
            <ul class="nav-links">
                <li><a href="index.php?page=admin_menu" class="active">📋 Menu</a></li>
                <li><a href="index.php?page=admin_orders">📦 Pesanan</a></li>
                <li><a href="index.php?logout=1">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <main style="padding-top: 120px;">
        <div class="admin-container">
            <div class="admin-content">
                <h1>🍽️ Manajemen Menu</h1>

                <div class="action-buttons" style="margin-bottom: 20px;">
                    <a href="index.php?page=admin_menu_create" class="btn" style="background: var(--bg-button-green); color: white;">➕ Tambah Menu Baru</a>
                </div>

                <div class="table-responsive">
                    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                        <tr style="background-color: var(--primary-green); color: white;">
                            <th>No</th>
                            <th>Nama Menu</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Gambar</th>
                            <th>Aksi</th>
                        </tr>
                        <?php
                        $no = 1;
                        if (isset($menus) && $menus) {
                            while($row = $menus->fetch_assoc()){
                                ?>
                                <tr>
                                    <td><?php echo $no; ?></td>
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
                                        <div class="action-buttons">
                                            <a href="index.php?page=admin_menu_edit&id=<?php echo $row['id']; ?>" class="action-btn edit-btn">✏️ Edit</a>
                                            <a href="index.php?page=admin_menu_delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus menu ini?')" class="action-btn delete-btn">🗑️ Delete</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                                $no++;
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align: center;'>Tidak ada data menu.</td></tr>";
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA - Admin Panel</p>
    </footer>

    <style>
        .admin-container {
            max-width: 1400px;
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

        .action-buttons {
            text-align: center;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table-responsive table {
            min-width: 800px;
            border-collapse: collapse;
        }

        .table-responsive th,
        .table-responsive td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table-responsive th {
            background-color: var(--primary-green);
            color: white;
            font-weight: bold;
        }

        .table-responsive tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table-responsive tr:hover {
            background-color: #f5f5f5;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .edit-btn {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }

        .edit-btn:hover {
            background: linear-gradient(135deg, #1976D2, #1565C0);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(33, 150, 243, 0.3);
        }

        .delete-btn {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(244, 67, 54, 0.3);
        }
    </style>
</body>
</html>