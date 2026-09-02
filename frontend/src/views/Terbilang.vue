<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const nominal = ref('')
const hasil = ref('')

const loading = ref(false)

const errorMessage = ref('')
const successMessage = ref('')

const formatRupiah = (value) => {
    if (!value) {
        return ''
    }

    const number = String(value)
        .replace(/\D/g, '')

    if (!number) {
        return ''
    }

    return new Intl.NumberFormat(
        'id-ID'
    ).format(number)
}

const handleInput = (event) => {
    const value = event.target.value

    nominal.value = formatRupiah(value)
}

const generate = async () => {
    errorMessage.value = ''
    successMessage.value = ''
    hasil.value = ''

    const numericValue = Number(
        nominal.value.replace(/\./g, '')
    )

    if (!nominal.value || numericValue < 0) {
        errorMessage.value =
            'Nominal wajib diisi.'

        return
    }

    loading.value = true

    try {
        const response = await api.post(
            '/terbilang/generate',
            {
                nominal: numericValue,
            }
        )

        hasil.value =
            response.data.data.hasil

        successMessage.value =
            'Nominal berhasil dikonversi dan disimpan.'

    } catch (error) {
        console.error(error)

        if (error.response?.data?.message) {
            errorMessage.value =
                error.response.data.message
        } else {
            errorMessage.value =
                'Gagal melakukan konversi.'
        }

    } finally {
        loading.value = false
    }
}
</script>

<template>
    <div class="terbilang-page">

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
                        🔤
                    </div>

                    <div>
                        <div class="page-badge">
                            Konverter
                        </div>

                        <h1>
                            Terbilang
                        </h1>

                        <p>
                            Konversi nominal Rupiah menjadi tulisan
                            terbilang secara otomatis.
                        </p>
                    </div>

                </div>

            </div>

            <!-- Content -->
            <div class="row justify-content-center">

                <div class="col-12 col-lg-8 col-xl-7">

                    <div class="converter-card">

                        <div class="card-header-custom">
                            <h4>
                                Konversi Nominal
                            </h4>

                            <p>
                                Masukkan nominal Rupiah yang ingin
                                dikonversi.
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
                                    Konversi gagal
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

                        <!-- Nominal -->
                        <div class="form-group">

                            <label
                                for="nominal"
                                class="form-label"
                            >
                                Nominal Rupiah
                            </label>

                            <div class="input-group nominal-input">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input
                                    id="nominal"
                                    type="text"
                                    inputmode="numeric"
                                    class="form-control form-control-lg"
                                    :value="nominal"
                                    placeholder="132.000"
                                    autocomplete="off"
                                    :disabled="loading"
                                    @input="handleInput"
                                    @keyup.enter="generate"
                                >

                            </div>

                            <div class="form-help">
                                Masukkan angka saja. Contoh: 132.000
                            </div>

                        </div>

                        <!-- Button -->
                        <button
                            type="button"
                            class="btn btn-primary btn-lg convert-button"
                            :disabled="loading"
                            @click="generate"
                        >

                            <span v-if="loading">
                                <span
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>

                                Memproses...
                            </span>

                            <span v-else>
                                🔤 Konversi Nominal
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
                                        Hasil Terbilang
                                    </h4>

                                    <p>
                                        Hasil konversi nominal Rupiah.
                                    </p>
                                </div>

                                <span class="result-badge">
                                    Berhasil
                                </span>

                            </div>

                            <div class="result-box">
                                {{ hasil }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>

<style scoped>
.terbilang-page {
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

    background: #e7f1ff;

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

.converter-card {
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

.nominal-input .input-group-text {
    background: #f8f9fa;

    border-color: #ced4da;

    font-weight: 600;

    color: #495057;
}

.nominal-input .form-control {
    border-radius: 0 10px 10px 0;
}

.nominal-input .input-group-text {
    border-radius: 10px 0 0 10px;
}

.form-control {
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.form-control:focus {
    border-color: #86b7fe;

    box-shadow:
        0 0 0 3px rgba(13, 110, 253, 0.12);
}

.form-control:disabled {
    background: #f8f9fa;
}

.form-help {
    color: #6c757d;

    font-size: 12px;

    margin-top: 6px;
}

/* Button */

.convert-button {
    width: 100%;

    min-height: 50px;

    border-radius: 10px;

    font-weight: 600;

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}

.convert-button:hover:not(:disabled) {
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
    padding: 18px 20px;

    background: #f8f9fa;

    border: 1px solid #dee2e6;

    border-radius: 12px;

    color: #212529;

    font-size: 18px;
    font-weight: 600;

    line-height: 1.6;

    text-transform: capitalize;
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

    .converter-card {
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

        font-size: 16px;
    }
}
</style>