<?php
// Controller untuk mengelola operasi menu
class MenuController {
    private $model;

    // Konstruktor menerima model menu
    public function __construct($menuModel) {
        $this->model = $menuModel;
    }

    // Menampilkan daftar menu (untuk admin)
    public function index() {
        $result = $this->model->getAllMenus();
        include 'views/MenuListView.php';
    }

    // Membuat menu baru
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Validasi input
                $nama_menu = trim($_POST['nama_menu'] ?? '');
                $kategori = $_POST['kategori'] ?? '';
                $harga = $_POST['harga'] ?? '';
                $deskripsi = trim($_POST['deskripsi'] ?? '');

                // Validasi nama menu
                if (empty($nama_menu) || strlen($nama_menu) < 2 || strlen($nama_menu) > 100) {
                    throw new Exception("Nama menu harus diisi dan antara 2-100 karakter");
                }

                // Validasi kategori (category_id harus numeric dan ada di database)
                if (!is_numeric($kategori) || $kategori <= 0) {
                    throw new Exception("Kategori tidak valid");
                }
                // Cek apakah kategori ada di database
                if (!$this->model->validateCategory($kategori)) {
                    throw new Exception("Kategori tidak ditemukan");
                }

                // Validasi harga
                if (!is_numeric($harga) || $harga <= 0 || $harga > 1000000) {
                    throw new Exception("Harga harus berupa angka positif dan maksimal 1.000.000");
                }

                // Validasi deskripsi
                if (strlen($deskripsi) > 500) {
                    throw new Exception("Deskripsi maksimal 500 karakter");
                }

                // Handle file upload
                $gambar = '';
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid() . '.' . $ext;
                        $upload_path = __DIR__ . '/../image/' . $new_filename;

                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                            $gambar = 'image/' . $new_filename;
                        } else {
                            throw new Exception("Error uploading file.");
                        }
                    } else {
                        throw new Exception("Invalid file type. Only JPG, JPEG, PNG allowed.");
                    }
                }

                if ($this->model->addMenu($nama_menu, $kategori, $harga, $gambar, $deskripsi)) {
                    header("Location: index.php?page=admin_menu");
                    exit;
                } else {
                    throw new Exception("Error saving menu to database.");
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = $e->getMessage();
                header("Location: index.php?page=admin_menu_create");
                exit;
            }
        }
        include 'views/MenuCreateView.php';
    }

    // Mengedit menu berdasarkan ID
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validasi input
                $nama_menu = trim($_POST['nama_menu'] ?? '');
                $kategori = $_POST['kategori'] ?? '';
                $harga = $_POST['harga'] ?? '';
                $deskripsi = trim($_POST['deskripsi'] ?? '');

                // Validasi nama menu
                if (empty($nama_menu) || strlen($nama_menu) < 2 || strlen($nama_menu) > 100) {
                    throw new Exception("Nama menu harus diisi dan antara 2-100 karakter");
                }

                // Validasi kategori (category_id harus numeric dan ada di database)
                if (!is_numeric($kategori) || $kategori <= 0) {
                    throw new Exception("Kategori tidak valid");
                }
                // Cek apakah kategori ada di database
                if (!$this->model->validateCategory($kategori)) {
                    throw new Exception("Kategori tidak ditemukan");
                }

                // Validasi harga
                if (!is_numeric($harga) || $harga <= 0 || $harga > 1000000) {
                    throw new Exception("Harga harus berupa angka positif dan maksimal 1.000.000");
                }

                // Validasi deskripsi
                if (strlen($deskripsi) > 500) {
                    throw new Exception("Deskripsi maksimal 500 karakter");
                }

                // Get current menu data for existing image
                $current_data = $this->model->getMenuById($id);
                $gambar = $current_data['gambar'];

                // Handle file upload
                if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png'];
                    $filename = $_FILES['gambar']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowed)) {
                        $new_filename = uniqid() . '.' . $ext;
                        $upload_path = __DIR__ . '/../image/' . $new_filename;

                        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                            // Delete old image if exists
                            if ($gambar && file_exists(__DIR__ . '/../' . $gambar)) {
                                unlink(__DIR__ . '/../' . $gambar);
                            }
                            $gambar = 'image/' . $new_filename;
                        } else {
                            throw new Exception("Error uploading file.");
                        }
                    } else {
                        throw new Exception("Invalid file type. Only JPG, JPEG, PNG allowed.");
                    }
                }

                if ($this->model->updateMenu($id, $nama_menu, $kategori, $harga, $gambar, $deskripsi)) {
                    header('Location: index.php?page=admin_menu');
                    exit;
                } else {
                    throw new Exception("Error updating menu in database.");
                }
            } catch (Exception $e) {
                $_SESSION['error_message'] = $e->getMessage();
                header("Location: index.php?page=admin_menu_edit&id=$id");
                exit;
            }
        }
        $data = $this->model->getMenuById($id);
        include 'views/MenuEditView.php';
    }

    // Menghapus menu berdasarkan ID
    public function delete($id) {
        try {
            // Get menu data to delete image file
            $menu_data = $this->model->getMenuById($id);
            if ($menu_data && $menu_data['gambar'] && file_exists(__DIR__ . '/../' . $menu_data['gambar'])) {
                unlink(__DIR__ . '/../' . $menu_data['gambar']);
            }

            if ($this->model->deleteMenu($id)) {
                header('Location: index.php?page=admin_menu');
                exit;
            } else {
                throw new Exception("Error deleting menu from database.");
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            exit;
        }
    }
}
?>