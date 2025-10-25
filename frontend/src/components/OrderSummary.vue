<template>
  <v-card class="pa-4">
    <h3>Итог заказа</h3>
    <div v-if="selectedItems.length===0" class="grey--text pa-6">Пока нет выбранных фотографий</div>

    <v-list v-else>
      <v-list-item v-for="(it, idx) in selectedItems" :key="it.id">
        <v-list-item-avatar>
          <v-img :src="it.src" />
        </v-list-item-avatar>
        <v-list-item-content>
          <div class="text-subtitle-2">Фото #{{ it.id }}</div>
          <div class="text-caption" v-for="(qty, size) in it.sizes" :key="size" v-if="qty > 0">
            {{ size }} — {{ qty }} шт.
          </div>
        </v-list-item-content>
      </v-list-item>
    </v-list>

    <v-textarea v-model="note" label="Комментарий (опционально)" rows="2" class="mt-4" />

    <div class="d-flex align-center mt-4">
      <v-btn :disabled="selectedItems.length===0 || submitting" elevation="2" @click="onSubmit">Отправить заявку</v-btn>
      <v-spacer />
      <div v-if="submitting">Отправка...</div>
    </div>
  </v-card>
</template>

<script>
import { computed, ref } from 'vue'
import photoService from '../services/photoService'

export default {
  name: 'OrderSummary',
  props: {
    items: { type: Array, default: () => [] },
    code: { type: String, default: '' }
  },
  emits: ['submit-success','submit-error'],
  setup(props, { emit }) {
    const note = ref('')
    const submitting = ref(false)

    const selectedItems = computed(() => {
      // фильтруем по selected и сумме qty > 0
      return props.items
          .filter(it => it.selected)
          .map(it => {
            const sizes = { ...it.sizes }
            // оставляем только qty > 0
            const filtered = {}
            Object.keys(sizes).forEach(k => {
              const q = Number(sizes[k]) || 0
              if (q > 0) filtered[k] = q
            })
            return { id: it.id, src: it.src, sizes: filtered }
          })
          .filter(it => Object.keys(it.sizes).length > 0)
    })

    const onSubmit = async () => {
      if (selectedItems.value.length === 0) return alert('Выберите хотя бы одну фотографию и укажите количество для размера.')
      submitting.value = true
      try {
        const payload = {
          code: props.code,
          items: selectedItems.value,
          note: note.value
        }
        await photoService.createOrder(payload)
        emit('submit-success', 'Заявка отправлена. Спасибо!')
        note.value = ''
      } catch (err) {
        console.error(err)
        emit('submit-error', err.message || 'Ошибка отправки')
      } finally {
        submitting.value = false
      }
    }

    return { note, submitting, selectedItems, onSubmit }
  }
}
</script>
