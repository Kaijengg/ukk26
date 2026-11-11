-- Skema MySQL aplikasi Perpustakaan Sekolah Digital
-- Skema ini dibuat oleh migration Laravel; gunakan php artisan migrate.
CREATE DATABASE IF NOT EXISTS perpustakaan_sekolah CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE perpustakaan_sekolah;

CREATE TABLE books (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  isbn VARCHAR(30) NULL UNIQUE,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) NOT NULL,
  publisher VARCHAR(255) NULL,
  published_year SMALLINT UNSIGNED NULL,
  category VARCHAR(255) NOT NULL DEFAULT 'Umum',
  stock INT UNSIGNED NOT NULL DEFAULT 0,
  available_stock INT UNSIGNED NOT NULL DEFAULT 0,
  description TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE members (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nis VARCHAR(30) NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  class_name VARCHAR(30) NOT NULL,
  email VARCHAR(255) NULL UNIQUE,
  phone VARCHAR(30) NULL,
  status ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL
) ENGINE=InnoDB;

CREATE TABLE users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id BIGINT UNSIGNED NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  email_verified_at TIMESTAMP NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(255) NOT NULL DEFAULT 'admin',
  remember_token VARCHAR(100) NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  CONSTRAINT users_member_fk FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE loans (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  member_id BIGINT UNSIGNED NOT NULL,
  book_id BIGINT UNSIGNED NOT NULL,
  processed_by BIGINT UNSIGNED NULL,
  loan_date DATE NOT NULL,
  due_date DATE NOT NULL,
  returned_at DATE NULL,
  status ENUM('dipinjam','dikembalikan') NOT NULL DEFAULT 'dipinjam',
  notes TEXT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  INDEX loans_status_due_index (status, due_date),
  CONSTRAINT loans_member_fk FOREIGN KEY (member_id) REFERENCES members(id) ON DELETE RESTRICT,
  CONSTRAINT loans_book_fk FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE RESTRICT,
  CONSTRAINT loans_processor_fk FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;
