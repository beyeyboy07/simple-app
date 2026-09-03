<script setup>
import {
    reactive,
    ref,
    computed,
    onMounted,
} from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import { regions } from '../data/regions'

const router = useRouter()

const loading = ref(false)
const listLoading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const inputDataList = ref([])

const editingId = ref(null)

/* =========================
   FORM
========================= */

const form = reactive({
    nama: '',
    email: '',
    no_hp: '',
    alamat: '',
    provinsi: '',
    kota: '',
    kecamatan: '',
    kelurahan: '',
    level: '',
    range_gaji: '',
    gaji: null,
})

/* =========================
   REGION
========================= */

const selectedProvince = computed(() => {
    return regions.find(
        item => item.province === form.provinsi
    )
})

const cities = computed(() => {
    return selectedProvince.value?.cities || []
})

const selectedCity = computed(() => {
    return cities.value.find(
        item => item.name === form.kota
    )
})

const districts = computed(() => {
    return selectedCity.value?.districts || []
})

const selectedDistrict = computed(() => {
    return districts.value.find(
        item => item.name === form.kecamatan
    )
})

const villages = computed(() => {
    return selectedDistrict.value?.villages || []
})

/* =========================
   INPUT HANDLER
========================= */

const preventNonName = (event) => {
    const allowedKeys = [
        'Backspace',
        'Delete',
        'ArrowLeft',
        'ArrowRight',
        'ArrowUp',
        'ArrowDown',
        'Tab',
        'Home',
        'End',
        ' ',
    ]

    if (event.ctrlKey || event.metaKey) {
        return
    }

    if (/^[a-zA-ZÀ-ÿ]$/.test(event.key)) {
        return
    }

    if (allowedKeys.includes(event.key)) {
        return
    }

    event.preventDefault()
}

const handleNamaInput = (event) => {
    let value = event.target.value

    value = value.replace(
        /[^a-zA-ZÀ-ÿ\s]/g,
        ''
    )

    value = value.replace(
        /\s{2,}/g,
        ' '
    )

    form.nama = value
}

const handlePhoneInput = (event) => {
    let value = event.target.value

    value = value.replace(/\D/g, '')
    value = value.replace(/^0+/, '')
    value = value.slice(0, 13)

    form.no_hp = value
}

const formatRupiah = (value) => {
    if (
        value === null ||
        value === undefined ||
        value === ''
    ) {
        return ''
    }

    return new Intl.NumberFormat(
        'id-ID'
    ).format(value)
}

const handleSalaryInput = (event) => {
    let value = event.target.value

    value = value.replace(/\D/g, '')
    value = value.replace(/^0+(?=\d)/, '')

    form.gaji = value
        ? Number(value)
        : null
}

const preventNonNumeric = (event) => {
    const allowedKeys = [
        'Backspace',
        'Delete',
        'ArrowLeft',
        'ArrowRight',
        'ArrowUp',
        'ArrowDown',
        'Tab',
        'Home',
        'End',
    ]

    if (
        allowedKeys.includes(event.key) ||
        /^[0-9]$/.test(event.key) ||
        event.ctrlKey ||
        event.metaKey
    ) {
        return
    }

    event.preventDefault()
}

/* =========================
   REGION HANDLER
========================= */

const handleProvinceChange = () => {
    form.kota = ''
    form.kecamatan = ''
    form.kelurahan = ''
}

const handleCityChange = () => {
    form.kecamatan = ''
    form.kelurahan = ''
}

const handleDistrictChange = () => {
    form.kelurahan = ''
}

/* =========================
   VALIDATION
========================= */

const validateForm = () => {
    const nama = form.nama.trim()

    if (!nama) {
        return 'Nama wajib diisi.'
    }

    if (!/^[a-zA-ZÀ-ÿ\s]+$/.test(nama)) {
        return 'Nama hanya boleh berisi huruf dan spasi.'
    }

    if (!form.email) {
        return 'Email wajib diisi.'
    }

    if (!form.no_hp) {
        return 'Nomor HP wajib diisi.'
    }

    if (!/^[1-9][0-9]{8,12}$/.test(form.no_hp)) {
        return 'Nomor HP harus berupa angka dan tidak boleh diawali 0.'
    }

    if (!form.alamat.trim()) {
        return 'Alamat wajib diisi.'
    }

    if (!form.provinsi) {
        return 'Provinsi wajib dipilih.'
    }

    if (!form.kota) {
        return 'Kota / Kabupaten wajib dipilih.'
    }

    if (!form.kecamatan) {
        return 'Kecamatan wajib dipilih.'
    }

    if (!form.kelurahan) {
        return 'Kelurahan wajib dipilih.'
    }

    if (!form.level) {
        return 'Level wajib dipilih.'
    }

    if (!form.range_gaji) {
        return 'Range gaji wajib dipilih.'
    }

    if (
        form.gaji === null ||
        form.gaji === undefined ||
        form.gaji === ''
    ) {
        return 'Gaji wajib diisi.'
    }

    if (
        !Number.isInteger(form.gaji) ||
        form.gaji < 0
    ) {
        return 'Gaji harus berupa angka yang valid.'
    }

    return null
}

/* =========================
   RESET FORM
========================= */

const resetForm = () => {
    form.nama = ''
    form.email = ''
    form.no_hp = ''
    form.alamat = ''
    form.provinsi = ''
    form.kota = ''
    form.kecamatan = ''
    form.kelurahan = ''
    form.level = ''
    form.range_gaji = ''
    form.gaji = null

    editingId.value = null
}

/* =========================
   GET DATA
========================= */

const fetchInputData = async () => {
    listLoading.value = true

    try {
        const response = await api.get(
            '/input-data'
        )

        inputDataList.value =
            response.data.data || []
    } catch (error) {
        console.error(
            'Gagal mengambil data:',
            error
        )

        errorMessage.value =
            error.response?.data?.message ||
            'Gagal mengambil data user.'
    } finally {
        listLoading.value = false
    }
}

/* =========================
   EDIT
========================= */

const editData = (item) => {
    editingId.value = item.id

    form.nama = item.nama || ''
    form.email = item.email || ''

    /*
     * Database:
     * 628123456789
     *
     * Form:
     * 8123456789
     */
    form.no_hp = item.no_hp
        ? item.no_hp.replace(/^62/, '')
        : ''

    form.alamat = item.alamat || ''
    form.provinsi = item.provinsi || ''
    form.kota = item.kota || ''
    form.kecamatan = item.kecamatan || ''
    form.kelurahan = item.kelurahan || ''
    form.level = item.level || ''
    form.range_gaji = item.range_gaji || ''
    form.gaji = item.gaji
        ? Number(item.gaji)
        : null

    successMessage.value = ''
    errorMessage.value = ''

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    })
}

/* =========================
   DELETE
========================= */

const deleteData = async (id) => {
    const confirmed = window.confirm(
        'Apakah kamu yakin ingin menghapus data user ini?'
    )

    if (!confirmed) {
        return
    }

    try {
        await api.delete(
            `/input-data/${id}`
        )

        successMessage.value =
            'Data user berhasil dihapus.'

        errorMessage.value = ''

        if (editingId.value === id) {
            resetForm()
        }

        await fetchInputData()

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        })
    } catch (error) {
        console.error(error)

        errorMessage.value =
            error.response?.data?.message ||
            'Gagal menghapus data user.'

        successMessage.value = ''

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        })
    }
}

/* =========================
   SUBMIT
========================= */

const submitForm = async () => {
    successMessage.value = ''
    errorMessage.value = ''

    const validationError = validateForm()

    if (validationError) {
        errorMessage.value =
            validationError

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        })

        return
    }

    loading.value = true

    try {
        const payload = {
            ...form,
            nama: form.nama.trim(),
            alamat: form.alamat.trim(),
            no_hp: `62${form.no_hp}`,
        }

        let response

        /*
         * CREATE
         */
        if (!editingId.value) {
            response = await api.post(
                '/input-data',
                payload
            )

            successMessage.value =
                response.data.message ||
                'Data berhasil disimpan.'
        }

        /*
         * UPDATE
         */
        else {
            response = await api.put(
                `/input-data/${editingId.value}`,
                payload
            )

            successMessage.value =
                response.data.message ||
                'Data berhasil diperbarui.'
        }

        resetForm()

        await fetchInputData()

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        })
    } catch (error) {
        console.error(error)

        if (
            error.response?.status === 422
        ) {
            const errors =
                error.response.data.errors

            if (errors) {
                errorMessage.value =
                    Object.values(errors)
                        .flat()
                        .join(' ')
            } else {
                errorMessage.value =
                    error.response.data.message ||
                    'Data yang dimasukkan tidak valid.'
            }
        } else {
            errorMessage.value =
                error.response?.data?.message ||
                'Terjadi kesalahan saat menyimpan data.'
        }

        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        })
    } finally {
        loading.value = false
    }
}

/* =========================
   CANCEL EDIT
========================= */

const cancelEdit = () => {
    resetForm()

    successMessage.value = ''
    errorMessage.value = ''
}

/* =========================
   INITIAL LOAD
========================= */

onMounted(() => {
    fetchInputData()
})
</script>

<template>
    <div class="input-data-page">

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

                <div class="page-title-wrapper">

                    <div class="page-icon">
                        📝
                    </div>

                    <div>

                        <div class="page-badge">
                            Form Data
                        </div>

                        <h1>
                            Input Data
                        </h1>

                        <p>
                            Lengkapi data berikut dengan
                            informasi yang sesuai.
                        </p>

                    </div>

                </div>

            </div>


            <!-- SUCCESS -->

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


            <!-- ERROR -->

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
                        Data tidak dapat diproses
                    </div>

                    <div>
                        {{ errorMessage }}
                    </div>

                </div>

            </div>


            <!-- FORM -->

            <form
                class="input-form"
                @submit.prevent="submitForm"
            >

                <!-- DATA PRIBADI -->

                <div class="form-card">

                    <div class="section-header">

                        <div class="section-icon">
                            👤
                        </div>

                        <div>

                            <h2>
                                Data Pribadi
                            </h2>

                            <p>
                                Masukkan informasi pribadi Anda.
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">

                        <!-- NAMA -->

                        <div class="form-group">

                            <label for="nama">
                                Nama
                                <span>*</span>
                            </label>

                            <input
                                id="nama"
                                :value="form.nama"
                                type="text"
                                placeholder="Masukkan nama lengkap"
                                autocomplete="name"
                                required
                                :disabled="loading"
                                @keydown="preventNonName"
                                @input="handleNamaInput"
                            />

                            <small>
                                Nama hanya dapat berisi huruf
                                dan spasi.
                            </small>

                        </div>


                        <!-- EMAIL -->

                        <div class="form-group">

                            <label for="email">
                                Email
                                <span>*</span>
                            </label>

                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                placeholder="contoh@email.com"
                                autocomplete="email"
                                required
                                :disabled="loading"
                            />

                        </div>


                        <!-- NO HP -->

                        <div class="form-group">

                            <label for="no_hp">
                                No HP
                                <span>*</span>
                            </label>

                            <div class="phone-input">

                                <span class="phone-prefix">
                                    +62
                                </span>

                                <input
                                    id="no_hp"
                                    :value="form.no_hp"
                                    type="tel"
                                    inputmode="numeric"
                                    pattern="[1-9][0-9]{8,12}"
                                    placeholder="81234567890"
                                    autocomplete="tel"
                                    required
                                    :disabled="loading"
                                    @keydown="preventNonNumeric"
                                    @input="handlePhoneInput"
                                />

                            </div>

                            <small>
                                Masukkan nomor tanpa angka
                                0 di depan.
                            </small>

                        </div>

                    </div>

                </div>


                <!-- ALAMAT -->

                <div class="form-card">

                    <div class="section-header">

                        <div class="section-icon">
                            📍
                        </div>

                        <div>

                            <h2>
                                Alamat
                            </h2>

                            <p>
                                Lengkapi informasi alamat
                                tempat tinggal.
                            </p>

                        </div>

                    </div>


                    <div class="form-group address-group">

                        <label for="alamat">
                            Alamat Lengkap
                            <span>*</span>
                        </label>

                        <textarea
                            id="alamat"
                            v-model="form.alamat"
                            placeholder="Masukkan alamat lengkap"
                            rows="3"
                            required
                            :disabled="loading"
                        ></textarea>

                    </div>


                    <div class="form-grid">

                        <!-- PROVINSI -->

                        <div class="form-group">

                            <label for="provinsi">
                                Provinsi
                                <span>*</span>
                            </label>

                            <select
                                id="provinsi"
                                v-model="form.provinsi"
                                required
                                :disabled="loading"
                                @change="handleProvinceChange"
                            >

                                <option value="">
                                    Pilih Provinsi
                                </option>

                                <option
                                    v-for="region in regions"
                                    :key="region.province"
                                    :value="region.province"
                                >
                                    {{ region.province }}
                                </option>

                            </select>

                        </div>


                        <!-- KOTA -->

                        <div class="form-group">

                            <label for="kota">
                                Kota / Kabupaten
                                <span>*</span>
                            </label>

                            <select
                                id="kota"
                                v-model="form.kota"
                                required
                                :disabled="
                                    !form.provinsi ||
                                    loading
                                "
                                @change="handleCityChange"
                            >

                                <option value="">
                                    Pilih Kota / Kabupaten
                                </option>

                                <option
                                    v-for="city in cities"
                                    :key="city.name"
                                    :value="city.name"
                                >
                                    {{ city.name }}
                                </option>

                            </select>

                        </div>


                        <!-- KECAMATAN -->

                        <div class="form-group">

                            <label for="kecamatan">
                                Kecamatan
                                <span>*</span>
                            </label>

                            <select
                                id="kecamatan"
                                v-model="form.kecamatan"
                                required
                                :disabled="
                                    !form.kota ||
                                    loading
                                "
                                @change="handleDistrictChange"
                            >

                                <option value="">
                                    Pilih Kecamatan
                                </option>

                                <option
                                    v-for="district in districts"
                                    :key="district.name"
                                    :value="district.name"
                                >
                                    {{ district.name }}
                                </option>

                            </select>

                        </div>


                        <!-- KELURAHAN -->

                        <div class="form-group">

                            <label for="kelurahan">
                                Kelurahan
                                <span>*</span>
                            </label>

                            <select
                                id="kelurahan"
                                v-model="form.kelurahan"
                                required
                                :disabled="
                                    !form.kecamatan ||
                                    loading
                                "
                            >

                                <option value="">
                                    Pilih Kelurahan
                                </option>

                                <option
                                    v-for="village in villages"
                                    :key="village"
                                    :value="village"
                                >
                                    {{ village }}
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <!-- DATA PEKERJAAN -->

                <div class="form-card">

                    <div class="section-header">

                        <div class="section-icon">
                            💼
                        </div>

                        <div>

                            <h2>
                                Data Pekerjaan
                            </h2>

                            <p>
                                Masukkan informasi pekerjaan
                                dan penghasilan.
                            </p>

                        </div>

                    </div>


                    <div class="form-grid">

                        <!-- LEVEL -->

                        <div class="form-group">

                            <label for="level">
                                Level
                                <span>*</span>
                            </label>

                            <select
                                id="level"
                                v-model="form.level"
                                required
                                :disabled="loading"
                            >

                                <option value="">
                                    Pilih Level
                                </option>

                                <option value="Staff">
                                    Staff
                                </option>

                                <option value="Supervisor">
                                    Supervisor
                                </option>

                                <option value="Manager">
                                    Manager
                                </option>

                            </select>

                        </div>


                        <!-- RANGE GAJI -->

                        <div class="form-group">

                            <label for="range_gaji">
                                Range Gaji
                                <span>*</span>
                            </label>

                            <select
                                id="range_gaji"
                                v-model="form.range_gaji"
                                required
                                :disabled="loading"
                            >

                                <option value="">
                                    Pilih Range Gaji
                                </option>

                                <option value="1 - 3 juta">
                                    1 - 3 juta
                                </option>

                                <option value="3 - 5 juta">
                                    3 - 5 juta
                                </option>

                                <option value="5 - 10 juta">
                                    5 - 10 juta
                                </option>

                                <option value="10 - 15 juta">
                                    10 - 15 juta
                                </option>

                                <option value="15 - 20 juta">
                                    15 - 20 juta
                                </option>

                            </select>

                        </div>


                        <!-- GAJI -->

                        <div class="form-group full-width">

                            <label for="gaji">
                                Gaji
                                <span>*</span>
                            </label>

                            <div class="salary-input">

                                <span class="salary-prefix">
                                    Rp
                                </span>

                                <input
                                    id="gaji"
                                    :value="
                                        formatRupiah(form.gaji)
                                    "
                                    type="text"
                                    inputmode="numeric"
                                    placeholder="Masukkan nominal gaji"
                                    required
                                    :disabled="loading"
                                    @keydown="preventNonNumeric"
                                    @input="handleSalaryInput"
                                />

                            </div>

                            <small>
                                Masukkan nominal gaji sesuai
                                dengan range yang dipilih.
                            </small>

                        </div>

                    </div>

                </div>


                <!-- SUBMIT -->

                <div class="submit-section">

                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="loading"
                    >

                        <span
                            v-if="loading"
                            class="loading-content"
                        >

                            <span
                                class="loading-spinner"
                            ></span>

                            {{
                                editingId
                                    ? 'Memperbarui...'
                                    : 'Menyimpan...'
                            }}

                        </span>

                        <span v-else>
                            {{
                                editingId
                                    ? '✓ Update Data'
                                    : '✓ Simpan Data'
                            }}
                        </span>

                    </button>


                    <button
                        v-if="editingId"
                        type="button"
                        class="cancel-button"
                        @click="cancelEdit"
                        :disabled="loading"
                    >
                        Batal
                    </button>

                </div>

            </form>


            <!-- LIST DATA USER -->

            <div class="form-card data-list-card">

                <div class="section-header">

                    <div class="section-icon">
                        👥
                    </div>

                    <div>

                        <h2>
                            Data User
                        </h2>

                        <p>
                            Daftar data yang sudah berhasil
                            diinput.
                        </p>

                    </div>

                </div>


                <!-- LIST LOADING -->

                <div
                    v-if="listLoading"
                    class="list-loading"
                >

                    <span class="list-spinner"></span>

                    Memuat data user...

                </div>


                <!-- EMPTY -->

                <div
                    v-else-if="
                        inputDataList.length === 0
                    "
                    class="empty-data"
                >

                    <div class="empty-icon">
                        📋
                    </div>

                    <div>

                        <strong>
                            Belum ada data
                        </strong>

                        <p>
                            Belum ada user yang diinput.
                        </p>

                    </div>

                </div>


                <!-- TABLE -->

                <div
                    v-else
                    class="table-wrapper"
                >

                    <table class="data-table">

                        <thead>

                            <tr>

                                <th>
                                    No
                                </th>

                                <th>
                                    Nama
                                </th>

                                <th>
                                    Email
                                </th>

                                <th>
                                    No HP
                                </th>

                                <th>
                                    Provinsi
                                </th>

                                <th>
                                    Kota
                                </th>

                                <th>
                                    Level
                                </th>

                                <th>
                                    Range Gaji
                                </th>

                                <th>
                                    Gaji
                                </th>

                                <th>
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <tr
                                v-for="(
                                    item,
                                    index
                                ) in inputDataList"
                                :key="item.id"
                            >

                                <td>
                                    {{ index + 1 }}
                                </td>

                                <td>
                                    <strong>
                                        {{ item.nama }}
                                    </strong>
                                </td>

                                <td>
                                    {{ item.email }}
                                </td>

                                <td>
                                    +{{ item.no_hp }}
                                </td>

                                <td>
                                    {{ item.provinsi }}
                                </td>

                                <td>
                                    {{ item.kota }}
                                </td>

                                <td>

                                    <span
                                        class="level-badge"
                                        :class="{
                                            staff:
                                                item.level === 'Staff',
                                            supervisor:
                                                item.level === 'Supervisor',
                                            manager:
                                                item.level === 'Manager',
                                        }"
                                    >
                                        {{ item.level }}
                                    </span>

                                </td>

                                <td>
                                    {{ item.range_gaji }}
                                </td>

                                <td>
                                    Rp
                                    {{
                                        formatRupiah(
                                            item.gaji
                                        )
                                    }}
                                </td>

                                <td>

                                    <div class="action-buttons">

                                        <button
                                            type="button"
                                            class="edit-button"
                                            @click="
                                                editData(item)
                                            "
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="delete-button"
                                            @click="
                                                deleteData(
                                                    item.id
                                                )
                                            "
                                        >
                                            Hapus
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
</template>

<style scoped>

.input-data-page {
    min-height: 100vh;
    background: #f8f9fa;
}

.page-container {
    max-width: 960px;
    padding-top: 32px;
    padding-bottom: 60px;
}

.page-header {
    margin-bottom: 30px;
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
    font-size: 27px;
}

.page-badge {
    color: #0d6efd;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 4px;
}

.page-title-wrapper h1 {
    color: #212529;
    font-size: 30px;
    font-weight: 700;
    margin: 0 0 6px;
}

.page-title-wrapper p {
    color: #6c757d;
    font-size: 14px;
    margin: 0;
    line-height: 1.6;
}

.custom-alert {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    border-radius: 10px;
    padding: 13px 15px;
    font-size: 13px;
    margin-bottom: 20px;
}

.alert-icon {
    width: 23px;
    height: 23px;
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

.input-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-card {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 18px;
    padding: 28px;
    box-shadow:
        0 8px 25px rgba(0, 0, 0, 0.04);
}

.section-header {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    margin-bottom: 24px;
    padding-bottom: 18px;
    border-bottom: 1px solid #f0f1f3;
}

.section-icon {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f6ff;
    border-radius: 10px;
    font-size: 18px;
}

.section-header h2 {
    color: #212529;
    font-size: 17px;
    font-weight: 700;
    margin: 0 0 3px;
}

.section-header p {
    color: #6c757d;
    font-size: 12px;
    margin: 0;
}

.address-group {
    margin-bottom: 20px;
}

.form-grid {
    display: grid;
    grid-template-columns:
        repeat(2, minmax(0, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    color: #343a40;
    font-size: 13px;
    font-weight: 600;
}

.form-group label span {
    color: #dc3545;
}

.form-group input,
.form-group textarea,
.form-group select {
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #ced4da;
    border-radius: 10px;
    background: #ffffff;
    color: #212529;
    font-family: inherit;
    font-size: 14px;
    padding: 11px 13px;
    outline: none;
    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;
}

.form-group input,
.form-group select {
    min-height: 46px;
}

.form-group textarea {
    resize: vertical;
    min-height: 90px;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #86b7fe;
    box-shadow:
        0 0 0 3px rgba(13, 110, 253, 0.10);
}

.form-group input:disabled,
.form-group textarea:disabled,
.form-group select:disabled {
    background: #f8f9fa;
    cursor: not-allowed;
}

.form-group select:disabled {
    color: #adb5bd;
}

.form-group small {
    color: #6c757d;
    font-size: 11px;
    line-height: 1.4;
}

.phone-input,
.salary-input {
    display: flex;
    width: 100%;
}

.phone-prefix,
.salary-prefix {
    display: flex;
    align-items: center;
    padding: 0 13px;
    border: 1px solid #ced4da;
    border-right: 0;
    background: #f8f9fa;
    color: #495057;
    font-size: 14px;
    font-weight: 600;
    border-radius: 10px 0 0 10px;
}

.phone-input input,
.salary-input input {
    border-radius: 0 10px 10px 0;
}

.submit-section {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.submit-button {
    min-width: 180px;
    min-height: 48px;
    padding: 11px 20px;
    border: 0;
    border-radius: 10px;
    background: #0d6efd;
    color: #ffffff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease,
        background 0.2s ease;
}

.submit-button:hover:not(:disabled) {
    background: #0b5ed7;
    transform: translateY(-1px);
    box-shadow:
        0 6px 15px rgba(13, 110, 253, 0.20);
}

.submit-button:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.cancel-button {
    min-height: 48px;
    padding: 11px 20px;
    border: 1px solid #ced4da;
    border-radius: 10px;
    background: #ffffff;
    color: #495057;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.cancel-button:hover {
    background: #f8f9fa;
}

.loading-content {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.loading-spinner,
.list-spinner {
    width: 15px;
    height: 15px;
    border: 2px solid rgba(13, 110, 253, 0.25);
    border-top-color: #0d6efd;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

.loading-content .loading-spinner {
    border-color: rgba(255, 255, 255, 0.4);
    border-top-color: #ffffff;
}


/* =========================
   DATA LIST
========================= */

.data-list-card {
    margin-top: 20px;
}

.list-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 35px 20px;
    color: #6c757d;
    font-size: 13px;
}

.empty-data {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 35px 20px;
    color: #6c757d;
    text-align: center;
}

.empty-icon {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f6ff;
    border-radius: 10px;
    font-size: 20px;
}

.empty-data strong {
    display: block;
    color: #343a40;
    font-size: 14px;
    margin-bottom: 3px;
}

.empty-data p {
    margin: 0;
    font-size: 12px;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.data-table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
    font-size: 13px;
}

.data-table th {
    background: #f8f9fa;
    color: #495057;
    font-weight: 700;
    text-align: left;
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
    white-space: nowrap;
}

.data-table td {
    color: #495057;
    padding: 13px 12px;
    border-bottom: 1px solid #f0f1f3;
    white-space: nowrap;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.data-table td strong {
    color: #212529;
}


/* =========================
   LEVEL BADGE
========================= */

.level-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.level-badge.staff {
    background: #e7f1ff;
    color: #0d6efd;
}

.level-badge.supervisor {
    background: #fff3cd;
    color: #856404;
}

.level-badge.manager {
    background: #d1e7dd;
    color: #0f5132;
}


/* =========================
   ACTION BUTTON
========================= */

.action-buttons {
    display: flex;
    gap: 6px;
}

.edit-button,
.delete-button {
    border: 0;
    border-radius: 7px;
    padding: 6px 10px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

.edit-button {
    background: #e7f1ff;
    color: #0d6efd;
}

.edit-button:hover {
    background: #cfe2ff;
}

.delete-button {
    background: #f8d7da;
    color: #842029;
}

.delete-button:hover {
    background: #f1bfc4;
}


@keyframes spin {

    to {
        transform: rotate(360deg);
    }

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

    .form-grid {
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .full-width {
        grid-column: auto;
    }

    .form-card {
        padding: 22px 20px;
        border-radius: 16px;
    }

    .section-header {
        margin-bottom: 20px;
    }

    .submit-section {
        display: block;
    }

    .submit-button,
    .cancel-button {
        width: 100%;
    }

    .cancel-button {
        margin-top: 8px;
    }

}


@media (max-width: 576px) {

    .form-card {
        padding: 20px 16px;
    }

    .section-icon {
        width: 36px;
        height: 36px;
        font-size: 16px;
    }

    .section-header h2 {
        font-size: 16px;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        font-size: 14px;
    }

}

</style>
