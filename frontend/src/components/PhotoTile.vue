<template>
  <v-card :class="['photo-tile', { selected: local.selected }]" outlined>
    <v-img :src="photo.src" height="180px" cover @click="toggleSelected" class="cursor-pointer">
      <template #placeholder>
        <v-row class="fill-height ma-0" align="center" justify="center">
          <div class="small-muted">Загрузка...</div>
        </v-row>
      </template>

      <div style="position:absolute; left:8px; top:8px;">
        <v-checkbox v-model="local.selected" @click.stop="onCheckboxClick" :aria-label="'Выбрать фото ' + photo.id" />
      </div>
    </v-img>

    <v-card-text>
      <div class="d-flex align-center">
        <div class="text-subtitle-2">Фото #{{ photo.id }}</div>
        <v-spacer />
        <v-btn icon small @click="$emit('remove')" aria-label="Remove" style="visibility:hidden">
          <svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9Z"/></svg>
        </v-btn>
      </div>

      <div v-if="local.selected" class="mt-3">
        <SizeControls :sizes="local.sizes" @update="onSizesUpdate" />
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
import { reactive, watch, nextTick } from 'vue'
import SizeControls from './SizeControls.vue'

export default {
  name: 'PhotoTile',
  components: { SizeControls },
  props: {
    photo: { type: Object, required: true }
  },
  emits: ['update', 'remove'],
  setup(props, { emit }) {
    const local = reactive({
      id: props.photo.id,
      src: props.photo.src,
      selected: !!props.photo.selected,
      sizes: { ...(props.photo.sizes || { '10x15':0,'15x21':0,'20x30':0 }) }
    })

    // Флаг, чтобы не эмитить update при синхронизации из пропсов
    let syncingFromProps = false

    // Синхронизация из пропсов (например, после загрузки/сброса родителем)
    watch(() => props.photo, async (n) => {
      if (!n) return
      syncingFromProps = true
      local.id = n.id
      local.src = n.src
      local.selected = !!n.selected
      local.sizes = { ...(n.sizes || { '10x15':0,'15x21':0,'20x30':0 }) }
      await nextTick()
      syncingFromProps = false
    }, { deep: true })

    // Эмитим изменения только при реальных действиях пользователя
    watch(
      () => [local.selected, local.sizes['10x15'], local.sizes['15x21'], local.sizes['20x30']],
      () => {
        if (syncingFromProps) return
        emit('update', { id: local.id, src: local.src, selected: local.selected, sizes: { ...local.sizes } })
      }
    )

    const toggleSelected = () => {
      local.selected = !local.selected
      if (!local.selected) {
        local.sizes = { '10x15':0,'15x21':0,'20x30':0 }
      } else {
        const sum = Object.values(local.sizes).reduce((a,b)=>a+(Number(b)||0),0)
        if (sum === 0) local.sizes['10x15'] = 1
      }
    }

    const onCheckboxClick = () => {
      if (local.selected) {
        const sum = Object.values(local.sizes).reduce((a,b)=>a+(Number(b)||0),0)
        if (sum === 0) local.sizes['10x15'] = 1
      } else {
        local.sizes = { '10x15':0,'15x21':0,'20x30':0 }
      }
    }

    const onSizesUpdate = (newSizes) => {
      local.sizes = { ...newSizes }
      const sum = Object.values(local.sizes).reduce((a,b)=>a+(Number(b)||0),0)
      if (sum > 0) local.selected = true
    }

    return { local, toggleSelected, onCheckboxClick, onSizesUpdate }
  }
}
</script>

<style scoped>
.photo-tile { cursor: default; }
.photo-tile .v-img { cursor: pointer; }
</style>