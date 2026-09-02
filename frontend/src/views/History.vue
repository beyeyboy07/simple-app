<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'

const router = useRouter()

const histories = ref([])
const loading = ref(true)
const errorMessage = ref('')

const getHistory = async () => {
    loading.value = true
    errorMessage.value = ''

    try {
        const response = await api.get('/history')

        histories.value = response.data.data || []
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
    if (!date) {
        return '-'
    }

    const parsedDate = new Date(date)

    if (Number.isNaN(parsedDate.getTime())) {
        return '-'
    }

    return parsedDate.toLocaleString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const getTypeLabel = (type) => {
    switch (type) {
        case 'bintang':
            return 'Bintang'

        case 'terbilang':
            return 'Terbilang'

        case 'input_data':
            return 'Input Data'

        default:
            return type || 'Aktivitas'
    }
}

const getTypeIcon = (type) => {
    switch (type) {
        case 'bintang':
            return '⭐'

        case 'terbilang':
            return '🔤'

        case 'input_data':
            return '📝'

        default:
            return '📋'
    }
}

const getTypeClass = (type) => {
    switch (type) {
        case 'bintang':
            return 'type-star'

        case 'terbilang':
            return 'type-terbilang'

        case 'input_data':
            return 'type-input'

        default:
            return 'type-default'
    }
}

const formatAction = (action) => {
    if (!action) {
        return 'Aktivitas'
    }

    return action
        .replace(/_/g, ' ')
        .replace(/\b\w/g, char => char.toUpperCase())
}

const formatJson = (data) => {
    try {
        return JSON.stringify(data, null, 2)
    } catch {
        return String(data)
    }
}

onMounted(() => {
    getHistory()
})
</script>

<template>
    <div class="history-page">
        <div class="container page-container">

            <!-- HEADER -->
            <div class="page-header">

                <button
                    type="button"
                    class="back-button"
                    @click="router.push('/dashboard')"
                >
                    <span>←</span>
                    Kembali ke Dashboard
                </button>

                <div class="page-title-row">

                    <div class="page-icon">
                        🕘
                    </div>

                    <div class="page-title-content">

                        <div class="page-badge">
                            Aktivitas
                        </div>

                        <h1>History</h1>

                        <p>
                            Riwayat aktivitas yang telah
                            dilakukan pada aplikasi.
                        </p>

                    </div>

                </div>

            </div>

            <!-- SUMMARY -->
            <div
                v-if="!loading && !errorMessage"
                class="summary-card"
            >
                <div class="summary-icon">
                    📊
                </div>

                <div class="summary-content">
                    <span>Total Aktivitas</span>

                    <strong>
                        {{ histories.length }}
                    </strong>
                </div>

                <div
                    v-if="histories.length > 0"
                    class="summary-status"
                >
                    <span class="status-dot"></span>
                    History tersedia
                </div>
            </div>

            <!-- LOADING -->
            <div
                v-if="loading"
                class="state-card"
            >
                <div class="loading-spinner"></div>

                <h3>Memuat history...</h3>

                <p>
                    Sedang mengambil riwayat aktivitas Anda.
                </p>
            </div>

            <!-- ERROR -->
            <div
                v-else-if="errorMessage"
                class="state-card error-state"
            >
                <div class="state-icon error-icon">
                    !
                </div>

                <h3>Gagal memuat history</h3>

                <p>
                    {{ errorMessage }}
                </p>

                <button
                    type="button"
                    class="retry-button"
                    @click="getHistory"
                >
                    Coba Lagi
                </button>
            </div>

            <!-- EMPTY -->
            <div
                v-else-if="histories.length === 0"
                class="state-card"
            >
                <div class="state-icon empty-icon">
                    🕘
                </div>

                <h3>Belum ada history</h3>

                <p>
                    Aktivitas yang kamu lakukan akan
                    muncul di halaman ini.
                </p>

                <button
                    type="button"
                    class="dashboard-button"
                    @click="router.push('/dashboard')"
                >
                    Kembali ke Dashboard
                </button>
            </div>

            <!-- HISTORY LIST -->
            <div
                v-else
                class="history-list"
            >

                <div
                    v-for="(history, index) in histories"
                    :key="history.id || index"
                    class="history-item"
                >

                    <!-- TIMELINE -->
                    <div class="timeline">

                        <div
                            class="timeline-icon"
                            :class="getTypeClass(history.type)"
                        >
                            {{ getTypeIcon(history.type) }}
                        </div>

                        <div
                            v-if="index !== histories.length - 1"
                            class="timeline-line"
                        ></div>

                    </div>

                    <!-- CARD -->
                    <div class="history-card">

                        <div class="history-card-header">

                            <div class="history-title-wrapper">

                                <div
                                    class="type-label"
                                    :class="getTypeClass(history.type)"
                                >
                                    {{ getTypeIcon(history.type) }}

                                    {{ getTypeLabel(history.type) }}
                                </div>

                                <span class="history-date">
                                    {{ formatDate(history.created_at) }}
                                </span>

                            </div>

                            <span class="action-badge">
                                {{ formatAction(history.action) }}
                            </span>

                        </div>

                        <div class="history-description">
                            {{ history.description || 'Tidak ada deskripsi.' }}
                        </div>

                        <!-- DETAIL -->
                        <details
                            v-if="history.data"
                            class="history-detail"
                        >

                            <summary>
                                <span>▸</span>
                                Lihat detail data
                            </summary>

                            <div class="detail-content">
                                <pre>{{ formatJson(history.data) }}</pre>
                            </div>

                        </details>

                    </div>

                </div>

            </div>

        </div>
    </div>
</template>

<style scoped>
.history-page {
    min-height: 100vh;
    background: #f8f9fa;
}

.page-container {
    max-width: 960px;
    padding-top: 32px;
    padding-bottom: 60px;
}

/* =========================
   HEADER
========================= */

.page-header {
    margin-bottom: 28px;
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

    transition:
        color 0.2s ease;
}

.back-button:hover {
    color: #0d6efd;
}

.back-button span {
    font-size: 20px;
    line-height: 1;
}

.page-title-row {
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

    font-size: 27px;
}

.page-title-content {
    flex: 1;
}

.page-badge {
    color: #0d6efd;

    font-size: 12px;
    font-weight: 700;

    margin-bottom: 4px;
}

.page-title-content h1 {
    color: #212529;

    font-size: 30px;
    font-weight: 700;

    margin: 0 0 6px;
}

.page-title-content p {
    color: #6c757d;

    font-size: 14px;

    margin: 0;

    line-height: 1.6;
}

/* =========================
   SUMMARY
========================= */

.summary-card {
    display: flex;
    align-items: center;

    gap: 14px;

    background: #ffffff;

    border: 1px solid #e9ecef;
    border-radius: 14px;

    padding: 16px 18px;

    margin-bottom: 24px;

    box-shadow:
        0 6px 20px rgba(0, 0, 0, 0.035);
}

.summary-icon {
    width: 42px;
    height: 42px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    background: #f1f6ff;
    border-radius: 10px;

    font-size: 18px;
}

.summary-content {
    display: flex;
    flex-direction: column;

    gap: 2px;
}

.summary-content span {
    color: #6c757d;

    font-size: 11px;
    font-weight: 600;
}

.summary-content strong {
    color: #212529;

    font-size: 20px;
    line-height: 1;
}

.summary-status {
    margin-left: auto;

    display: inline-flex;
    align-items: center;
    gap: 7px;

    color: #198754;

    font-size: 12px;
    font-weight: 600;
}

.status-dot {
    width: 7px;
    height: 7px;

    border-radius: 50%;

    background: #198754;
}

/* =========================
   STATE
========================= */

.state-card {
    background: #ffffff;

    border: 1px solid #e9ecef;
    border-radius: 18px;

    padding: 55px 25px;

    text-align: center;

    box-shadow:
        0 8px 25px rgba(0, 0, 0, 0.04);
}

.state-card h3 {
    color: #212529;

    font-size: 17px;
    font-weight: 700;

    margin: 16px 0 6px;
}

.state-card p {
    color: #6c757d;

    font-size: 13px;

    margin: 0 auto;

    max-width: 420px;

    line-height: 1.6;
}

.loading-spinner {
    width: 34px;
    height: 34px;

    margin: 0 auto;

    border: 3px solid #e9ecef;
    border-top-color: #0d6efd;

    border-radius: 50%;

    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.state-icon {
    width: 54px;
    height: 54px;

    margin: 0 auto;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    font-size: 22px;
}

.empty-icon {
    background: #f1f3f5;
}

.error-icon {
    background: #f8d7da;

    color: #842029;

    font-size: 18px;
    font-weight: 700;
}

.retry-button,
.dashboard-button {
    margin-top: 20px;

    min-height: 42px;

    padding: 9px 18px;

    border: 0;
    border-radius: 9px;

    background: #0d6efd;

    color: #ffffff;

    font-size: 13px;
    font-weight: 600;

    cursor: pointer;

    transition:
        background 0.2s ease,
        transform 0.2s ease;
}

.retry-button:hover,
.dashboard-button:hover {
    background: #0b5ed7;

    transform: translateY(-1px);
}

/* =========================
   HISTORY LIST
========================= */

.history-list {
    display: flex;
    flex-direction: column;

    gap: 0;
}

.history-item {
    display: grid;

    grid-template-columns: 54px minmax(0, 1fr);

    gap: 12px;
}

.timeline {
    position: relative;

    display: flex;

    justify-content: center;
}

.timeline-icon {
    position: relative;

    z-index: 2;

    width: 44px;
    height: 44px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 12px;

    font-size: 17px;

    border: 1px solid #e9ecef;
}

.timeline-line {
    position: absolute;

    top: 44px;
    bottom: 0;

    width: 1px;

    background: #dee2e6;
}

/* =========================
   TYPE COLORS
========================= */

.type-star {
    background: #fff8e1;
    color: #a66b00;
}

.type-terbilang {
    background: #f1edff;
    color: #5f3dc4;
}

.type-input {
    background: #e7f5ef;
    color: #087f5b;
}

.type-default {
    background: #f1f3f5;
    color: #495057;
}

/* =========================
   HISTORY CARD
========================= */

.history-card {
    background: #ffffff;

    border: 1px solid #e9ecef;
    border-radius: 16px;

    padding: 20px;

    margin-bottom: 18px;

    box-shadow:
        0 6px 20px rgba(0, 0, 0, 0.035);

    transition:
        box-shadow 0.2s ease,
        transform 0.2s ease;
}

.history-card:hover {
    transform: translateY(-1px);

    box-shadow:
        0 10px 28px rgba(0, 0, 0, 0.06);
}

.history-card-header {
    display: flex;

    align-items: flex-start;
    justify-content: space-between;

    gap: 15px;

    margin-bottom: 13px;
}

.history-title-wrapper {
    display: flex;
    flex-direction: column;

    gap: 6px;
}

.type-label {
    display: inline-flex;
    align-items: center;

    width: fit-content;

    gap: 6px;

    padding: 5px 9px;

    border-radius: 7px;

    font-size: 11px;
    font-weight: 700;
}

.history-date {
    color: #868e96;

    font-size: 11px;
}

.action-badge {
    flex-shrink: 0;

    padding: 5px 9px;

    border-radius: 7px;

    background: #f1f3f5;

    color: #495057;

    font-size: 10px;
    font-weight: 700;

    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.history-description {
    color: #343a40;

    font-size: 14px;
    line-height: 1.6;
}

/* =========================
   DETAIL
========================= */

.history-detail {
    margin-top: 16px;

    border-top: 1px solid #f0f1f3;

    padding-top: 12px;
}

.history-detail summary {
    display: inline-flex;
    align-items: center;

    gap: 6px;

    color: #0d6efd;

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;

    user-select: none;

    list-style: none;
}

.history-detail summary::-webkit-details-marker {
    display: none;
}

.history-detail[open] summary span {
    transform: rotate(90deg);
}

.history-detail summary span {
    transition:
        transform 0.2s ease;
}

.detail-content {
    margin-top: 12px;

    padding: 14px;

    background: #f8f9fa;

    border: 1px solid #e9ecef;

    border-radius: 10px;

    overflow-x: auto;
}

.detail-content pre {
    margin: 0;

    color: #495057;

    font-family:
        Consolas,
        Monaco,
        monospace;

    font-size: 11px;

    line-height: 1.6;

    white-space: pre-wrap;
    word-break: break-word;
}

/* =========================
   RESPONSIVE
========================= */

@media (max-width: 768px) {
    .page-container {
        padding-top: 22px;
        padding-bottom: 40px;
    }

    .page-header {
        margin-bottom: 22px;
    }

    .back-button {
        margin-bottom: 20px;
    }

    .page-title-row {
        gap: 12px;
    }

    .page-icon {
        width: 48px;
        height: 48px;

        border-radius: 12px;

        font-size: 23px;
    }

    .page-title-content h1 {
        font-size: 25px;
    }

    .page-title-content p {
        font-size: 13px;
    }

    .summary-card {
        padding: 14px 15px;
    }

    .history-item {
        grid-template-columns: 40px minmax(0, 1fr);

        gap: 8px;
    }

    .timeline-icon {
        width: 36px;
        height: 36px;

        border-radius: 10px;

        font-size: 14px;
    }

    .timeline-line {
        top: 36px;
    }

    .history-card {
        padding: 16px;

        border-radius: 14px;

        margin-bottom: 14px;
    }

    .history-card-header {
        flex-direction: column;

        gap: 9px;
    }

    .action-badge {
        align-self: flex-start;
    }
}

@media (max-width: 576px) {
    .summary-status {
        display: none;
    }

    .state-card {
        padding: 45px 20px;
    }

    .history-description {
        font-size: 13px;
    }

    .history-date {
        font-size: 10px;
    }

    .detail-content {
        padding: 11px;
    }

    .detail-content pre {
        font-size: 10px;
    }
}
</style>