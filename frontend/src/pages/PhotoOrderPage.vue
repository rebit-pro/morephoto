<template>
  <div>
    <v-row class="mb-4" align="center">
      <v-col cols="12" md="8">
        <h1 v-if="collectionName">{{ collectionName }}</h1>
        <h1 v-else>Выбор фотографий</h1>
        <div class="small-muted">Код коллекции: {{ code }}</div>
      </v-col>
      <v-col cols="12" md="4" class="d-flex justify-end">
        <v-btn text @click="reload" :disabled="loading">Обновить</v-btn>
      </v-col>
    </v-row>

    <v-row>
      <v-col cols="12" md="8" class="left-col">
        <v-row class="photo-grid">
          <v-col
              v-for="(p, idx) in photos"
              :key="p.id"
              cols="12" sm="6" md="4"
          >
            <PhotoTile :photo="p" @update="onUpdatePhoto" />
          </v-col>
        </v-row>
        <div v-if="!loading && photos.length===0" class="pa-6 grey--text">Фотографий нет или коллекция не найдена.</div>
        <div v-if="loading" class="pa-6">Загрузка...</div>
      </v-col>

      <v-col cols="12" md="4" class="right-col">
        <OrderSummary
            :code="code"
            :items="photos"
            @submit-success="onSubmitSuccess"
            @submit-error="onSubmitError"
        />
      </v-col>
    </v-row>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" top right>
      {{ snackbar.text }}
      <template #actions>
        <v-btn text @click="snackbar.show = false">OK</v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import PhotoTile from '../components/PhotoTile.vue'
import OrderSummary from '../components/OrderSummary.vue'
import photoService from '../services/photoService'

export default {
  name: 'PhotoOrderPage',
  components: { PhotoTile, OrderSummary },
  props: ['code'],
  setup(props, { attrs, emit, expose }) {
    const routeCode = props.code || (attrs && attrs.route && attrs.route.params && attrs.route.params.code) || null
    // In case props.code wasn't passed, try to read from $route (router passes props=true above)
    // But since we used props: true in router, props.code should be present.
    const code = ref(routeCode || '')
    const collectionName = ref('')
    const photos = ref([])
    const loading = ref(false)

    const snackbar = ref({ show: false, text: '', color: '' })

    const fetchCollection = async () => {
      if (!code.value) {
        collectionName.value = ''
        photos.value = []
        return
      }
      loading.value = true
      try {
        const resp = await photoService.getCollectionByCode(code.value)
        // ожидаем структуру { data: { code, name, photos: [{src: "..."}] } } или { code, name, photos }
        const payload = resp?.data ?? resp
        collectionName.value = payload?.name || payload?.data?.name || ''
        const rawPhotos = payload?.photos ?? payload?.data?.photos ?? []
        // Маппим в локальную структуру
        photos.value = rawPhotos.map((p, i) => {
          const src = (p.src && (p.src.startsWith('http') ? p.src : ('https://api.morephoto.loc' + p.src))) || p.url || ''
          return {
            id: p.id ?? i,
            src,
            selected: false,
            sizes: { '10x15': 0, '15x21': 0, '20x30': 0 }
          }
        })
      } catch (err) {
        console.error(err)
        collectionName.value = ''
        photos.value = []
        showSnackbar('Ошибка при загрузке коллекции: ' + (err.message || err), 'error')
      } finally {
        loading.value = false
      }
    }

    onMounted(fetchCollection)

    const reload = () => fetchCollection()

    const onUpdatePhoto = (updated) => {
      // updated must contain id to match
      const idx = photos.value.findIndex(x => x.id === updated.id)
      if (idx !== -1) photos.value.splice(idx, 1, updated)
    }

    const onSubmitSuccess = (msg) => {
      showSnackbar(msg || 'Заявка успешно отправлена', 'success')
      // Сброс selected и qty
      photos.value = photos.value.map(p => ({ ...p, selected: false, sizes: { '10x15':0,'15x21':0,'20x30':0 } }))
    }
    const onSubmitError = (msg) => showSnackbar(msg || 'Ошибка отправки', 'error')

    function showSnackbar(text, type = 'info') {
      snackbar.value = { show: true, text, color: type === 'success' ? 'green' : type === 'error' ? 'red' : 'primary' }
    }

    return { code, collectionName, photos, loading, reload, onUpdatePhoto, snackbar, onSubmitSuccess, onSubmitError }
  }
}
</script>

<style scoped>
h1 { margin: 0 0 6px 0 }
</style>
