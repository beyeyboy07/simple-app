<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const jumlah = ref(5)
const tipe = ref(1)

const hasil = ref('')
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const generate = async () => {
    errorMessage.value = ''
    successMessage.value = ''
    hasil.value = ''

    if (!jumlah.value) {
        errorMessage.value =
            'Jumlah bintang wajib diisi.'

        return
    }

    loading.value = true

    try {
        const response = await api.post(
            '/stars/generate',
            {
                jumlah: Number(jumlah.value),
                tipe: Number(tipe.value),
            }
        )

        hasil.value =
            response.data.data.hasil

        successMessage.value =
            'Bintang berhasil digenerate dan disimpan.'

    } catch (error) {
        console.error(error)

        if (error.response?.data?.message) {
            errorMessage.value =
                error.response.data.message
        } else {
            errorMessage.value =
                'Gagal generate bintang.'
        }

    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="star-page">

        <div class="container page-container">

            <!-- Header -->
            <div class="page-header">

                <button
                    type="button"
                    class="back-button"
                    @click="router.push('/dashboard')"
                >
                    <span>←</span>
                    Kembali ke Dashboard
                </button>

                <div class="page-title-wrapper">

                    <div class="page-icon">
                        ⭐
                    </div>

                    <div>
                        <div class="page-badge">
                            Generator
                        </div>

                        <h1>
                            Generate Bintang
                        </h1>

                        <p>
                            Buat pola bintang dengan memilih jumlah
                            dan tipe pola yang tersedia.
                        </p>
                    </div>

                </div>

            </div>

            <!-- Content -->
            <div class="row justify-content-center">

                <div class="col-12 col-lg-8 col-xl-7">

                    <div class="generator-card">

                        <div class="card-header-custom">
                            <h4>
                                Pengaturan Pola
                            </h4>

                            <p>
                                Tentukan jumlah bintang dan tipe pola
                                yang ingin dibuat.
                            </p>
                        </div>

                        <!-- Error -->
                        <div
                            v-if="errorMessage"
                            class="alert alert-danger custom-alert"
                            role="alert"
                        >
                            <div class="alert-icon">
                                !
                            </div>

                            <div>
                                <div class="alert-title">
                                    Generate gagal
                                </div>

                                <div>
                                    {{ errorMessage }}
                                </div>
                            </div>
                        </div>

                        <!-- Success -->
                        <div
                            v-if="successMessage"
                            class="alert alert-success custom-alert"
                            role="alert"
                        >
                            <div class="alert-icon">
                                ✓
                            </div>

                            <div>
                                <div class="alert-title">
                                    Berhasil
                                </div>

                                <div>
                                    {{ successMessage }}
                                </div>
                            </div>
                        </div>

                        <!-- Jumlah -->
                        <div class="form-group">

                            <label
                                for="jumlah"
                                class="form-label"
                            >
                                Jumlah Bintang
                            </label>

                            <input
                                id="jumlah"
                                v-model.number="jumlah"
                                type="number"
                                class="form-control form-control-lg"
                                min="1"
                                max="100"
                                placeholder="Masukkan jumlah bintang"
                                :disabled="loading"
                            >

                            <div class="form-help">
                                Masukkan jumlah bintang antara 1–100.
                            </div>

                        </div>

                        <!-- Tipe -->
                        <div class="form-group">

                            <label
                                for="tipe"
                                class="form-label"
                            >
                                Tipe Pola
                            </label>

                            <select
                                id="tipe"
                                v-model.number="tipe"
                                class="form-select form-select-lg"
                                :disabled="loading"
                            >
                                <option :value="1">
                                    Segitiga Siku Kanan Bawah
                                </option>

                                <option :value="2">
                                    Segitiga Biasa
                                </option>

                                <option :value="3">
                                    Segitiga Siku Kiri Bawah
                                </option>
                            </select>

                        </div>

                        <!-- Generate -->
                        <button
                            type="button"
                            class="btn btn-primary btn-lg generate-button"
                            :disabled="loading"
                            @click="generate"
                        >

                            <span v-if="loading">
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>

                                Generating...
                            </span>

                            <span v-else>
                                ⭐ Generate Bintang
                            </span>

                        </button>

                        <!-- Result -->
                        <div
                            v-if="hasil"
                            class="result-section"
                        >

                            <div class="result-header">

                                <div>
                                    <h4>
                                        Hasil Generate
                                    </h4>

                                    <p>
                                        Pola bintang yang berhasil dibuat.
                                    </p>
                                </div>

                                <span class="result-badge">
                                    Berhasil
                                </span>

                            </div>

                            <div class="result-box">
                                <pre>{{ hasil }}</pre>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>

<style scoped>
.star-page {
    min-height: 100vh;
    background: #f8f9fa;
}

.page-container {
    padding-top: 32px;
    padding-bottom: 60px;
}

/* Header */

.page-header {
    margin-bottom: 32px;
}

.back-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;

    border: 0;
    background: transparent;

    color: #6c757d;

    font-size: 14px;
    font-weight: 600;

    padding: 6px 0;

    margin-bottom: 24px;

    cursor: pointer;

    transition: color 0.2s ease;
}

.back-button:hover {
    color: #0d6efd;
}

.back-button span {
    font-size: 20px;
    line-height: 1;
}

.page-title-wrapper {
    display: flex;
    align-items: flex-start;
    gap: 16px;
}

.page-icon {
    width: 58px;
    height: 58px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #fff4cc;

    border-radius: 14px;

    font-size: 28px;
}

.page-badge {
    display: inline-block;

    color: #0d6efd;

    font-size: 12px;
    font-weight: 700;

    margin-bottom: 4px;
}

.page-title-wrapper h1 {
    font-size: 30px;
    font-weight: 700;

    color: #212529;

    margin-bottom: 6px;
}

.page-title-wrapper p {
    color: #6c757d;

    font-size: 14px;

    margin: 0;

    line-height: 1.6;
}

/* Card */

.generator-card {
    background: #ffffff;

    border: 1px solid #e9ecef;
    border-radius: 18px;

    padding: 30px;

    box-shadow:
        0 8px 25px rgba(0, 0, 0, 0.05);
}

.card-header-custom {
    margin-bottom: 26px;
}

.card-header-custom h4 {
    font-size: 20px;
    font-weight: 700;

    margin-bottom: 5px;
}

.card-header-custom p {
    color: #6c757d;

    font-size: 14px;

    margin: 0;
}

/* Alert */

.custom-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;

    border-radius: 10px;

    padding: 12px 14px;

    font-size: 13px;

    margin-bottom: 22px;
}

.alert-icon {
    width: 22px;
    height: 22px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    font-size: 12px;
    font-weight: 700;
}

.alert-title {
    font-weight: 700;
    margin-bottom: 2px;
}

/* Form */

.form-group {
    margin-bottom: 22px;
}

.form-label {
    font-size: 14px;
    font-weight: 600;

    margin-bottom: 8px;
}

.form-control,
.form-select {
    border-radius: 10px;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.form-control:focus,
.form-select:focus {
    border-color: #86b7fe;

    box-shadow:
        0 0 0 3px rgba(13, 110, 253, 0.12);
}

.form-control:disabled,
.form-select:disabled {
    background: #f8f9fa;
}

.form-help {
    color: #6c757d;

    font-size: 12px;

    margin-top: 6px;
}

/* Button */

.generate-button {
    width: 100%;

    min-height: 50px;

    border-radius: 10px;

    font-weight: 600;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.generate-button:hover:not(:disabled) {
    transform: translateY(-1px);

    box-shadow:
        0 6px 15px rgba(13, 110, 253, 0.2);
}

/* Result */

.result-section {
    margin-top: 32px;

    padding-top: 28px;

    border-top: 1px solid #e9ecef;
}

.result-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    gap: 16px;

    margin-bottom: 14px;
}

.result-header h4 {
    font-size: 18px;
    font-weight: 700;

    margin-bottom: 4px;
}

.result-header p {
    color: #6c757d;

    font-size: 13px;

    margin: 0;
}

.result-badge {
    flex-shrink: 0;

    padding: 5px 10px;

    background: #d1e7dd;
    color: #146c43;

    border-radius: 20px;

    font-size: 11px;
    font-weight: 700;
}

.result-box {
    background: #f8f9fa;

    border: 1px solid #dee2e6;

    border-radius: 12px;

    padding: 20px;

    overflow-x: auto;
}

.result-box pre {
    margin: 0;

    font-family:
        'Courier New',
        Courier,
        monospace;

    font-size: 18px;
    line-height: 1.5;

    color: #212529;
}

/* Mobile */

@media (max-width: 576px) {

    .page-container {
        padding-top: 22px;
        padding-bottom: 40px;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .back-button {
        margin-bottom: 20px;
    }

    .page-title-wrapper {
        gap: 12px;
    }

    .page-icon {
        width: 48px;
        height: 48px;

        border-radius: 12px;

        font-size: 23px;
    }

    .page-title-wrapper h1 {
        font-size: 25px;
    }

    .page-title-wrapper p {
        font-size: 13px;
    }

    .generator-card {
        padding: 22px 20px;

        border-radius: 16px;
    }

    .card-header-custom h4 {
        font-size: 18px;
    }

    .result-header {
        flex-direction: column;
        gap: 8px;
    }

    .result-box {
        padding: 16px;
    }

    .result-box pre {
        font-size: 16px;
    }
}
</style>