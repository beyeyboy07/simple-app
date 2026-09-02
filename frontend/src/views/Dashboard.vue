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

        <nav class="navbar navbar-dark bg-dark">
            <div class="container">
                <span class="navbar-brand">
                    Simple App
                </span>

                <button
                    class="btn btn-outline-light btn-sm"
                    @click="logout"
                >
                    Logout
                </button>
            </div>
        </nav>

        <main class="container py-4">

            <h2>Dashboard</h2>

            <p class="text-muted">
                Selamat datang di Simple App.
            </p>

            <div class="row g-3 mt-2">

                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5>⭐ Bintang</h5>

                            <p class="text-muted">
                                Generate pola bintang.
                            </p>

                          <button class="btn btn-primary" @click="router.push('/star')">
                                Buka
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5>🔤 Terbilang</h5>

                            <p class="text-muted">
                                Konversi nominal menjadi terbilang.
                            </p>

                           <button class="btn btn-primary" @click="router.push('/terbilang')" >
                                Buka
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5>📝 Input Data</h5>

                            <p class="text-muted">
                                Input data pengguna.
                            </p>

                            <button
                                class="btn btn-primary"
                                @click="router.push('/input-data')"
                            >
                                Buka
                            </button>
                        </div>
                    </div>
                </div>


                <div class="col-12 col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5>📜 History</h5>

                            <p class="text-muted">
                                Melihat riwayat aktivitas.
                            </p>

                            <button
                                class="btn btn-primary"
                                @click="router.push('/history')"
                            >
                                Buka
                            </button>
                        </div>
                    </div>
                </div>

            </div>

        </main>

    </div>
</template>