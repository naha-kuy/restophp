<?php
// Controller untuk mengelola operasi pesanan
class OrderController {
    private $model;

    // Konstruktor menerima model pesanan
    public function __construct($orderModel) {
        $this->model = $orderModel;
    }

    // Memproses checkout pesanan dengan validasi menyeluruh
    public function checkout() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validasi CSRF token terlebih dahulu
                if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                    throw new Exception("Token keamanan tidak valid. Silakan coba lagi.");
                }

                // Validasi input form
                $customer_name = trim($_POST['name'] ?? '');
                $whatsapp = trim($_POST['whatsapp'] ?? '');
                $address = trim($_POST['address'] ?? '');
                $payment_method = $_POST['payment'] ?? '';
                $notes = trim($_POST['notes'] ?? '');

                // Validasi nama pelanggan
                if (empty($customer_name) || strlen($customer_name) < 2 || strlen($customer_name) > 100) {
                    throw new Exception("Nama pelanggan harus diisi dan antara 2-100 karakter");
                }

                // Validasi WhatsApp
                if (empty($whatsapp) || !preg_match('/^[0-9]{10,15}$/', $whatsapp)) {
                    throw new Exception("Nomor WhatsApp harus berupa angka 10-15 digit");
                }

                // Validasi alamat
                if (empty($address) || strlen($address) < 2 || strlen($address) > 500) {
                    throw new Exception("Alamat harus diisi dan antara 2-500 karakter");
                }

                // Validasi metode pembayaran
                $validPayments = ['COD', 'Shopeepay', 'Gopay'];
                if (!in_array($payment_method, $validPayments)) {
                    throw new Exception("Metode pembayaran tidak valid");
                }

                // Validasi catatan
                if (strlen($notes) > 500) {
                    throw new Exception("Catatan maksimal 500 karakter");
                }

                // Validasi dan kalkulasi keranjang
                $cart = $_SESSION['cart'] ?? [];
                if (empty($cart)) {
                    throw new Exception("Keranjang belanja kosong. Silakan tambahkan menu terlebih dahulu.");
                }

                $total_amount = 0.0;
                $cart_items = [];

                foreach ($cart as $item) {
                    // Validasi struktur data item
                    if (!isset($item['id']) || !isset($item['price']) || !isset($item['quantity'])) {
                        throw new Exception("Data item keranjang tidak lengkap. Silakan refresh halaman dan coba lagi.");
                    }

                    $menu_id = (int)$item['id'];
                    $quantity = (int)$item['quantity'];
                    $price = (float)$item['price'];

                    // Validasi nilai
                    if ($menu_id <= 0 || $quantity <= 0 || $price <= 0) {
                        throw new Exception("Data item keranjang tidak valid. Pastikan jumlah dan harga benar.");
                    }

                    // Hitung subtotal dan akumulasi total
                    $subtotal = $price * $quantity;
                    $total_amount += $subtotal;

                    $cart_items[] = [
                        'menu_id' => $menu_id,
                        'quantity' => $quantity,
                        'price' => $price
                    ];
                }

                // Validasi total akhir
                if ($total_amount <= 0) {
                    throw new Exception("Total harga pesanan tidak valid. Silakan periksa item di keranjang.");
                }

                // Buat pesanan
                $order_id = $this->model->createOrder($customer_name, $whatsapp, $address, $payment_method, $notes, $total_amount, $cart_items);

                if ($order_id && $order_id > 0) {
                    // Bersihkan keranjang setelah sukses
                    unset($_SESSION['cart']);

                    // Generate struk (untuk admin)
                    $this->generateReceipt($order_id);

                    // Redirect ke halaman sukses
                    header("Location: index.php?page=order_success&order_id=$order_id");
                    exit;
                } else {
                    throw new Exception("Gagal membuat pesanan. Silakan coba lagi atau hubungi admin.");
                }

            } catch (Exception $e) {
                // Handle error khusus untuk keranjang kosong
                if (strpos($e->getMessage(), 'Keranjang belanja kosong') !== false) {
                    header("Location: index.php?page=menu&error=" . urlencode($e->getMessage()));
                    exit;
                }

                // Error lainnya tampilkan di halaman checkout
                $error_message = $e->getMessage();
                include 'views/CheckoutView.php';
                exit;
            }
        }

        // Tampilkan form checkout
        include 'views/CheckoutView.php';
    }

    // Menampilkan halaman sukses pesanan
    public function orderSuccess() {
        $order_id = $_GET['order_id'] ?? null;
        if (!$order_id) {
            header("Location: index.php");
            exit;
        }

        $order_data = $this->model->getOrderById($order_id);
        include 'views/OrderSuccessView.php';
    }

    // Edit pesanan
    public function editOrder($order_id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validasi input
                $customer_name = trim($_POST['customer_name'] ?? '');
                $whatsapp = trim($_POST['whatsapp'] ?? '');
                $address = trim($_POST['address'] ?? '');
                $payment_method = $_POST['payment_method'] ?? '';
                $notes = trim($_POST['notes'] ?? '');

                // Validasi nama pelanggan
                if (empty($customer_name) || strlen($customer_name) < 2 || strlen($customer_name) > 100) {
                    throw new Exception("Nama pelanggan harus diisi dan antara 2-100 karakter");
                }

                // Validasi WhatsApp
                if (empty($whatsapp) || !preg_match('/^[0-9]{10,15}$/', $whatsapp)) {
                    throw new Exception("Nomor WhatsApp harus berupa angka 10-15 digit");
                }

                // Validasi alamat
                if (empty($address) || strlen($address) < 2 || strlen($address) > 500) {
                    throw new Exception("Alamat harus diisi dan antara 2-500 karakter");
                }

                // Validasi metode pembayaran
                $validPayments = ['COD', 'Shopeepay', 'Gopay'];
                if (!in_array($payment_method, $validPayments)) {
                    throw new Exception("Metode pembayaran tidak valid");
                }

                // Validasi catatan
                if (strlen($notes) > 500) {
                    throw new Exception("Catatan maksimal 500 karakter");
                }

                if ($this->model->updateOrder($order_id, $customer_name, $whatsapp, $address, $payment_method, $notes)) {
                    header('Location: index.php?page=admin_orders');
                    exit;
                } else {
                    throw new Exception("Error updating order.");
                }
            } catch (Exception $e) {
                echo "<script>alert('" . addslashes($e->getMessage()) . "'); window.history.back();</script>";
                exit;
            }
        }

        $order_data = $this->model->getOrderById($order_id);
        if (!$order_data || $order_data->num_rows === 0) {
            echo "Pesanan tidak ditemukan.";
            return;
        }

        $order = $order_data->fetch_assoc();
        include 'views/OrderEditView.php';
    }

    // Menghapus pesanan
    public function deleteOrder($order_id) {
        try {
            if ($this->model->deleteOrder($order_id)) {
                header('Location: index.php?page=admin_orders');
                exit;
            } else {
                throw new Exception("Error deleting order.");
            }
        } catch (Exception $e) {
            echo "<script>alert('" . addslashes($e->getMessage()) . "'); window.history.back();</script>";
            exit;
        }
    }

    public function generateReceipt($order_id) {
        $order_data = $this->model->getOrderById($order_id);

        if (!$order_data) {
            return;
        }

        // Get order info
        $order = $order_data->fetch_assoc();
        $order_data->data_seek(0); // Reset pointer

        // Start output buffering for receipt
        ob_start();
        ?>
        <div style="font-family: 'Courier New', monospace; max-width: 300px; margin: 0 auto; padding: 10px; border: 1px solid #000;">
            <h2 style="text-align: center; margin: 0;">LAPAK NUSANTARA</h2>
            <p style="text-align: center; margin: 5px 0; font-size: 12px;">🍃 Nasi Bakar Nusantara 🍃</p>
            <hr style="border: none; border-top: 1px dashed #000;">

            <p style="margin: 5px 0; font-size: 12px;"><strong>Order ID:</strong> #<?php echo $order_id; ?></p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Tanggal:</strong> <?php echo date('d/m/Y H:i'); ?></p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Pelanggan:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>WA:</strong> <?php echo htmlspecialchars($order['whatsapp']); ?></p>

            <hr style="border: none; border-top: 1px dashed #000;">

            <?php
            $total = 0;
            while ($item = $order_data->fetch_assoc()) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
                echo "<p style='margin: 3px 0; font-size: 12px;'>{$item['nama_menu']} x{$item['quantity']} = Rp " . number_format($subtotal) . "</p>";
            }
            ?>

            <hr style="border: none; border-top: 1px dashed #000;">
            <p style="margin: 5px 0; font-size: 12px;"><strong>Total: Rp <?php echo number_format($total); ?></strong></p>
            <p style="margin: 5px 0; font-size: 12px;"><strong>Pembayaran:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>

            <hr style="border: none; border-top: 1px dashed #000;">
            <p style="text-align: center; margin: 10px 0; font-size: 12px;">Terima Kasih!</p>
            <p style="text-align: center; margin: 5px 0; font-size: 10px;">Citarasa Lokal, Yang Tak Terlupakan</p>
        </div>

        <script>
            window.onload = function() {
                window.print();
            }
        </script>
        <?php
        $receipt_html = ob_get_clean();

        // Save receipt HTML to session for printing
        $_SESSION['receipt_html'] = $receipt_html;
    }

    // Method printReceipt dihapus karena fitur cetak tidak diperlukan
}
?>