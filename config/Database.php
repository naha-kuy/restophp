<?php
class Database{
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db="lapak_nusantara_db";
    public $conn;

    public function connect(){
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if($this->conn->connect_error){
            die("Koneksi database gagal : ".$this->conn->connect_error);
        }
        return $this->conn;
    }
}
?>