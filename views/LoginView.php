<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>LAPAK NUSANTARA - Login</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">🍃 LAPAK NUSANTARA</div>
        </nav>
    </header>

    <main>
        <section class="content">
            <div class="text-section">
                <div class="title-box">
                    <h1>🍃 LAPAK NUSANTARA 🍃</h1>
                </div>
                <div class="about-box">
                    <h2>SISTEM MANAJEMEN RESTORAN</h2>
                    <p>
                    Selamat datang di Sistem Manajemen Restoran Lapak Nusantara. Pilih jenis login untuk melanjutkan.
                    </p>
                </div>

                <div class="login-container">
                    <?php if (isset($login_error)): ?>
                        <div style="background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 2px solid #ffcdd2; text-align: center;">
                            <?php echo htmlspecialchars($login_error); ?>
                        </div>
                    <?php endif; ?>

                    <div class="login-options">
                        <!-- Admin Login -->
                        <div class="login-card admin-login">
                            <h3>🔐 Admin Login</h3>
                            <form method="POST" action="index.php">
                                <input type="hidden" name="login_type" value="admin">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <div class="form-group">
                                    <label for="username">👤 Username:</label>
                                    <input type="text" id="username" name="username" placeholder="Masukkan username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">🔒 Password:</label>
                                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                                </div>
                                <button type="submit" class="btn" style="width: 100%; margin-top: 15px;">Masuk sebagai Admin</button>
                            </form>
                        </div>

                        <!-- Customer Login -->
                        <div class="login-card customer-login">
                            <h3>🍽️ Masuk sebagai Pelanggan</h3>
                            <form method="POST" action="index.php">
                                <input type="hidden" name="login_type" value="customer">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <p style="text-align: center; margin-bottom: 20px;">Klik tombol di bawah untuk melanjutkan sebagai pelanggan</p>
                                <button type="submit" class="btn" style="width: 100%; background: var(--bg-button-green); color: white;">🍽️ Mulai Berbelanja</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="image-section">
                <img src="image/3 menu.png" alt="Menu Preview" loading="lazy" />
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 LAPAK NUSANTARA - Sistem Restoran 2.0</p>
    </footer>

    <style>
        .login-container {
            margin-top: 30px;
        }

        .login-options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }

        .login-card {
            background: linear-gradient(135deg, var(--white) 0%, var(--light-gray) 100%);
            padding: 30px;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            border: 3px solid var(--primary-yellow);
            text-align: center;
        }

        .admin-login {
            border-color: var(--red);
        }

        .customer-login {
            border-color: var(--primary-green);
        }

        .login-card h3 {
            color: var(--dark-green);
            margin-bottom: 20px;
            font-size: var(--font-size-xxl);
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--dark-green);
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border-light);
            border-radius: var(--radius-md);
            font-size: 16px;
            transition: var(--transition-fast);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.3);
        }

        @media (max-width: 768px) {
            .login-options {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .login-card {
                padding: 20px;
            }
        }
    </style>
</body>
</html>