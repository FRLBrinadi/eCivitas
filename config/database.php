<?php
// File: config/database.php

class Database {
    private static $instance = null;

    public static function connect() {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ];
                
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
                
            } catch (PDOException $e) {
                // Tampilan Error Friendly
                die("<div style='padding:20px; background:#f8d7da; color:#721c24; border:1px solid #f5c6cb;'>
                        <strong>Database Error:</strong> Gagal terhubung ke database.<br>
                        <small>Pastikan file 'ecivitas3.sql' sudah di-import ke phpMyAdmin.</small><br>
                        <small>Teknis: " . $e->getMessage() . "</small>
                     </div>");
            }
        }
        return self::$instance;
    }
}

// --- JEMBATAN KONEKSI (Compatibility Layer) ---
// Variable $pdo ini otomatis tersedia saat Anda include app.php
// Jadi kode lama Anda tidak perlu diubah total.
$pdo = Database::connect();
?>