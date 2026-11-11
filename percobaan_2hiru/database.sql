-- Jalankan di phpMyAdmin: tab "SQL" lalu klik "Kirim/Go"
-- PERHATIAN: skrip ini menghapus tabel lama (users & tugas) lalu membuat ulang.
CREATE DATABASE IF NOT EXISTS db_sekolah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_sekolah;

DROP TABLE IF EXISTS likes;
DROP TABLE IF EXISTS tugas;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id_user      INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username     VARCHAR(50)  NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,
    role         ENUM('admin','user') NOT NULL DEFAULT 'user',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tugas (
    id_tugas   INT AUTO_INCREMENT PRIMARY KEY,
    id_user    INT NOT NULL,
    judul      VARCHAR(100) NOT NULL,
    deskripsi  TEXT,
    gambar     VARCHAR(100) DEFAULT NULL,
    video      VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE likes (
    id_user  INT NOT NULL,
    id_tugas INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_user, id_tugas),
    FOREIGN KEY (id_user)  REFERENCES users(id_user)   ON DELETE CASCADE,
    FOREIGN KEY (id_tugas) REFERENCES tugas(id_tugas)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- Akun contoh (password sudah di-hash): admin / admin123  dan  siswa / siswa123
INSERT INTO users (nama_lengkap, username, password, role) VALUES
('Administrator', 'admin', '$6$3i0CAbfVJxpbDvCd$HNMZ4.cjnwJmp5NY3TjXwGqYhxMSmeFt3XP9LaHuV.FHTpopMRt5wxH0H0NaVgylbfM.kuLPwp2Q51w6uXI4h/', 'admin'),
('Siswa Contoh',  'siswa', '$6$AzYv9Id.NXtm8pml$tLTbWnxa7CfoIbKsth1ZYJynFKAmteTToCFrfWCX2KDT7T/Gh4qv6mnJ9eJ.3DLkOE3x3vv8a0Vu4OKLblHgl/', 'user');

INSERT INTO tugas (id_user, judul, deskripsi) VALUES
(2, 'Web Portofolio Pribadi', 'Website portofolio dengan HTML, CSS, dan JavaScript.'),
(2, 'Sistem Login PHP', 'Latihan login, session, dan role admin/user dengan PHP & MySQL.');