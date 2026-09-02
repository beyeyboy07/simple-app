```vue
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

        errorMessage.value = 'Gagal mengambil CAPTCHA.'
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

        successMessage.value = 'Login berhasil.'

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

        <div class="login-wrapper">

            <!-- Logo / Header -->
            <div class="text-center mb-4">

                <div class="app-logo">
                    SA
                </div>

                <h2 class="app-title">
                    Simple App
                </h2>

                <p class="app-subtitle">
                    Silakan masuk untuk melanjutkan
                </p>

            </div>

            <!-- Login Card -->
            <div class="login-card">

                <div class="mb-4">
                    <h4 class="fw-bold mb-1">
                        Login
                    </h4>

                    <p class="text-muted small mb-0">
                        Masukkan akun Anda
                    </p>
                </div>

                <!-- Error -->
                <div
                    v-if="errorMessage"
                    class="alert alert-danger small"
                >
                    {{ errorMessage }}
                </div>

                <!-- Success -->
                <div
                    v-if="successMessage"
                    class="alert alert-success small"
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
                        class="form-control form-control-lg"
                        placeholder="nama@email.com"
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
                        class="form-control form-control-lg"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                    >

                </div>

                <!-- CAPTCHA -->
                <div class="mb-4">

                    <label class="form-label">
                        CAPTCHA
                    </label>

                    <div class="captcha-container">

                        <div class="captcha-box">

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

                        </div>

                        <button
                            type="button"
                            class="refresh-button"
                            @click="loadCaptcha"
                            :disabled="captchaLoading"
                            title="Refresh CAPTCHA"
                        >
                            <span
                                :class="{ spinning: captchaLoading }"
                            >
                                ↻
                            </span>
                        </button>

                    </div>

                    <input
                        v-model="captchaCode"
                        type="text"
                        class="form-control form-control-lg mt-2"
                        placeholder="Masukkan kode CAPTCHA"
                        maxlength="6"
                        autocomplete="off"
                        @keyup.enter="login"
                    >

                </div>

                <!-- Login Button -->
                <button
                    type="button"
                    class="btn btn-primary btn-lg w-100 login-button"
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

            <p class="text-center text-muted small mt-4 mb-0">
                Simple App &copy; 2026
            </p>

        </div>

    </div>
</template>

<style scoped>
.login-page {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    padding: 30px 20px;
}

.login-wrapper {
    width: 100%;
    max-width: 430px;
}

.app-logo {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #0d6efd;
    color: #ffffff;

    border-radius: 16px;

    font-size: 22px;
    font-weight: 700;
    letter-spacing: 1px;

    box-shadow: 0 8px 20px rgba(13, 110, 253, 0.2);
}

.app-title {
    font-weight: 700;
    margin-bottom: 6px;
}

.app-subtitle {
    color: #6c757d;
    margin-bottom: 0;
}

.login-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    padding: 30px;

    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.07);
}

.form-label {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 7px;
}

.form-control {
    border-radius: 10px;
}

.form-control:focus {
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.12);
}

.captcha-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.captcha-box {
    flex: 1;
    height: 62px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 10px;

    overflow: hidden;
}

.captcha-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.captcha-placeholder {
    color: #6c757d;
    font-size: 14px;
}

.refresh-button {
    width: 52px;
    height: 52px;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #ffffff;
    border: 1px solid #ced4da;
    border-radius: 10px;

    font-size: 25px;
    color: #495057;

    cursor: pointer;
    transition: all 0.2s ease;
}

.refresh-button:hover:not(:disabled) {
    background: #f1f3f5;
    border-color: #adb5bd;
}

.refresh-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.spinning {
    display: inline-block;
    animation: spin 0.8s linear infinite;
}

.login-button {
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.login-button:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 15px rgba(13, 110, 253, 0.2);
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

@media (max-width: 480px) {
    .login-page {
        padding: 20px 15px;
    }

    .login-card {
        padding: 24px 20px;
        border-radius: 16px;
    }

    .app-logo {
        width: 56px;
        height: 56px;
        font-size: 20px;
    }

    .app-title {
        font-size: 24px;
    }
}
</style>
```
