<script setup>
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const logout = async () => {
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
</script>

<template>
    <div class="dashboard">

        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container py-2">
                <span class="navbar-brand fw-bold">
                    Simple App
                </span>

                <button
                    class="btn btn-outline-light btn-sm px-3"
                    @click="logout"
                >
                    Logout
                </button>
            </div>
        </nav>

        <main class="container py-5">

            <div class="welcome-section mb-5">
                <span class="badge bg-primary-subtle text-primary mb-3">
                    Dashboard
                </span>

                <h1 class="fw-bold mb-2">
                    Selamat Datang 👋
                </h1>

                <p class="text-muted mb-0">
                    Kelola dan gunakan berbagai fitur yang tersedia di Simple App.
                </p>
            </div>

            <div class="row g-4">

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            ⭐
                        </div>

                        <h5 class="fw-bold">
                            Bintang
                        </h5>

                        <p class="text-muted small">
                            Generate pola bintang dengan beberapa pilihan bentuk.
                        </p>

                        <button
                            class="btn btn-primary w-100 mt-auto"
                            @click="router.push('/star')"
                        >
                            Buka Fitur
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            🔤
                        </div>

                        <h5 class="fw-bold">
                            Terbilang
                        </h5>

                        <p class="text-muted small">
                            Konversi nominal Rupiah menjadi tulisan terbilang.
                        </p>

                        <button
                            class="btn btn-primary w-100 mt-auto"
                            @click="router.push('/terbilang')"
                        >
                            Buka Fitur
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            📝
                        </div>

                        <h5 class="fw-bold">
                            Input Data
                        </h5>

                        <p class="text-muted small">
                            Masukkan dan simpan data pengguna ke dalam aplikasi.
                        </p>

                        <button
                            class="btn btn-primary w-100 mt-auto"
                            @click="router.push('/input-data')"
                        >
                            Buka Fitur
                        </button>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="feature-card h-100">
                        <div class="feature-icon">
                            📜
                        </div>

                        <h5 class="fw-bold">
                            History
                        </h5>

                        <p class="text-muted small">
                            Lihat riwayat aktivitas yang telah dilakukan.
                        </p>

                        <button
                            class="btn btn-primary w-100 mt-auto"
                            @click="router.push('/history')"
                        >
                            Buka Fitur
                        </button>
                    </div>
                </div>

            </div>

        </main>

    </div>
</template>

<style scoped>
.dashboard {
    min-height: 100vh;
    background: #f8f9fa;
}

.welcome-section {
    max-width: 700px;
}

.feature-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    transition: all 0.2s ease;
}

.feature-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
}

.feature-icon {
    width: 56px;
    height: 56px;
    border-radius: 14px;
    background: #f1f3f5;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    margin-bottom: 20px;
}

.feature-card h5 {
    margin-bottom: 8px;
}

.feature-card p {
    line-height: 1.6;
    margin-bottom: 24px;
}

@media (max-width: 576px) {
    main.container {
        padding-top: 32px !important;
        padding-bottom: 32px !important;
    }

    .welcome-section {
        margin-bottom: 32px !important;
    }

    .welcome-section h1 {
        font-size: 28px;
    }

    .feature-card {
        padding: 20px;
    }
}
</style>
