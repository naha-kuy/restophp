<?php
// Model untuk mengelola data pesanan
class OrderModel {
    private $conn;

    // Konstruktor menerima koneksi database
    public function __construct($db){
        $this->conn = $db;
    }

    // Membuat pesanan baru dengan validasi komprehensif
    public function createOrder($customer_name, $whatsapp, $address, $payment_method, $notes, $total_amount, $cart_items) {
        // Validasi parameter input
        if (empty($customer_name) || empty($whatsapp) || empty($address) || empty($payment_method)) {
            return false;
        }

        if (!is_numeric($total_amount) || $total_amount <= 0) {
            return false;
        }

        if (empty($cart_items) || !is_array($cart_items)) {
            return false;
        }

        // Mulai transaksi database
        $this->conn->begin_transaction();

        try {
            // Insert ke tabel orders
            $stmt = $this->conn->prepare("INSERT INTO orders (customer_name, whatsapp, address, payment_method, notes, total_amount, status, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, 'Dipesan', NOW())");

            if (!$stmt) {
                throw new Exception("Failed to prepare order statement");
            }

            $stmt->bind_param("sssssd", $customer_name, $whatsapp, $address, $payment_method, $notes, $total_amount);

            if (!$stmt->execute()) {
                throw new Exception("Failed to execute order insert");
            }

            $order_id = $this->conn->insert_id;

            if (!$order_id) {
                throw new Exception("Failed to get order ID");
            }

            // Insert ke tabel order_items
            $stmt_item = $this->conn->prepare("INSERT INTO order_items (order_id, menu_id, quantity, price)
                          VALUES (?, ?, ?, ?)");

            if (!$stmt_item) {
                throw new Exception("Failed to prepare order items statement");
            }

            foreach ($cart_items as $item) {
                // Validasi data item
                if (!isset($item['menu_id']) || !isset($item['quantity']) || !isset($item['price'])) {
                    throw new Exception("Incomplete cart item data");
                }

                if (!is_numeric($item['menu_id']) || !is_numeric($item['quantity']) || !is_numeric($item['price'])) {
                    throw new Exception("Invalid cart item data types");
                }

                if ($item['menu_id'] <= 0 || $item['quantity'] <= 0 || $item['price'] <= 0) {
                    throw new Exception("Invalid cart item values");
                }

                $stmt_item->bind_param("iiid", $order_id, $item['menu_id'], $item['quantity'], $item['price']);

                if (!$stmt_item->execute()) {
                    throw new Exception("Failed to insert order item");
                }
            }

            // Commit transaksi
            $this->conn->commit();
            return $order_id;

        } catch (Exception $e) {
            // Rollback jika ada error
            $this->conn->rollback();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }

    // Mengambil detail pesanan
    public function getOrderById($order_id) {
        $stmt = $this->conn->prepare("SELECT o.*, oi.*, m.nama_menu, m.gambar
                  FROM orders o
                  JOIN order_items oi ON o.id = oi.order_id
                  JOIN menu m ON oi.menu_id = m.id
                  WHERE o.id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    // Mengambil semua pesanan - diurutkan berdasarkan tanggal terlama ke terbaru
    public function getAllOrders() {
        $query = "SELECT * FROM orders ORDER BY created_at ASC";
        return $this->conn->query($query);
    }

    // Update pesanan
    public function updateOrder($order_id, $customer_name, $whatsapp, $address, $payment_method, $notes) {
        $stmt = $this->conn->prepare("UPDATE orders SET
            customer_name = ?,
            whatsapp = ?,
            address = ?,
            payment_method = ?,
            notes = ?
            WHERE id = ?");
        $stmt->bind_param("sssssi", $customer_name, $whatsapp, $address, $payment_method, $notes, $order_id);
        return $stmt->execute();
    }

    // Mengupdate status pesanan
    public function updateOrderStatus($order_id, $status) {
        $validStatuses = ['Dipesan', 'Diproses', 'Selesai'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $stmt = $this->conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }

    // Menghapus pesanan dan item terkait
    public function deleteOrder($order_id) {
        $this->conn->begin_transaction();

        try {
            // Hapus order items terlebih dahulu
            $stmt = $this->conn->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $order_id);
            $stmt->execute();

            // Hapus order
            $stmt = $this->conn->prepare("DELETE FROM orders WHERE id = ?");
            $stmt->bind_param("i", $order_id);
            $result = $stmt->execute();

            $this->conn->commit();
            return $result;
        } catch (Exception $e) {
            $this->conn->rollback();
            return false;
        }
    }

    // Mengambil pesanan berdasarkan status (untuk filter)
    public function getOrdersByStatus($status = null) {
        if ($status && $status !== 'all') {
            $stmt = $this->conn->prepare("SELECT * FROM orders WHERE status = ? ORDER BY created_at ASC");
            $stmt->bind_param("s", $status);
            $stmt->execute();
            return $stmt->get_result();
        } else {
            return $this->getAllOrders();
        }
    }
}
?>