<?php
// Customer Layout - Contains header, footer, and main content area
$currentPage = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPAK NUSANTARA - Customer</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA</div>
            <ul class="nav-links">
                <li><a href="index.php?page=home" class="<?php echo ($currentPage === 'home') ? 'active' : ''; ?>">🏠 Home</a></li>
                <li><a href="index.php?page=menu" class="<?php echo ($currentPage === 'menu') ? 'active' : ''; ?>">🍽️ Menu</a></li>
                <li><a href="index.php?page=contact" class="<?php echo ($currentPage === 'contact') ? 'active' : ''; ?>">📞 Contact</a></li>
            </ul>
            <div style="display: flex; align-items: center; gap: 20px;">
                <?php if ($currentPage === 'menu'): ?>
                    <div class="cart-icon-container">
                        <span id="cart-icon" class="cart-icon">🛒</span>
                    </div>
                <?php endif; ?>
                <a href="index.php?logout=1" style="color: #ffc107; text-decoration: none; font-weight: bold;">🚪 Logout</a>
            </div>
        </nav>
    </header>

    <main style="padding-top: 120px;">
        <?php if ($currentPage === 'home'): ?>
            <!-- Home Page Content -->
            <section class="content">
                <div class="text-section">
                    <div class="title-box">
                        <h1>🍃 NASI BAKAR NUSANTARA 🍃</h1>
                    </div>
                    <div class="about-box">
                        <h2>CITARASA LOKAL, YANG TAK TERLUPAKAN</h2>
                        <p>
                        Selamat datang di Lapak Nusantara! Kami hadirkan Nasi Bakar Nusantara dengan cita rasa lokal yang otentik, diolah menggunakan bahan-bahan berkualitas dan teknik memasak tradisional. Rasakan sensasi Nasi Bakar Autentik Aroma Daun Pisang yang lezat, praktis, dan pasti mengenyangkan. Karena setiap hidangan kami diracik dengan penuh kasih sayang!
                        </p>
                    </div>
                    <div class="hero-box">
                        <a href="index.php?page=menu" class="btn">Pesan Sekarang →</a>
                    </div>
                </div>

                <div class="image-section">
                    <img src="image/3 menu.png" alt="Menu Preview" loading="lazy" />
                </div>
            </section>

        <?php elseif ($currentPage === 'menu'): ?>
            <!-- Menu Page Content -->
            <section id="menu">
                <h1>Daftar Menu Kami</h1>

                <?php if (isset($_GET['error'])): ?>
                    <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 2px solid #ffcdd2; text-align: center;">
                        <?php echo htmlspecialchars($_GET['error']); ?>
                    </div>
                <?php endif; ?>
                <div class="menu-items" id="menu-items">
                    <?php
                    if (isset($result) && $result) {
                        while($row = $result->fetch_assoc()){
                            ?>
                            <div class="menu-item">
                                <img src="<?php echo htmlspecialchars($row['gambar'] ?: 'image/default.png'); ?>" alt="<?php echo htmlspecialchars($row['nama_menu']); ?>" class="menu-image" loading="lazy">
                                <h3><?php echo htmlspecialchars($row['nama_menu']); ?></h3>
                                <p>Rp <?php echo number_format($row['harga']); ?></p>
                                <div class="menu-item-buttons">
                                    <button class="description-btn" onclick="showDescription('<?php echo htmlspecialchars($row['nama_menu']); ?>', '<?php echo htmlspecialchars($row['deskripsi'] ?: 'Deskripsi tidak tersedia'); ?>', '<?php echo htmlspecialchars($row['gambar'] ?: 'image/default.png'); ?>')">🔍 Keterangan Produk</button>
                                    <button class="add-to-cart" data-item="<?php echo htmlspecialchars($row['nama_menu']); ?>" data-price="<?php echo $row['harga']; ?>" data-id="<?php echo $row['id']; ?>">🛒 Tambah ke Keranjang</button>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>Tidak ada menu tersedia.</p>";
                    }
                    ?>
                </div>
            </section>

            <!-- Cart Modal -->
            <div id="cart-modal" class="cart-modal">
                <div class="cart-content">
                    <h2>Keranjang Belanja</h2>
                    <div id="cart-items"></div>
                    <div id="cart-total">Total: Rp 0</div>
                    <button id="clear-cart-btn">🗑️ Hapus Semua</button>
                    <button id="checkout-btn">Checkout</button>
                    <button id="close-cart" style="color: #ffc107;">X</button>
                </div>
            </div>

            <!-- Product Modal -->
            <div id="product-modal" class="product-modal" style="display: none;">
                <div class="product-modal-content">
                    <span class="close-modal">&times;</span>
                    <div class="product-modal-header">
                        <h2 id="modal-title"></h2>
                    </div>
                    <div class="product-modal-body">
                        <img id="modal-image" src="" alt="" class="product-modal-image" loading="lazy">
                        <div class="product-modal-description">
                            <p id="modal-description"></p>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button class="close-btn">✅ Tutup</button>
                    </div>
                </div>
            </div>

        <?php elseif ($currentPage === 'contact'): ?>
            <!-- Contact Page Content -->
            <section id="contact">
                <h1 align="center">Hubungi Kami</h1>
                <div class="contact-card">
                    <img src="image/pelayan.png" alt="Pelayan" class="contact-image" loading="lazy">
                    <div class="contact-info">
                        <p><strong>Pesan via WhatsApp:</strong> +62 856-0893-4919 (Candrika)</p>
                        <p><strong>Alamat:</strong> Jl. Cakrawala No.5, Sumbersari, Kec. Lowokwaru, Kota Malang, Jawa Timur 65145</p>
                        <p><strong>Email:</strong> candrika.prihantari.2203116@students.um.ac.id</p>
                    </div>
                </div>
            </section>

        <?php elseif ($currentPage === 'checkout'): ?>
            <!-- Checkout Page Content -->
            <section id="checkout">
                <div class="checkout-header">
                    <h1>🛒 Checkout Pesanan</h1>
                    <p class="checkout-subtitle">Lengkapi data untuk menyelesaikan pesanan Anda</p>
                </div>

                <div class="checkout-container">
                    <div class="checkout-card">
                        <div class="form-header">
                            <h2>📝 Data Pelanggan</h2>
                        </div>

                        <form id="checkout-form" method="POST" action="index.php?page=checkout">
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

        <?php elseif ($currentPage === 'order_success'): ?>
            <!-- Order Success Page Content -->
            <section id="order-success">
                <div class="checkout-header">
                    <h1>✅ Pesanan Berhasil!</h1>
                    <p class="checkout-subtitle">Terima kasih telah memesan di Lapak Nusantara</p>
                </div>

                <div class="checkout-container">
                    <div class="checkout-card">
                        <div class="form-header">
                            <h2>🎉 Detail Pesanan</h2>
                        </div>

                        <div class="success-content">
                            <?php if (isset($order_data) && $order_data): ?>
                                <?php $order = $order_data->fetch_assoc(); ?>
                                <div class="order-details">
                                    <p><strong>Order ID:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
                                    <p><strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                    <p><strong>WhatsApp:</strong> <?php echo htmlspecialchars($order['whatsapp']); ?></p>
                                    <p><strong>Alamat:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                                    <p><strong>Pembayaran:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                                    <p><strong>Total:</strong> Rp <?php echo number_format($order['total_amount']); ?></p>
                                    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                                </div>

                                <div class="order-items">
                                    <h3>📋 Item Pesanan:</h3>
                                    <ul>
                                        <?php
                                        $order_data->data_seek(0); // Reset pointer
                                        while ($item = $order_data->fetch_assoc()) {
                                            echo "<li>{$item['nama_menu']} x{$item['quantity']} - Rp " . number_format($item['price'] * $item['quantity']) . "</li>";
                                        }
                                        ?>
                                    </ul>
                                </div>
                            <?php else: ?>
                                <p>Detail pesanan tidak ditemukan.</p>
                            <?php endif; ?>

                            <div class="success-actions">
                                <a href="index.php?page=home" class="btn" style="background: var(--bg-button-green); color: white;">🏠 Kembali ke Home</a>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-info">
                        <div class="info-card">
                            <h3>📱 Langkah Selanjutnya</h3>
                            <ul>
                                <li>✅ Pesanan telah dicatat dalam sistem</li>
                                <li>📞 Kami akan menghubungi via WhatsApp untuk konfirmasi</li>
                                <li>🚚 Pengantaran akan dilakukan sesuai kesepakatan</li>
                                <li>💰 Pembayaran sesuai metode yang dipilih</li>
                            </ul>
                        </div>

                        <div class="info-card">
                            <h3>🍽️ Terima Kasih</h3>
                            <p>Citarasa Lokal, Yang Tak Terlupakan</p>
                            <p>Semoga makanan kami memuaskan selera Anda!</p>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA</p>
    </footer>

    <?php if ($currentPage === 'menu'): ?>
    <script>
        // Cart functionality using PHP sessions via AJAX
        let cart = [];

        // Load cart from server
        function loadCart() {
            fetch('index.php?page=get_cart', { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    cart = data;
                    updateCartDisplay();
                })
                .catch(error => console.error('Error loading cart:', error));
        }

        // Save cart to server
        function saveCart() {
            return fetch('index.php?page=save_cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(cart)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Failed to save cart:', data);
                    throw new Error('Failed to save cart');
                }
                return data;
            })
            .catch(error => {
                console.error('Error saving cart:', error);
                throw error;
            });
        }

        // Add item to cart
        function addToCart(itemName, price, itemId) {
            const existingItem = cart.find(cartItem => cartItem.id === itemId);
            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({ id: itemId, item: itemName, price: parseInt(price), quantity: 1 });
            }
            saveCart();
            updateCartDisplay();
            showAddToCartMessage(itemName);
        }

        // Update cart display
        function updateCartDisplay() {
            const cartItems = document.getElementById('cart-items');
            const cartTotal = document.getElementById('cart-total');
            const cartIcon = document.getElementById('cart-icon');

            cartItems.innerHTML = '';
            let total = 0;

            cart.forEach((cartItem, index) => {
                const itemTotal = cartItem.price * cartItem.quantity;
                total += itemTotal;

                const cartItemDiv = document.createElement('div');
                cartItemDiv.className = 'cart-item';
                cartItemDiv.innerHTML = `
                    <div class="cart-item-info">
                        <div>${cartItem.item}</div>
                        <div>Rp ${cartItem.price.toLocaleString()} x ${cartItem.quantity}</div>
                    </div>
                    <div class="cart-item-controls">
                        <button onclick="changeQuantity(${index}, -1)">-</button>
                        <span>${cartItem.quantity}</span>
                        <button onclick="changeQuantity(${index}, 1)">+</button>
                    </div>
                `;
                cartItems.appendChild(cartItemDiv);
            });

            cartTotal.textContent = `Total: Rp ${total.toLocaleString()}`;

            // Update cart icon
            const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
            if (cartIcon) {
                cartIcon.setAttribute('data-count', totalItems);
            }
        }

        // Change quantity
        function changeQuantity(index, change) {
            cart[index].quantity += change;
            if (cart[index].quantity <= 0) {
                cart.splice(index, 1);
            }
            saveCart();
            updateCartDisplay();
        }

        // Clear cart
        function clearCart() {
            if (cart.length === 0) {
                showAddToCartMessage('Keranjang sudah kosong! 🛒');
                return;
            }

            if (confirm('🗑️ Yakin ingin menghapus semua pesanan dari keranjang?')) {
                cart = [];
                saveCart();
                updateCartDisplay();
                showAddToCartMessage('Semua pesanan telah dihapus! 🗑️');
            }
        }

        // Checkout
        async function handleCheckout() {
            if (cart.length === 0) {
                alert('Keranjang kosong. Silakan tambahkan item pesanan terlebih dahulu.');
                return;
            }

            try {
                // Pastikan cart tersimpan sebelum checkout dengan await
                await saveCart();
                console.log('Cart saved successfully, redirecting to checkout...');
                // Redirect ke checkout
                window.location.href = 'index.php?page=checkout';
            } catch (error) {
                console.error('Failed to save cart before checkout:', error);
                alert('Gagal menyimpan keranjang. Silakan coba lagi.');
            }
        }

        // Show product description
        function showDescription(name, description, image) {
            document.getElementById('modal-title').textContent = name;
            document.getElementById('modal-description').textContent = description;
            document.getElementById('modal-image').src = image;
            document.getElementById('product-modal').style.display = 'flex';
        }

        // Show add to cart message
        function showAddToCartMessage(itemName) {
            const message = document.createElement('div');
            message.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #ffbf00, #45a049);
                color: white;
                padding: 15px 20px;
                border-radius: 25px;
                box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
                z-index: 10000;
                font-weight: bold;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                border: 2px solid #FFF;
            `;
            message.textContent = `✅ ${itemName} ditambahkan ke keranjang!`;
            document.body.appendChild(message);

            setTimeout(() => message.style.transform = 'translateX(0)', 100);
            setTimeout(() => message.style.transform = 'translateX(100%)', 2500);
            setTimeout(() => document.body.removeChild(message), 3000);
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            loadCart();

            // Add to cart buttons
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('add-to-cart')) {
                    const item = e.target.getAttribute('data-item');
                    const price = e.target.getAttribute('data-price');
                    const id = e.target.getAttribute('data-id');
                    addToCart(item, price, id);

                    // Animation
                    e.target.style.transform = 'scale(0.95)';
                    e.target.style.background = '#45a049';
                    setTimeout(() => {
                        e.target.style.transform = 'scale(1)';
                        e.target.style.background = '#4CAF50';
                    }, 150);
                }
            });

            // Cart icon
            const cartIcon = document.getElementById('cart-icon');
            if (cartIcon) {
                cartIcon.addEventListener('click', function() {
                    document.getElementById('cart-modal').style.display = 'flex';
                });
            }

            // Close cart modal
            const closeCartBtn = document.getElementById('close-cart');
            if (closeCartBtn) {
                closeCartBtn.addEventListener('click', function() {
                    document.getElementById('cart-modal').style.display = 'none';
                });
            }

            // Clear cart button
            const clearCartBtn = document.getElementById('clear-cart-btn');
            if (clearCartBtn) {
                clearCartBtn.addEventListener('click', clearCart);
            }

            // Checkout button
            const checkoutBtn = document.getElementById('checkout-btn');
            if (checkoutBtn) {
                checkoutBtn.addEventListener('click', handleCheckout);
            }

            // Close product modal
            document.querySelectorAll('.close-modal, .close-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('product-modal').style.display = 'none';
                    document.getElementById('cart-modal').style.display = 'none';
                });
            });

            // Close modals when clicking outside
            document.querySelectorAll('.product-modal, .cart-modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            });

            // WhatsApp validation for checkout
            const whatsappInput = document.getElementById('whatsapp');
            if (whatsappInput) {
                whatsappInput.addEventListener('input', function(e) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                });

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

                whatsappInput.addEventListener('paste', function(e) {
                    setTimeout(() => {
                        this.value = this.value.replace(/[^0-9]/g, '');
                    }, 10);
                });
            }

            // Checkout form validation
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) {
                checkoutForm.addEventListener('submit', function(e) {
                    if (whatsappInput) {
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
                    }
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>