<?php
// View untuk halaman Daftar Pesanan Admin
$currentPage = 'admin_orders';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPAK NUSANTARA - Admin Orders</title>
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
                <h1>📦 Daftar Pesanan</h1>

                <!-- Filter Section -->
                <div class="filter-section" style="margin-bottom: 20px; text-align: center;">
                    <label for="statusFilter" style="font-weight: bold; margin-right: 10px;">Filter Status:</label>
                    <select id="statusFilter" onchange="filterOrders()" style="padding: 8px 15px; border-radius: 5px; border: 2px solid var(--primary-yellow);">
                        <option value="all">Semua Pesanan</option>
                        <option value="Dipesan">Dipesan</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                    <button onclick="resetFilter()" style="margin-left: 10px; padding: 8px 15px; border-radius: 5px; border: 2px solid var(--primary-green); background: var(--primary-green); color: white; cursor: pointer;">🔄 Reset Filter</button>
                </div>

                <div class="table-responsive">
                    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
                        <tr style="background-color: var(--primary-green); color: white;">
                            <th>No</th>
                            <th>Pelanggan</th>
                            <th>WhatsApp</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                        <?php
                        $totalSum = 0; // Initialize total sum
                        $no = 1; // Initialize sequential number
                        ?>
                        <?php
                        if (isset($orders) && $orders) {
                            while($row = $orders->fetch_assoc()){
                                $statusColor = '';
                                switch($row['status']) {
                                    case 'Dipesan': $statusColor = '#FFF3CD'; break; // Kuning lembut
                                    case 'Diproses': $statusColor = '#D1ECF1'; break; // Biru lembut
                                    case 'Selesai': $statusColor = '#D4EDDA'; break; // Hijau lembut
                                }
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['whatsapp']); ?></td>
                                    <td>Rp <?php echo number_format($row['total_amount']); $totalSum += $row['total_amount']; ?></td>
                                    <td>
                                        <form method="POST" action="index.php?page=admin_order_status" style="display: inline;">
                                        <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <select name="status" onchange="this.form.submit()" style="background: <?php echo $statusColor; ?>; color: black; border: 1px solid #ccc; padding: 5px; border-radius: 3px; font-weight: bold;">
                                                <option value="Dipesan" <?php echo ($row['status'] === 'Dipesan') ? 'selected' : ''; ?>>Dipesan</option>
                                                <option value="Diproses" <?php echo ($row['status'] === 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                                                <option value="Selesai" <?php echo ($row['status'] === 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="showOrderDetail(<?php echo $row['id']; ?>, '<?php echo addslashes($row['customer_name']); ?>', '<?php echo addslashes($row['address']); ?>', '<?php echo addslashes($row['whatsapp']); ?>', '<?php echo addslashes($row['payment_method']); ?>', '<?php echo addslashes($row['notes']); ?>')" class="action-btn detail-btn">👁️ Detail</button>
                                            <a href="index.php?page=admin_order_edit&id=<?php echo $row['id']; ?>" class="action-btn edit-btn">✏️ Edit</a>
                                            <a href="index.php?page=admin_order_delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus pesanan ini?')" class="action-btn delete-btn">🗑️ Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align: center;'>Tidak ada data pesanan.</td></tr>";
                        }
                        ?>
                        <tr style="background-color: var(--primary-yellow); color: var(--dark-green); font-weight: bold;">
                            <td colspan="3" style="text-align: right;">TOTAL PENDAPATAN:</td>
                            <td>Rp <?php echo number_format($totalSum); ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal untuk detail pesanan -->
    <div id="orderDetailModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div class="modal-content" style="background: white; padding: 20px; border-radius: 10px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <span class="close-modal" onclick="closeOrderDetail()" style="float: right; cursor: pointer; font-size: 24px;">&times;</span>
            <div id="orderDetailContent">
                <!-- Detail akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA - Admin Panel</p>
    </footer>

    <script>
        // Fungsi untuk filter pesanan
        function filterOrders() {
            const status = document.getElementById('statusFilter').value;
            window.location.href = `index.php?page=admin_orders&filter=${status}`;
        }

        // Fungsi untuk reset filter
        function resetFilter() {
            window.location.href = 'index.php?page=admin_orders';
        }

        // Fungsi untuk menampilkan detail pesanan
        function showOrderDetail(orderId, customerName, address, whatsapp, paymentMethod, notes) {
            const content = `
                <div class="order-detail-container">
                    <!-- Header Pesanan -->
                    <div class="order-header">
                        <h3>📋 Detail Pesanan </h3>
                        <div class="order-id-badge">ID: ${orderId}</div>
                    </div>
                    
                    <!-- Informasi Pelanggan -->
                    <div class="customer-info">
                        <div class="info-section">
                            <h4>👤 Informasi Pelanggan</h4>
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="label">Nama:</span>
                                    <span class="value">${customerName}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">WhatsApp:</span>
                                    <span class="value">${whatsapp}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Alamat:</span>
                                    <span class="value">${address}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Pembayaran:</span>
                                    <span class="value payment-badge">${paymentMethod}</span>
                                </div>
                            </div>
                        </div>
                        
                        ${notes ? `
                        <div class="info-section">
                            <h4>📝 Catatan</h4>
                            <div class="notes-box">
                                ${notes}
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    
                    <!-- Items Pesanan -->
                    <div class="order-items-section">
                        <h4>🍽️ Item Pesanan</h4>
                        <div id="orderItems" class="items-container">
                            <div class="loading-state">
                                Memuat detail item...
                            </div>
                        </div>
                    </div>
                    
                    <!-- Total -->
                    <div id="orderTotal" class="total-section">
                        <div class="loading-state">Menghitung total...</div>
                    </div>
                </div>
            `;

            document.getElementById('orderDetailContent').innerHTML = content;
            document.getElementById('orderDetailModal').style.display = 'flex';

            // Load order items via AJAX
            fetch(`index.php?page=get_order_items&order_id=${orderId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.length > 0) {
                        let itemsHtml = '';
                        let totalAmount = 0;
                        data.forEach(item => {
                            const itemTotal = item.price * item.quantity;
                            totalAmount += itemTotal;
                            itemsHtml += `
                                <div class="order-item">
                                    <div class="item-info">
                                        <span class="item-name">${item.nama_menu}</span>
                                        <span class="item-quantity">x${item.quantity}</span>
                                    </div>
                                    <div class="item-price">
                                        Rp ${new Intl.NumberFormat('id-ID').format(itemTotal)}
                                    </div>
                                </div>
                            `;
                        });
                        document.getElementById('orderItems').innerHTML = `
                            <div class="order-items-list">
                                ${itemsHtml}
                            </div>
                        `;
                        document.getElementById('orderTotal').innerHTML = `
                            <div class="total-amount">
                                <span class="total-label">Total Biaya:</span>
                                <span class="total-value">Rp ${new Intl.NumberFormat('id-ID').format(totalAmount)}</span>
                            </div>
                        `;
                    } else {
                        document.getElementById('orderItems').innerHTML = `
                            <div class="empty-state">
                                <p>Tidak ada item pesanan</p>
                            </div>
                        `;
                        document.getElementById('orderTotal').innerHTML = `
                            <div class="total-amount">
                                <span class="total-label">Total Biaya:</span>
                                <span class="total-value">Rp 0</span>
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading order items:', error);
                    document.getElementById('orderItems').innerHTML = `
                        <div class="error-state">
                            <p>Error memuat detail item pesanan</p>
                        </div>
                    `;
                    document.getElementById('orderTotal').innerHTML = `
                        <div class="total-amount">
                            <span class="total-label">Total Biaya:</span>
                            <span class="total-value error">Error menghitung total</span>
                        </div>
                    `;
                });
        }

        // Fungsi untuk menutup modal detail
        function closeOrderDetail() {
            document.getElementById('orderDetailModal').style.display = 'none';
        }

        // Tutup modal saat klik di luar
        window.onclick = function(event) {
            const modal = document.getElementById('orderDetailModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // Set filter value on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const filter = urlParams.get('filter');
            if (filter) {
                document.getElementById('statusFilter').value = filter;
            }
        });

        // Enhanced status update with confirmation
        function updateOrderStatus(orderId, newStatus) {
            const statusText = newStatus === 'Dipesan' ? 'Dipesan' :
                              newStatus === 'Diproses' ? 'Diproses' : 'Selesai';

            if (confirm(`Apakah Anda yakin ingin mengubah status pesanan menjadi "${statusText}"?`)) {
                // Status akan diupdate melalui form submit otomatis
                return true;
            }
            return false;
        }

        // Auto-submit form when status changes with confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const statusSelects = document.querySelectorAll('select[name="status"]');
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const orderId = this.previousElementSibling.value;
                    const newStatus = this.value;

                    if (updateOrderStatus(orderId, newStatus)) {
                        this.form.submit();
                    } else {
                        // Reset to previous value if cancelled
                        const urlParams = new URLSearchParams(window.location.search);
                        const currentFilter = urlParams.get('filter');
                        if (currentFilter && currentFilter !== 'all') {
                            // Reload page to reset status
                            window.location.reload();
                        }
                    }
                });
            });
        });
    </script>

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

        .modal-content {
            position: relative;
        }

        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
        }

        .close-modal:hover {
            color: #f44336;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .detail-btn {
            background: #2196F3;
            color: white;
        }

        .detail-btn:hover {
            background: #1976D2;
            transform: translateY(-1px);
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

        .print-btn.active {
            background: #4caf50;
            color: white;
        }

        .print-btn.active:hover {
            background: #388e3c;
            transform: translateY(-1px);
        }

        .delete-btn {
            background: #f44336;
            color: white;
        }

        .delete-btn:hover {
            background: #d32f2f;
            transform: translateY(-1px);
        }

        .filter-section {
            background: linear-gradient(135deg, var(--light-gray) 0%, var(--white) 100%);
            padding: 15px;
            border-radius: 8px;
            border: 2px solid var(--primary-yellow);
            display: inline-block;
        }

        /* Order Detail Popup Styles */
        .order-detail-container {
            max-width: 600px;
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .order-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, #2E7D32 100%);
            color: white;
            padding: 25px 20px;
            border-radius: 12px 12px 0 0;
            margin: -20px -20px 20px -20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .order-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.15) 0%, transparent 50%, rgba(255,255,255,0.15) 100%);
            pointer-events: none;
            z-index: 1;
        }

        .order-header h3 {
            margin: 0;
            font-size: 1.8em;
            font-weight: 700;
            color: #ffffff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.4), 0 0 20px rgba(255,255,255,0.4);
            letter-spacing: 1px;
            position: relative;
            z-index: 2;
            margin-bottom: 15px;
            animation: fadeInDown 0.6s ease-out;
        }

        .order-header h3::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-yellow), #FFD54F, var(--primary-yellow));
            border-radius: 2px;
            z-index: 2;
            box-shadow: 0 2px 6px rgba(255, 193, 7, 0.4);
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes fadeInDown {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            0% {
                opacity: 0;
                transform: translateX(-50%) translateY(10px);
                width: 50px;
            }
            100% {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
                width: 100px;
            }
        }

        .order-id-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 25px;
            padding: 10px 18px;
            font-size: 0.9em;
            font-weight: 600;
            color: #ffffff;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
            letter-spacing: 0.5px;
            position: relative;
            z-index: 2;
            margin-top: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2), inset 0 1px 2px rgba(255,255,255,0.2);
            animation: pulse 2s ease-in-out infinite alternate;
            transition: all 0.3s ease;
        }

        .order-id-badge:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.3), inset 0 1px 2px rgba(255,255,255,0.3);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 0 rgba(255, 255, 255, 0.4);
            }
            100% {
                box-shadow: 0 4px 12px rgba(0,0,0,0.2), 0 0 0 10px rgba(255, 255, 255, 0);
            }
        }

        .customer-info {
            margin-bottom: 25px;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-section h4 {
            color: var(--dark-green);
            margin: 0 0 12px 0;
            font-size: 1.1em;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-yellow);
            padding-bottom: 5px;
        }

        .info-grid {
            display: grid;
            gap: 12px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 6px;
            border-left: 4px solid var(--primary-green);
        }

        .info-item .label {
            font-weight: 600;
            color: #495057;
            min-width: 80px;
        }

        .info-item .value {
            color: #212529;
            text-align: right;
            flex: 1;
            margin-left: 10px;
        }

        .payment-badge {
            background: var(--primary-yellow);
            color: var(--dark-green);
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 0.9em;
        }

        .notes-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 12px;
            font-style: italic;
            color: #856404;
        }

        .order-items-section {
            margin-bottom: 20px;
        }

        .order-items-section h4 {
            color: var(--dark-green);
            margin: 0 0 12px 0;
            font-size: 1.1em;
            font-weight: 600;
            border-bottom: 2px solid var(--primary-yellow);
            padding-bottom: 5px;
        }

        .items-container {
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .loading-state {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }

        .order-items-list {
            padding: 15px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            margin-bottom: 8px;
            background: white;
            border-radius: 6px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .order-item:last-child {
            margin-bottom: 0;
        }

        .item-info {
            display: flex;
            flex-direction: column;
        }

        .item-name {
            font-weight: 600;
            color: #212529;
            font-size: 1em;
        }

        .item-quantity {
            color: #6c757d;
            font-size: 0.9em;
            margin-top: 2px;
        }

        .item-price {
            font-weight: 600;
            color: var(--primary-green);
            font-size: 1em;
        }

        .total-section {
            background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);
            border-radius: 8px;
            border: 2px solid var(--primary-green);
            padding: 15px;
            margin-top: 20px;
        }

        .total-amount {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-label {
            font-size: 1.1em;
            font-weight: 600;
            color: var(--dark-green);
        }

        .total-value {
            font-size: 1.3em;
            font-weight: bold;
            color: var(--primary-green);
        }

        .total-value.error {
            color: #dc3545;
        }

        .empty-state {
            padding: 20px;
            text-align: center;
            color: #6c757d;
        }

        .error-state {
            padding: 20px;
            text-align: center;
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 6px;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .order-detail-container {
                max-width: 100%;
            }

            .info-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-item .value {
                text-align: left;
                margin-left: 0;
                margin-top: 5px;
            }

            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .item-price {
                margin-top: 8px;
                align-self: flex-end;
            }

            .total-amount {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .total-value {
                align-self: flex-end;
            }
        }
    </style>
</body>
</html>