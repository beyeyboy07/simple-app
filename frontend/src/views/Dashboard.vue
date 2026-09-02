<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const user = JSON.parse(localStorage.getItem('user') || '{}')
const logoutLoading = ref(false)

const logout = async () => {
    if (logoutLoading.value) return

    logoutLoading.value = true

    try {
        await api.post('/logout')
    } catch (error) {
        console.error(error)
    } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')

        router.push('/login')
    }
}

const openFeature = (path) => {
    router.push(path)
}
</script>

<template>
    <div class="dashboard">

        <!-- Navbar -->
        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container">

                <div class="navbar-brand-wrapper">
                    <div class="brand-icon">
                        SA
                    </div>

                    <span class="navbar-brand mb-0">
                        Simple App
                    </span>
                </div>

                <div class="d-flex align-items-center gap-3">

                    <div
                        v-if="user.name || user.email"
                        class="user-info d-none d-sm-block"
                    >
                        <div class="user-name">
                            {{ user.name || 'User' }}
                        </div>

                        <div class="user-email">
                            {{ user.email }}
                        </div>
                    </div>

                    <button
                        class="btn btn-outline-light btn-sm logout-button"
                        :disabled="logoutLoading"
                        @click="logout"
                    >
                        <span v-if="logoutLoading">
                            <span class="spinner-border spinner-border-sm me-1"></span>
                            Keluar...
                        </span>

                        <span v-else>
                            Logout
                        </span>
                    </button>

                </div>

            </div>
        </nav>

        <!-- Main -->
        <main class="container dashboard-content">

            <!-- Welcome -->
            <section class="welcome-section">

                <div class="welcome-badge">
                    <span class="status-dot"></span>
                    Dashboard
                </div>

                <h1>
                    Selamat Datang<span v-if="user.name">, {{ user.name }}</span>
                </h1>
            </section>

            <!-- Feature Section -->
            <section>

                <div class="section-heading">
                    <div>
                        <h4>
                            Fitur Aplikasi
                        </h4>

                        <p>
                            Pilih fitur yang ingin Anda gunakan.
                        </p>
                    </div>
                </div>

                <div class="row g-4">

                    <!-- Bintang -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div
                            class="feature-card"
                            @click="openFeature('/star')"
                        >

                            <div class="feature-top">
                                <div class="feature-icon star-icon">
                                    ⭐
                                </div>

                                <span class="feature-arrow">
                                    →
                                </span>
                            </div>

                            <div class="feature-content">
                                <h5>
                                    Bintang
                                </h5>

                                <p>
                                    Generate pola bintang dengan
                                    beberapa pilihan bentuk.
                                </p>
                            </div>

                            <button
                                class="btn btn-primary feature-button"
                                @click.stop="openFeature('/star')"
                            >
                                Buka Fitur
                            </button>

                        </div>
                    </div>

                    <!-- Terbilang -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div
                            class="feature-card"
                            @click="openFeature('/terbilang')"
                        >

                            <div class="feature-top">
                                <div class="feature-icon text-icon">
                                    🔤
                                </div>

                                <span class="feature-arrow">
                                    →
                                </span>
                            </div>

                            <div class="feature-content">
                                <h5>
                                    Terbilang
                                </h5>

                                <p>
                                    Konversi nominal Rupiah menjadi
                                    tulisan terbilang.
                                </p>
                            </div>

                            <button
                                class="btn btn-primary feature-button"
                                @click.stop="openFeature('/terbilang')"
                            >
                                Buka Fitur
                            </button>

                        </div>
                    </div>

                    <!-- Input Data -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div
                            class="feature-card"
                            @click="openFeature('/input-data')"
                        >

                            <div class="feature-top">
                                <div class="feature-icon form-icon">
                                    📝
                                </div>

                                <span class="feature-arrow">
                                    →
                                </span>
                            </div>

                            <div class="feature-content">
                                <h5>
                                    Input Data
                                </h5>

                                <p>
                                    Masukkan dan simpan data pengguna
                                    ke dalam aplikasi.
                                </p>
                            </div>

                            <button
                                class="btn btn-primary feature-button"
                                @click.stop="openFeature('/input-data')"
                            >
                                Buka Fitur
                            </button>

                        </div>
                    </div>

                    <!-- History -->
                    <div class="col-12 col-md-6 col-lg-3">
                        <div
                            class="feature-card"
                            @click="openFeature('/history')"
                        >

                            <div class="feature-top">
                                <div class="feature-icon history-icon">
                                    📜
                                </div>

                                <span class="feature-arrow">
                                    →
                                </span>
                            </div>

                            <div class="feature-content">
                                <h5>
                                    History
                                </h5>

                                <p>
                                    Lihat riwayat aktivitas yang
                                    telah dilakukan.
                                </p>
                            </div>

                            <button
                                class="btn btn-primary feature-button"
                                @click.stop="openFeature('/history')"
                            >
                                Buka Fitur
                            </button>

                        </div>
                    </div>

                </div>

            </section>

        </main>

        <!-- Footer -->
        <footer class="dashboard-footer">
            Simple App &copy; 2026
        </footer>

    </div>
</template>

<style scoped>
.dashboard {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
}

/* Navbar */

.navbar {
    min-height: 68px;
}

.navbar-brand-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}

.brand-icon {
    width: 36px;
    height: 36px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #0d6efd;
    color: #ffffff;

    border-radius: 9px;

    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
}

.navbar-brand {
    font-size: 18px;
    font-weight: 700;
}

.user-info {
    text-align: right;
    line-height: 1.2;
}

.user-name {
    color: #ffffff;
    font-size: 13px;
    font-weight: 600;
}

.user-email {
    color: #adb5bd;
    font-size: 11px;
    margin-top: 3px;
}

.logout-button {
    border-radius: 8px;
    min-width: 76px;
}

/* Main */

.dashboard-content {
    flex: 1;
    padding-top: 52px;
    padding-bottom: 60px;
}

/* Welcome */

.welcome-section {
    margin-bottom: 48px;
}

.welcome-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;

    padding: 6px 11px;

    background: #e7f1ff;
    color: #0d6efd;

    border-radius: 20px;

    font-size: 12px;
    font-weight: 600;

    margin-bottom: 16px;
}

.status-dot {
    width: 7px;
    height: 7px;

    background: #0d6efd;

    border-radius: 50%;
}

.welcome-section h1 {
    font-size: 34px;
    font-weight: 700;

    color: #212529;

    margin-bottom: 10px;
}

.welcome-section p {
    color: #6c757d;
    font-size: 16px;

    margin: 0;

    max-width: 650px;
}

/* Section */

.section-heading {
    margin-bottom: 20px;
}

.section-heading h4 {
    font-size: 20px;
    font-weight: 700;

    margin-bottom: 4px;
}

.section-heading p {
    color: #6c757d;
    font-size: 14px;

    margin: 0;
}

/* Feature Card */

.feature-card {
    height: 100%;

    display: flex;
    flex-direction: column;

    background: #ffffff;

    border: 1px solid #e9ecef;
    border-radius: 16px;

    padding: 22px;

    cursor: pointer;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        border-color 0.2s ease;
}

.feature-card:hover {
    transform: translateY(-5px);

    border-color: #d6e4ff;

    box-shadow:
        0 12px 30px rgba(0, 0, 0, 0.08);
}

.feature-top {
    display: flex;
    align-items: center;
    justify-content: space-between;

    margin-bottom: 20px;
}

.feature-icon {
    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 13px;

    font-size: 25px;
}

.star-icon {
    background: #fff4cc;
}

.text-icon {
    background: #e7f1ff;
}

.form-icon {
    background: #e9f7ef;
}

.history-icon {
    background: #f1eaff;
}

.feature-arrow {
    color: #adb5bd;

    font-size: 20px;

    transition:
        transform 0.2s ease,
        color 0.2s ease;
}

.feature-card:hover .feature-arrow {
    color: #0d6efd;
    transform: translateX(3px);
}

.feature-content {
    flex: 1;
}

.feature-content h5 {
    font-size: 17px;
    font-weight: 700;

    margin-bottom: 8px;
}

.feature-content p {
    color: #6c757d;

    font-size: 13px;
    line-height: 1.6;

    margin-bottom: 24px;
}

.feature-button {
    border-radius: 9px;

    font-size: 14px;
    font-weight: 600;

    min-height: 42px;

    transition: all 0.2s ease;
}

.feature-button:hover {
    box-shadow:
        0 5px 12px rgba(13, 110, 253, 0.18);
}

/* Footer */

.dashboard-footer {
    padding: 20px;

    text-align: center;

    color: #adb5bd;

    font-size: 12px;

    border-top: 1px solid #e9ecef;

    background: #ffffff;
}

/* Mobile */

@media (max-width: 576px) {

    .navbar {
        min-height: 62px;
    }

    .navbar-brand {
        font-size: 16px;
    }

    .brand-icon {
        width: 32px;
        height: 32px;
        font-size: 12px;
    }

    .dashboard-content {
        padding-top: 34px;
        padding-bottom: 40px;
    }

    .welcome-section {
        margin-bottom: 36px;
    }

    .welcome-section h1 {
        font-size: 27px;
        line-height: 1.3;
    }

    .welcome-section p {
        font-size: 14px;
        line-height: 1.6;
    }

    .section-heading h4 {
        font-size: 18px;
    }

    .feature-card {
        padding: 20px;
    }

    .logout-button {
        min-width: auto;
        padding-left: 12px;
        padding-right: 12px;
    }
}
</style>
