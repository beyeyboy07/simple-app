<script setup>
import { ref } from 'vue'
import api from '../services/api'

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
    <div class="container py-4">

        <div class="row justify-content-center">

            <div class="col-12 col-md-8 col-lg-6">

                <div class="card shadow-sm">

                    <div class="card-body">

                        <h3 class="mb-4">
                            ⭐ Generate Bintang
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

                        <!-- Jumlah -->
                        <div class="mb-3">

                            <label class="form-label">
                                Jumlah Bintang
                            </label>

                            <input
                                v-model.number="jumlah"
                                type="number"
                                class="form-control"
                                min="1"
                                max="100"
                                placeholder="Masukkan jumlah"
                            >

                        </div>

                        <!-- Tipe -->
                        <div class="mb-3">

                            <label class="form-label">
                                Tipe
                            </label>

                            <select
                                v-model.number="tipe"
                                class="form-select"
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

                        <button
                            class="btn btn-primary w-100"
                            :disabled="loading"
                            @click="generate"
                        >

                            <span v-if="loading">
                                Generating...
                            </span>

                            <span v-else>
                                Generate
                            </span>

                        </button>

                        <!-- Result -->
                        <div
                            v-if="hasil"
                            class="mt-4"
                        >

                            <h5>
                                Hasil
                            </h5>

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
.result-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    overflow-x: auto;
}

.result-box pre {
    margin: 0;
    font-family: monospace;
    font-size: 18px;
    line-height: 1.4;
}
</style>