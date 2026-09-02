<template>
  <div class="input-data-page">
    <div class="page-header">
      <h1>Input Data</h1>
      <p>Silakan lengkapi data berikut.</p>
    </div>

    <form class="input-form" @submit.prevent="submitForm">
      <div class="form-group">
        <label>Nama</label>
        <input
          v-model="form.nama"
          type="text"
          placeholder="Masukkan nama"
          required
        />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input
          v-model="form.email"
          type="email"
          placeholder="Masukkan email"
          required
        />
      </div>

      <div class="form-group">
        <label>No HP</label>
        <input
          v-model="form.no_hp"
          type="text"
          placeholder="Masukkan nomor HP"
          required
        />
      </div>

      <div class="form-group">
        <label>Alamat</label>
        <textarea
          v-model="form.alamat"
          placeholder="Masukkan alamat"
          rows="3"
          required
        ></textarea>
      </div>

        <div class="form-group">
        <label>Provinsi</label>
        <select
            v-model="form.provinsi"
            @change="handleProvinceChange"
            required
        >
            <option value="">Pilih Provinsi</option>

            <option
            v-for="region in regions"
            :key="region.province"
            :value="region.province"
            >
            {{ region.province }}
            </option>
        </select>
        </div>

        <div class="form-group">
        <label>Kota / Kabupaten</label>
        <select
            v-model="form.kota"
            @change="handleCityChange"
            :disabled="!form.provinsi"
            required
        >
            <option value="">Pilih Kota / Kabupaten</option>

            <option
            v-for="city in cities"
            :key="city.name"
            :value="city.name"
            >
            {{ city.name }}
            </option>
        </select>
        </div>

        <div class="form-group">
        <label>Kecamatan</label>
        <select
            v-model="form.kecamatan"
            @change="handleDistrictChange"
            :disabled="!form.kota"
            required
        >
            <option value="">Pilih Kecamatan</option>

            <option
            v-for="district in districts"
            :key="district.name"
            :value="district.name"
            >
            {{ district.name }}
            </option>
        </select>
        </div>

        <div class="form-group">
        <label>Kelurahan</label>
        <select
            v-model="form.kelurahan"
            :disabled="!form.kecamatan"
            required
        >
            <option value="">Pilih Kelurahan</option>

            <option
            v-for="village in villages"
            :key="village"
            :value="village"
            >
            {{ village }}
            </option>
        </select>
        </div>

      <div class="form-group">
        <label>Level</label>
        <select v-model="form.level" required>
          <option value="">Pilih Level</option>
          <option value="Staff">Staff</option>
          <option value="Supervisor">Supervisor</option>
          <option value="Manager">Manager</option>
        </select>
      </div>

      <div class="form-group">
        <label>Range Gaji</label>
        <select v-model="form.range_gaji" required>
          <option value="">Pilih Range Gaji</option>
          <option value="1 - 3 juta">1 - 3 juta</option>
          <option value="3 - 5 juta">3 - 5 juta</option>
          <option value="5 - 10 juta">5 - 10 juta</option>
          <option value="10 - 15 juta">10 - 15 juta</option>
          <option value="15 - 20 juta">15 - 20 juta</option>
        </select>
      </div>

      <div class="form-group">
        <label>Gaji</label>
        <input
          v-model.number="form.gaji"
          type="number"
          min="1"
          placeholder="Masukkan gaji"
          required
        />
      </div>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Menyimpan...' : 'Simpan Data' }}
      </button>

      <p v-if="successMessage" class="success-message">
        {{ successMessage }}
      </p>

      <p v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </p>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'
import api from '../services/api'
import { regions } from '../data/regions'

const loading = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

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

const submitForm = async () => {
  successMessage.value = ''
  errorMessage.value = ''
  loading.value = true

  try {
    const response = await api.post('/input-data', form)

    successMessage.value = response.data.message

    // Reset form setelah berhasil disimpan
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
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors

      if (errors) {
        errorMessage.value = Object.values(errors)
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
  } finally {
    loading.value = false
  }
}
</script>



<style scoped>
.input-data-page {
  max-width: 800px;
  margin: 0 auto;
  padding: 24px;
}

.page-header {
  margin-bottom: 24px;
}

.page-header h1 {
  margin-bottom: 8px;
}

.page-header p {
  margin: 0;
  color: #666;
}

.input-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-weight: 600;
}

.form-group input,
.form-group textarea,
.form-group select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  box-sizing: border-box;
}

.form-group textarea {
  resize: vertical;
}

button {
  padding: 12px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 15px;
  font-weight: 600;
}

button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.success-message {
  color: green;
  font-weight: 500;
}

.error-message {
  color: red;
  font-weight: 500;
}

@media (max-width: 600px) {
  .input-data-page {
    padding: 16px;
  }
}
</style>