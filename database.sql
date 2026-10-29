-- Database: db_inventaris
CREATE DATABASE IF NOT EXISTS db_inventaris;
USE db_inventaris;

-- Tabel users untuk autentikasi petugas
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel data barang / sarana prasarana kantor
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    harga DECIMAL(12, 2) NOT NULL,
    stok INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal Petugas Inventaris (Username: admin, Password: password123)
INSERT INTO users (nama, username, password) 
VALUES ('Administrator Kantor', 'admin', '$2y$10$4B9a83gLz3Uv0P0vT2eMquZk/E0RfgqR3aWd1qfT8oIeN0F17oNvy')
ON DUPLICATE KEY UPDATE id=id;

-- Data Contoh Inventaris Sarana & Prasarana Kantor (Office Assets)
INSERT INTO barang (kode, nama, kategori, harga, stok) VALUES
('OFC-IT-001', 'Laptop Lenovo ThinkPad T14 Gen 3 Core i7 16GB', 'IT & Komputer', 14500000.00, 12),
('OFC-IT-002', 'Monitor Dell UltraSharp 27" 4K USB-C (U2723QE)', 'IT & Komputer', 7850000.00, 8),
('OFC-FURN-001', 'Kursi Kerja Ergonomis Mesh Pira Stella', 'Furniture & Perabotan', 1850000.00, 24),
('OFC-FURN-002', 'Meja Kerja Kantor Minimalis Modera 140x70cm', 'Furniture & Perabotan', 2400000.00, 15),
('OFC-ATK-001', 'Kertas HVS PaperOne A4 80gsm (Dus / 5 Rim)', 'Alat Tulis Kantor (ATK)', 245000.00, 35),
('OFC-ATK-002', 'Map Ordner Bantex F4 7cm Lever Arch File', 'Alat Tulis Kantor (ATK)', 38000.00, 4),
('OFC-ELK-001', 'Proyektor Ruang Rapat Epson EB-E01 3300 Lumens', 'Elektronik & Presentasi', 5250000.00, 3),
('OFC-ELK-002', 'Wireless Presentation Clicker Logitech R400', 'Elektronik & Presentasi', 450000.00, 0),
('OFC-PNT-001', 'Dispenser Air Galon Bawah Sharp SWD-72EHL', 'Perlengkapan Pantry & Fasilitas', 2650000.00, 2)
ON DUPLICATE KEY UPDATE kode=VALUES(kode);
