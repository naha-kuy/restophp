<?php
// Mengaktifkan error reporting untuk debugging (production: matikan ini)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Memulai session untuk manajemen keranjang
session_start();

// Generate CSRF token jika belum ada
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Memuat file konfigurasi dan komponen utama
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/MenuModel.php';
require_once __DIR__ . '/models/OrderModel.php';
require_once __DIR__ . '/controllers/MenuController.php';
require_once __DIR__ . '/controllers/OrderController.php';

// Membuat objek database dan koneksi
$db = new Database();
$conn = $db->connect();

// Membuat objek model dan controller
$menuModel = new MenuModel($conn);
$orderModel = new OrderModel($conn);
$menuController = new MenuController($menuModel);
$orderController = new OrderController($orderModel);

// Fungsi helper untuk validasi dan sanitasi input
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validateRequired($field, $minLength = 1, $maxLength = 255) {
    $field = trim($field);
    return !empty($field) && strlen($field) >= $minLength && strlen($field) <= $maxLength;
}

// Routing berdasarkan parameter page dengan validasi
$page = $_GET['page'] ?? 'login';

// Validasi parameter page untuk keamanan
$allowedPages = [
    'login', 'home', 'menu', 'contact', 'checkout', 'order_success',
    'admin_menu', 'admin_orders', 'admin_menu_create', 'admin_menu_edit',
    'admin_menu_delete', 'admin_order_status', 'admin_order_delete',
    'admin_order_edit', 'get_cart', 'save_cart', 'get_order_items'
];

if (!in_array($page, $allowedPages)) {
    $page = 'login'; // Default ke login jika page tidak valid
}

// Handle AJAX requests first, before authentication
if ($page === 'get_order_items') {
    // Mengambil detail item pesanan untuk modal dengan validasi
    header('Content-Type: application/json');
    $order_id = $_GET['order_id'] ?? null;
    if ($order_id && is_numeric($order_id)) {
        try {
            $order_data = $orderModel->getOrderById($order_id);
            if ($order_data && $order_data->num_rows > 0) {
                $items = [];
                while ($item = $order_data->fetch_assoc()) {
                    $items[] = [
                        'nama_menu' => $item['nama_menu'] ?? '',
                        'quantity' => (int)($item['quantity'] ?? 0),
                        'price' => (float)($item['price'] ?? 0)
                    ];
                }
                echo json_encode($items);
            } else {
                echo json_encode([]);
            }
        } catch (Exception $e) {
            error_log("Error getting order items: " . $e->getMessage());
            echo json_encode([]);
        }
    } else {
        echo json_encode([]);
    }
    exit;
}

// Kredensial admin
$adminUsername = 'nahaku';
$adminPassword = 'enakbet2025';

// Menangani logout dengan pembersihan session yang aman
if (isset($_GET['logout'])) {
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }

    // Destroy the session
    session_destroy();

    header('Location: index.php');
    exit;
}

// Validasi CSRF token untuk semua POST request kecuali save_cart (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page !== 'save_cart') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die('CSRF token validation failed');
    }
}

// Menangani login dengan validasi input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_type'])) {
    if ($_POST['login_type'] === 'admin') {
        $username = sanitizeInput($_POST['username'] ?? '');
        $password = $_POST['password'] ?? ''; // Password tidak perlu sanitize karena akan diverify

        if ($username === $adminUsername && $password === $adminPassword) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_type'] = 'admin';
            header('Location: index.php?page=admin_menu');
            exit;
        } else {
            $login_error = 'Username atau password salah!';
        }
    } elseif ($_POST['login_type'] === 'customer') {
        $_SESSION['user_type'] = 'customer';
        header('Location: index.php?page=home');
        exit;
    }
}

// Check if user is logged in
$userType = $_SESSION['user_type'] ?? null;
$isAdmin = ($userType === 'admin' && isset($_SESSION['admin_logged_in']));
$isCustomer = ($userType === 'customer');

// Route based on user type and page
if ($page === 'login' || (!$isAdmin && !$isCustomer)) {
    // Show login page
    include 'views/LoginView.php';
} elseif ($isAdmin) {
    // Admin pages
    switch ($page) {
        case 'admin_menu':
            $menus = $menuModel->getAllMenus();
            include 'views/AdminMenuView.php';
            break;

        case 'admin_orders':
            $filter = $_GET['filter'] ?? 'all';
            $orders = $orderModel->getOrdersByStatus($filter);
            include 'views/AdminOrdersView.php';
            break;

        case 'admin_menu_create':
            $menuController->create();
            break;

        case 'admin_menu_edit':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $menuController->edit($id);
            } else {
                echo "ID menu tidak ditemukan.";
            }
            break;

        case 'admin_menu_delete':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $menuController->delete($id);
                header('Location: index.php?page=admin_menu');
                exit;
            }
            break;

        case 'admin_order_status':
            $order_id = $_POST['order_id'] ?? null;
            $status = $_POST['status'] ?? null;
            if ($order_id && $status) {
                try {
                    $result = $orderModel->updateOrderStatus($order_id, $status);
                    if (!$result) {
                        throw new Exception("Error updating order status.");
                    }
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                    exit;
                }
            }
            header('Location: index.php?page=admin_orders');
            exit;

        case 'admin_order_edit':
            $order_id = $_GET['id'] ?? null;
            if ($order_id) {
                $orderController->editOrder($order_id);
            } else {
                echo "ID pesanan tidak ditemukan.";
            }
            break;

        case 'admin_order_delete':
            $order_id = $_GET['id'] ?? null;
            if ($order_id) {
                $orderController->deleteOrder($order_id);
            }
            break;


        default:
            header('Location: index.php?page=admin_menu');
            exit;
    }
} elseif ($isCustomer) {
    // Customer pages
    switch ($page) {
        case 'home':
            include 'views/CustomerLayout.php';
            break;

        case 'menu':
            $result = $menuModel->getAllMenus();
            include 'views/CustomerLayout.php';
            break;

        case 'checkout':
            $orderController->checkout();
            break;

        case 'order_success':
            $orderController->orderSuccess();
            break;

        case 'contact':
            include 'views/CustomerLayout.php';
            break;

        case 'get_cart':
            // Mengembalikan data keranjang sebagai JSON
            header('Content-Type: application/json');
            echo json_encode($_SESSION['cart'] ?? []);
            exit;

        case 'save_cart':
            // Menyimpan data keranjang dari POST dengan validasi dan logging detail
            header('Content-Type: application/json');

            // Force error logging untuk debugging
            error_log('=== SAVE_CART REQUEST START ===');
            error_log('REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
            error_log('CONTENT_TYPE: ' . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
            error_log('Session ID: ' . session_id());

            try {
                // Baca input data
                $input = file_get_contents('php://input');
                error_log('Raw input length: ' . strlen($input));
                error_log('Raw input: ' . $input);

                if (empty($input)) {
                    throw new Exception('No input data received from client');
                }

                // Decode JSON
                $cartData = json_decode($input, true);
                error_log('JSON decode error: ' . json_last_error_msg());

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Invalid JSON data received: ' . json_last_error_msg());
                }

                error_log('Decoded cart data: ' . print_r($cartData, true));

                // Validasi struktur cart data
                if (!is_array($cartData)) {
                    error_log('Cart data is not an array, setting to empty array');
                    $cartData = [];
                }

                // Validasi setiap item dalam cart
                $validatedCart = [];
                foreach ($cartData as $index => $item) {
                    error_log("Validating cart item $index: " . print_r($item, true));

                    if (is_array($item) &&
                        isset($item['id']) && isset($item['item']) &&
                        isset($item['price']) && isset($item['quantity'])) {

                        // Pastikan tipe data benar
                        $validatedItem = [
                            'id' => (int)$item['id'],
                            'item' => (string)$item['item'],
                            'price' => (float)$item['price'],
                            'quantity' => (int)$item['quantity']
                        ];

                        error_log("Validated item $index: " . print_r($validatedItem, true));

                        // Validasi nilai
                        if ($validatedItem['id'] > 0 &&
                            $validatedItem['price'] >= 0 &&
                            $validatedItem['quantity'] >= 0) {
                            $validatedCart[] = $validatedItem;
                            error_log("Item $index passed validation");
                        } else {
                            error_log("Item $index failed validation: id={$validatedItem['id']}, price={$validatedItem['price']}, quantity={$validatedItem['quantity']}");
                        }
                    } else {
                        error_log("Item $index missing required fields");
                    }
                }

                error_log('Final validated cart: ' . print_r($validatedCart, true));

                // Simpan ke session
                $_SESSION['cart'] = $validatedCart;
                error_log('Cart saved to session successfully');

                $response = [
                    'success' => true,
                    'cart_count' => count($validatedCart),
                    'message' => 'Cart saved successfully'
                ];

                error_log('Sending success response: ' . json_encode($response));
                echo json_encode($response);

            } catch (Exception $e) {
                error_log('Cart save error: ' . $e->getMessage());
                error_log('Stack trace: ' . $e->getTraceAsString());

                $errorResponse = [
                    'success' => false,
                    'error' => 'Failed to save cart: ' . $e->getMessage()
                ];

                error_log('Sending error response: ' . json_encode($errorResponse));
                echo json_encode($errorResponse);
            }

            error_log('=== SAVE_CART REQUEST END ===');
            exit; // Pastikan tidak ada output lain setelah JSON response

        default:
            header('Location: index.php?page=home');
            exit;
    }
} else {
    // Not logged in, redirect to login
    header('Location: index.php');
    exit;
}
?>