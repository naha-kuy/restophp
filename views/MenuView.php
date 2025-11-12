<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAPAK NUSANTARA - Menu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA</div>
            <ul class="nav-links">
                <li><a href="index.php">🏠 Home</a></li>
                <li><a href="index.php?action=menu" class="active">🍽️ Menu</a></li>
                <li><a href="index.php?action=contact">📞 Contact</a></li>
            </ul>
            <div class="cart-icon-container">
                <span id="cart-icon" class="cart-icon">🛒</span>
            </div>
        </nav>
    </header>

    <main>
        <section id="menu">
            <h1>Daftar Menu Kami</h1>
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
    </main>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA</p>
    </footer>

    <script>
        // Cart functionality using PHP sessions via AJAX
        let cart = [];

        // Load cart from server
        function loadCart() {
            fetch('index.php?action=get_cart', { method: 'GET' })
                .then(response => response.json())
                .then(data => {
                    cart = data;
                    updateCartDisplay();
                })
                .catch(error => console.error('Error loading cart:', error));
        }

        // Save cart to server
        function saveCart() {
            fetch('index.php?action=save_cart', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(cart)
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
            const cartContainer = document.querySelector('.cart-icon-container');

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
                const container = cartIcon.closest('.cart-icon-container');
                if (container) {
                    container.style.display = totalItems > 0 ? 'block' : 'none';
                }
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
        function handleCheckout() {
            if (cart.length === 0) {
                alert('Keranjang kosong. Silakan tambahkan item pesanan terlebih dahulu.');
                return;
            }
            window.location.href = 'index.php?action=checkout';
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
        });
    </script>
</body>
</html>