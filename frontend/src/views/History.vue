<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const histories = ref([])
const loading = ref(true)
const errorMessage = ref('')

const getHistory = async () => {
    try {
        const response = await api.get('/history')

        histories.value = response.data.data
    } catch (error) {
        console.error(error)

        errorMessage.value =
            error.response?.data?.message ||
            'Gagal mengambil history.'
    } finally {
        loading.value = false
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
    })
}

const getTypeLabel = (type) => {
    switch (type) {
        case 'bintang':
            return '⭐ Bintang'

        case 'terbilang':
            return '🔤 Terbilang'

        case 'input_data':
            return '📝 Input Data'

        default:
            return type
    }
}

onMounted(() => {
    getHistory()
})
</script>

<template>
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>History</h2>
                <p class="text-muted mb-0">
                    Riwayat aktivitas aplikasi.
                </p>
            </div>

            <button
                class="btn btn-secondary"
                @click="router.push('/dashboard')"
            >
                Kembali
            </button>
        </div>

        <div
            v-if="loading"
            class="text-center py-4"
        >
            Memuat history...
        </div>

        <div
            v-else-if="errorMessage"
            class="alert alert-danger"
        >
            {{ errorMessage }}
        </div>

        <div
            v-else-if="histories.length === 0"
            class="alert alert-info"
        >
            Belum ada history.
        </div>

        <div
            v-else
            class="row g-3"
        >
            <div
                v-for="history in histories"
                :key="history.id"
                class="col-12"
            >
                <div class="card">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start gap-3">

                            <div>
                                <h5 class="mb-1">
                                    {{ getTypeLabel(history.type) }}
                                </h5>

                                <p class="mb-1">
                                    {{ history.description }}
                                </p>

                                <small class="text-muted">
                                    {{ formatDate(history.created_at) }}
                                </small>
                            </div>

                            <span class="badge bg-secondary">
                                {{ history.action }}
                            </span>

                        </div>

                        <div
                            v-if="history.data"
                            class="mt-3"
                        >
                            <details>
                                <summary>
                                    Lihat detail
                                </summary>

                                <pre class="mt-2 mb-0">{{ JSON.stringify(history.data, null, 2) }}</pre>
                            </details>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</template>