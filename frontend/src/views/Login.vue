<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const email = ref('')
const password = ref('')
const captchaCode = ref('')

const captchaId = ref('')
const captchaImage = ref('')

const loading = ref(false)
const captchaLoading = ref(false)

const errorMessage = ref('')
const successMessage = ref('')

const loadCaptcha = async () => {
    captchaLoading.value = true
    errorMessage.value = ''

    try {
        const response = await api.get('/captcha')

        captchaId.value = response.data.captcha_id
        captchaImage.value = response.data.image

        captchaCode.value = ''
    } catch (error) {
        console.error(error)

        errorMessage.value =
            'Gagal mengambil CAPTCHA.'
    } finally {
        captchaLoading.value = false
    }
}

const login = async () => {
    errorMessage.value = ''
    successMessage.value = ''

    if (!email.value || !password.value || !captchaCode.value) {
        errorMessage.value =
            'Email, password, dan CAPTCHA wajib diisi.'

        return
    }

    loading.value = true

    try {
        const response = await api.post('/login', {
            email: email.value,
            password: password.value,
            captcha_id: captchaId.value,
            captcha_code: captchaCode.value,
        })

        localStorage.setItem(
            'token',
            response.data.token
        )

        localStorage.setItem(
            'user',
            JSON.stringify(response.data.user)
        )

        successMessage.value =
            'Login berhasil.'

        router.push('/dashboard')

    } catch (error) {
        console.error(error)

        if (error.response?.data?.message) {
            errorMessage.value =
                error.response.data.message
        } else {
            errorMessage.value =
                'Terjadi kesalahan saat login.'
        }

        // CAPTCHA hanya bisa digunakan sekali,
        // jadi generate ulang setelah login gagal.
        await loadCaptcha()

    } finally {
        loading.value = false
    }
}

onMounted(() => {
    loadCaptcha()
})
</script>

<template>
    <div class="login-page">

        <div class="login-card">

            <div class="text-center mb-4">

                <div class="logo">
                    SIMPLE APP
                </div>

                <h3 class="mt-3">
                    Login
                </h3>

                <p class="text-muted">
                    Silakan masuk untuk melanjutkan
                </p>

            </div>

            <!-- Error -->
            <div
                v-if="errorMessage"
                class="alert alert-danger"
            >
                {{ errorMessage }}
            </div>

            <!-- Success -->
            <div
                v-if="successMessage"
                class="alert alert-success"
            >
                {{ successMessage }}
            </div>

            <!-- Email -->
            <div class="mb-3">

                <label class="form-label">
                    Email
                </label>

                <input
                    v-model="email"
                    type="email"
                    class="form-control"
                    placeholder="Masukkan email"
                    autocomplete="email"
                >

            </div>

            <!-- Password -->
            <div class="mb-3">

                <label class="form-label">
                    Password
                </label>

                <input
                    v-model="password"
                    type="password"
                    class="form-control"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                >

            </div>

            <!-- CAPTCHA -->
            <div class="mb-3">

                <label class="form-label">
                    CAPTCHA
                </label>

                <div class="captcha-container mb-2">

                    <img
                        v-if="captchaImage"
                        :src="captchaImage"
                        alt="CAPTCHA"
                        class="captcha-image"
                    >

                    <div
                        v-else
                        class="captcha-placeholder"
                    >
                        Loading...
                    </div>

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        @click="loadCaptcha"
                        :disabled="captchaLoading"
                        title="Refresh CAPTCHA"
                    >
                        ↻
                    </button>

                </div>

                <input
                    v-model="captchaCode"
                    type="text"
                    class="form-control"
                    placeholder="Masukkan kode CAPTCHA"
                    maxlength="6"
                    autocomplete="off"
                    @keyup.enter="login"
                >

            </div>

            <!-- Login -->
            <button
                type="button"
                class="btn btn-primary w-100"
                :disabled="loading"
                @click="login"
            >

                <span v-if="loading">
                    Memproses...
                </span>

                <span v-else>
                    Login
                </span>

            </button>

        </div>

    </div>
</template>

<style scoped>
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f6f8;
    padding: 20px;
}

.login-card {
    width: 100%;
    max-width: 420px;
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
}

.logo {
    font-size: 24px;
    font-weight: 700;
    letter-spacing: 1px;
}

.captcha-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.captcha-image {
    height: 60px;
    width: 180px;
    object-fit: contain;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    background: #f8f9fa;
}

.captcha-placeholder {
    width: 180px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    color: #6c757d;
}

@media (max-width: 480px) {
    .login-card {
        padding: 24px 20px;
    }

    .captcha-image,
    .captcha-placeholder {
        width: 150px;
    }
}
</style>