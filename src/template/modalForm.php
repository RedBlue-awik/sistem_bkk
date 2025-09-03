<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    :root {
        --primary-color: #3f87a6;
        --smk-color: #5ebf26;
        --secondary-color: #f8b500;
        --success-color: #28a745;
    }

    .login-modal-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .modal-header-custom {
        background: linear-gradient(135deg, var(--smk-color), #198504);
        color: white;
        border-bottom: none;
    }

    .lupa-header-custom {
        background: linear-gradient(135deg, #790404ff, #b82323ff);
        color: white;
        border-bottom: none;
    }

    .daftar {
        background: linear-gradient(135deg, #bd9206, #f2b305);
        color: white;
        border-bottom: none;
    }

    .brand-title {
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .input-group-custom {
        margin-bottom: 1.2rem;
    }

    .form-control-custom {
        border-radius: 8px;
        padding: 0.8rem 1rem;
        border: 1px solid #e1e5eb;
        transition: all 0.3s;
    }

    .form-control-custom:focus {
        box-shadow: 0 0 0 3px rgba(63, 135, 166, 0.2);
        border-color: var(--primary-color);
    }

    .input-group-text-custom {
        background: transparent;
        border-left: none;
        border-radius: 0 8px 8px 0;
        color: #6c757d;
        transition: all 0.3s;
    }

    .input-group-text-custom2 {
        background: transparent;
        border-radius: 0 8px 8px 0;
        color: #6c757d;
        transition: all 0.3s;
    }

    .form-floating:focus-within+.input-group-text-custom {
        color: var(--primary-color);
    }

    .btn-login {
        background: linear-gradient(135deg, #259c0e, var(--smk-color));
        color: white;
        border: none;
        padding: 0.8rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-login:hover {
        box-shadow: 0 5px 15px rgba(63, 135, 166, 0.3);
    }

    .btn-lupa {
        background: linear-gradient(135deg, #790404ff, #b82323ff);
        color: white;
        border: none;
        padding: 0.8rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-lupa:hover {
        box-shadow: 0 5px 15px rgba(63, 135, 166, 0.3);
    }

    .btn-whatsapp {
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: white;
        border: none;
        padding: 0.5rem;
        font-weight: 600;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .btn-whatsapp:hover {
        box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
    }

    .password-toggle {
        cursor: pointer;
        transition: color 0.3s;
    }

    .password-toggle:hover {
        color: var(--primary-color);
    }

    .accordion-button:not(.collapsed) {
        background-color: rgba(63, 135, 166, 0.1);
        color: var(--primary-color);
        font-weight: 600;
    }

    .phone-number {
        background: linear-gradient(135deg, var(--success-color), #6bc46b);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        display: inline-block;
        margin: 0.5rem 0;
        font-weight: bold;
    }

    .warning-box {
        background-color: #fff3cd;
        border-radius: 8px;
        padding: 1rem;
        margin: 1rem 0;
        border-left: 4px solid #ffc107;
    }
</style>

<!-- Modal Login -->
<div class="modal fade" id="Modallogin" tabindex="-1" aria-labelledby="ModalloginLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content login-modal-card">
            <div class="modal-header modal-header-custom text-center position-relative">
                <div class="w-100">
                    <h1 class="brand-title mb-1"><i class="fas fa-graduation-cap me-2"></i>Sign In - BKK</h1>
                    <p class="mb-0">Sistem Informasi Bursa Kerja Khusus</p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <p class="text-center text-muted mb-4">Masukkan akun Anda untuk mengakses Aplikasi</p>

                <form action="" method="post" class="needs-validation" novalidate>
                    <!-- Username Field -->
                    <div class="input-group input-group-custom">
                        <div class="form-floating">
                            <input id="username" type="text" name="username" class="form-control form-control-custom" placeholder="Username" required />
                            <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                        </div>
                        <span class="input-group-text input-group-text-custom">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>

                    <!-- Password Field -->
                    <div class="input-group input-group-custom">
                        <div class="form-floating">
                            <input id="password" type="password" name="password" class="form-control form-control-custom" placeholder="Password" required />
                            <label for="password"><i class="fas fa-key me-2"></i>Password</label>
                        </div>
                        <span class="input-group-text input-group-text-custom password-toggle" id="togglePasswordLogin">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input" />
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <a href="" data-bs-toggle="modal" data-bs-target="#Modallupapw" class="text-decoration-none text-primary">Lupa Password?</a>
                    </div>

                    <!-- Login Button -->
                    <div class="d-grid gap-2">
                        <button type="submit" name="login" class="btn btn-login">
                            <i class="fas fa-sign-in-alt me-2"></i>Masuk
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <p class="mb-0">Belum punya akun?
                            <a href="#" class="text-primary text-decoration-none fw-medium" data-bs-target="#Modaldaftar" data-bs-toggle="modal" data-bs-dismiss="modal">
                                Daftar di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Lupa Password -->
<div class="modal fade" id="Modallupapw" tabindex="-1" aria-labelledby="ModallupapwLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content login-modal-card">
            <div class="modal-header lupa-header-custom text-center position-relative">
                <div class="w-100">
                    <h1 class="brand-title mb-1"><i class="fas fa-user-lock me-2"></i>Reset Password</h1>
                    <p class="mb-0">Sistem Informasi Bursa Kerja Khusus</p>
                </div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <p class="text-center text-muted mb-4">Masukkan Username dan Nisn Anda <br> lalu ubah Password Anda</p>

                <form action="" method="post" class="needs-validation" novalidate>
                    <!-- Username Field -->
                    <div class="input-group input-group-custom">
                        <div class="form-floating me-3">
                            <input id="lupaUsername" type="text" name="username" class="form-control form-control-custom" placeholder="Username" required />
                            <label for="lupaUsername"><i class="fas fa-user me-2"></i>Username</label>
                        </div>

                        <div class="form-floating">
                            <input id="nisn" type="text" name="nisn" class="form-control" placeholder="" required autocomplete="off" />
                            <label for="nisn" class="form-label"><i class="fas fa-rectangle-list me-1"></i> Nisn</label>
                        </div>
                    </div>

                    <!-- Password Baru Field -->
                    <div class="input-group input-group-custom">
                        <div class="form-floating">
                            <input id="passwordBaru" type="password" name="passwordnew" class="form-control form-control-custom" placeholder="Password Baru" required minlength="6" />
                            <label for="passwordBaru"><i class="fas fa-lock me-2"></i>Password Baru</label>
                        </div>
                        <span class="input-group-text input-group-text-custom password-toggle" id="togglePasswordBaru">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <!-- Konfirmasi Password Field -->
                    <div class="input-group input-group-custom">
                        <div class="form-floating">
                            <input id="confirmPassword" type="password" name="confirmpassword" class="form-control form-control-custom" placeholder="Konfirmasi Password" required minlength="6" />
                            <label for="confirmPassword"><i class="fas fa-lock me-2"></i>Konfirmasi Password</label>
                        </div>
                        <span class="input-group-text input-group-text-custom password-toggle" id="toggleConfirmPassword">
                            <i class="fas fa-eye"></i>
                        </span>
                    </div>

                    <!-- Lupa Akun
                    <div class="d-flex justify-content-end align-items-center mb-4">
                        <a href="" class="lupaAkun text-decoration-none text-primary">Lupa Username?</a>
                    </div> -->

                    <!-- Reset Password Button -->
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" name="lupa" class="btn btn-lupa">
                            <i class="fas fa-sync-alt me-2"></i>Ubah Password
                        </button>
                    </div>

                    <div class="text-center mt-3">
                        <p class="mb-0">Sudah ingat Passwordnya?
                            <a href="#" class="text-primary text-decoration-none fw-medium" data-bs-target="#Modallogin" data-bs-toggle="modal" data-bs-dismiss="modal">
                                Login di sini
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Daftar -->
<div class="modal fade" id="Modaldaftar" tabindex="-1" aria-labelledby="ModaldaftarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header daftar">
                <h1 class="modal-title fs-5"><i class="fas fa-user-plus me-2"></i>Daftar Akun</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0 text-center">
                    <p class="mb-0">Jika ingin mendaftar sebagai alumni, silahkan hubungi admin melalui <b class="text-success">WhatsApp</b></p>
                </div>

                <div class="text-center mb-3">
                    <span class="phone-number">+62846583456</span>
                </div>

                <div class="accordion" id="accordionDaftar">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDaftar" aria-expanded="false" aria-controls="collapseDaftar">
                                <i class="fas fa-file-alt me-2"></i>Form Pendaftaran
                            </button>
                        </h2>
                        <div id="collapseDaftar" class="accordion-collapse collapse" data-bs-parent="#accordionDaftar">
                            <div class="accordion-body">
                                <form id="formDaftar" class="needs-validation" novalidate>
                                    <p class="text-muted text-center">Isi data diri Anda untuk mendaftar</p>

                                    <!-- Nama -->
                                    <div class="input-group input-group-custom">
                                        <div class="form-floating me-2">
                                            <input id="nama" type="text" name="nama" class="form-control form-control-custom" placeholder="Nama" required />
                                            <label for="nama"><i class="fas fa-user me-2"></i>Nama Lengkap</label>
                                        </div>
                                        <div class="form-floating">
                                            <input id="nisn" type="text" name="nisn" class="form-control" placeholder="" required autocomplete="off" />
                                            <label for="nisn" class="form-label"><i class="fas fa-rectangle-list me-1"></i> Nisn</label>
                                        </div>
                                    </div>

                                    <!-- Password -->
                                    <div class="input-group input-group-custom">
                                        <div class="form-floating">
                                            <input id="daftarpassword" type="password" name="password" class="form-control form-control-custom" placeholder="Password" minlength="8" required />
                                            <label for="daftarpassword"><i class="fas fa-key me-2"></i>Password</label>
                                        </div>
                                        <span class="input-group-text input-group-text-custom password-toggle" id="togglePasswordDaftar">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>

                                    <div class="input-group input-group-custom">
                                        <span class="input-group-text input-group-text-custom2 rounded-start">
                                            <i class="fas fa-tags"></i>
                                        </span>
                                        <div class="form-floating me-2">
                                            <select class="form-select form-select p-2" aria-label="Default select example" name="jurusan" id="jurusan" required>
                                                <option value="" selected disabled hidden>Pilih Jurusan</option>
                                                <option value="rpl">RPL</option>
                                                <option value="kuliner">KULINER</option>
                                                <option value="atph">ATPH</option>
                                                <option value="busana">BUSANA</option>
                                            </select>
                                        </div>
                                        <div class="form-floating flex-grow-1">
                                            <input type="date" class="form-control form-control-custom flatpickr" id="tahun_lulus" name="tahun_lulus" placeholder="" required autocomplete="off" data-date-format="Y-m-d">
                                            <label for="tahun_lulus" class="form-label">Tahun Lulus</label>
                                        </div>
                                        <div class="input-group-text input-group-text-custom2"><label for="tahun_lulus" class="fas fa-clock fa-lg"></label></div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="warning-box">
                    <p class="mb-0 text-center">
                        <i class="fas fa-exclamation-circle text-danger me-2"></i>
                        Silahkan <a href="#" class="text-primary text-decoration-none fw-medium" data-bs-toggle="modal" data-bs-target="#Modallogin" data-bs-dismiss="modal">Login</a>
                        jika sudah punya akun
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="btnHubungi" class="btn btn-whatsapp">
                    <i class="fab fa-whatsapp me-2"></i>
                    Hubungi Admin
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle password visibility untuk modal login
    document.getElementById('togglePasswordLogin').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Toggle password visibility untuk modal daftar
    document.getElementById('togglePasswordDaftar').addEventListener('click', function() {
        const passwordInput = document.getElementById('daftarpassword');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Form validation untuk semua form dengan class needs-validation
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });

    // WhatsApp link untuk modal daftar
    document.getElementById('btnHubungi').addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.getElementById("formDaftar");

        // Cek validasi form
        if (!form.checkValidity()) {
            // Tampilkan form validation
            form.classList.add("was-validated");

            // Buka accordion jika belum terbuka
            const collapseDaftar = document.getElementById('collapseDaftar');
            const bsCollapse = new bootstrap.Collapse(collapseDaftar, {
                toggle: false
            });
            bsCollapse.show();

            return;
        }

        // Ambil data dari form
        let nama = document.getElementById("nama").value;
        let jurusan = document.getElementById("jurusan").value;
        let tahun_lulus = document.getElementById("tahun_lulus").value;
        let pw = document.getElementById("daftarpassword").value;

        // Buat pesan WhatsApp
        let pesan = `Halo admin BKK, Saya mau daftar di website BKK sebagai alumni
Nama saya ${nama}
Jurusan saya ${jurusan.toUpperCase()}
Tahun Lulus saya pada ${tahun_lulus}
Dan Password yang saya inginkan : ${pw}
Mohon Bantuannya ya,
Terima kasih!`;

        // Buka WhatsApp
        window.open(`https://wa.me/6289503218690?text=${encodeURIComponent(pesan)}`, "_blank");

        // Tutup modal setelah 200ms
        setTimeout(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('Modaldaftar'));
            modal.hide();
        }, 200);
    });

    // Toggle password visibility untuk password baru
    document.getElementById('togglePasswordBaru').addEventListener('click', function() {
        const passwordInput = document.getElementById('passwordBaru');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Toggle password visibility untuk konfirmasi password
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('confirmPassword');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Validasi form lupa password
    document.querySelector('form').addEventListener('submit', function(e) {
        const passwordBaru = document.getElementById('passwordBaru').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (passwordBaru !== confirmPassword) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak cocok!');
        }
    });

    // // WhatsApp link untuk modal lupa akun
    // document.getElementById('lupaAkun').addEventListener('click', function(e) {
    //     e.preventDefault();

    //     // Buat pesan WhatsApp
    //     let pesan = `Halo admin BKK, Saya kehilangan Akun saya sebagai Alumni/Siswa
    //     Bisakah saya minta tolong untuk mendapatkan kembali akun saya?
    //     Terima kasih!`;

    //     // Buka WhatsApp
    //     window.open(`https://wa.me/6289503218690?text=${encodeURIComponent(pesan)}`, "_blank");

    //     // Tutup modal setelah 200ms
    //     setTimeout(() => {
    //         const modal = bootstrap.Modal.getInstance(document.getElementById('Modallupapw'));
    //         modal.hide();
    //     }, 200);
    // });
</script>
<!-- Nis Yang cuma bisa angka -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validasi NISN hanya angka (tambah & edit)
        document.querySelectorAll('input[name="nisn"]').forEach(function(el) {
            el.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });
    });
</script>
<!-- Date -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".flatpickr", {
            dateFormat: "Y-m-d",
            allowInput: true
        });
    });
</script>