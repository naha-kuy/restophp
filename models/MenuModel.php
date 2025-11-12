<?php
// Model untuk mengelola data menu
class MenuModel {
    private $conn;

    // Konstruktor menerima koneksi database
    public function __construct($db){
        $this->conn = $db;
    }

    // Mengambil semua menu
    public function getAllMenus(){
        $query = "SELECT m.*, c.name as kategori FROM menu m LEFT JOIN categories c ON m.category_id = c.id ORDER BY m.id ASC";
        return $this->conn->query($query);
    }

    // Mengambil menu berdasarkan ID
    public function getMenuById($id){
        $stmt = $this->conn->prepare("SELECT m.*, c.name as kategori FROM menu m LEFT JOIN categories c ON m.category_id = c.id WHERE m.id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Menambah menu baru
    public function addMenu($nama_menu, $category_id, $harga, $gambar = '', $deskripsi = ''){
        $stmt = $this->conn->prepare("INSERT INTO menu (nama_menu, category_id, harga, gambar, deskripsi)
                      VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sidss", $nama_menu, $category_id, $harga, $gambar, $deskripsi);
        return $stmt->execute();
    }

    // Mengupdate menu
    public function updateMenu($id, $nama_menu, $category_id, $harga, $gambar = '', $deskripsi = ''){
        $stmt = $this->conn->prepare("UPDATE menu SET
                      nama_menu = ?,
                      category_id = ?,
                      harga = ?,
                      gambar = ?,
                      deskripsi = ?
                    WHERE id = ?");
        $stmt->bind_param("sidssi", $nama_menu, $category_id, $harga, $gambar, $deskripsi, $id);
        return $stmt->execute();
    }

    // Menghapus menu
    public function deleteMenu($id){
        $stmt = $this->conn->prepare("DELETE FROM menu WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Memvalidasi apakah kategori ada dan aktif
    public function validateCategory($category_id){
        $stmt = $this->conn->prepare("SELECT id FROM categories WHERE id = ? AND is_active = 1");
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}
?>