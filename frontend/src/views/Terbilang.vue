<script setup>
import { ref } from 'vue'
import api from '../services/api'

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
    <div class="container py-4">

        <div class="row justify-content-center">

            <div class="col-12 col-md-8 col-lg-6">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h3 class="mb-4">
                            🔤 Terbilang
                        </h3>

                        <div
                            v-if="errorMessage"
                            class="alert alert-danger"
                        >
                            {{ errorMessage }}
                        </div>

                        <div
                            v-if="successMessage"
                            class="alert alert-success"
                        >
                            {{ successMessage }}
                        </div>

                        <div class="mb-3">

                            <label class="form-label">
                                Nominal Rupiah
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    Rp
                                </span>

                                <input
                                    type="text"
                                    class="form-control"
                                    :value="nominal"
                                    placeholder="132.000"
                                    @input="handleInput"
                                >

                            </div>

                        </div>

                        <button
                            class="btn btn-primary w-100"
                            :disabled="loading"
                            @click="generate"
                        >

                            <span v-if="loading">
                                Memproses...
                            </span>

                            <span v-else>
                                Konversi
                            </span>

                        </button>

                        <div
                            v-if="hasil"
                            class="mt-4"
                        >

                            <label class="form-label">
                                Hasil Terbilang
                            </label>

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
.result-box {
    padding: 15px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
    font-size: 18px;
    text-transform: capitalize;
}

@media (max-width: 576px) {
    .card-body {
        padding: 20px;
    }
}
</style>