<template>
  <div>
    <v-card class="pa-4">
      <div class="d-flex gap-3 mb-3">
        <v-btn @click="$refs.file.click()" variant="outlined">Select photos</v-btn>
        <v-btn variant="text" @click="clearAll" :disabled="items.length===0">Clear</v-btn>
        <div class="text-subtitle-2 ml-auto">{{ items.length }} photos</div>
      </div>


      <input ref="file" type="file" multiple accept="image/*" @change="onFiles" style="display:none" />


      <v-row>
        <v-col v-for="(item, idx) in items" :key="item.id" cols="12" sm="6" md="4">
          <photo-card :item="item" @update-item="updateItem(idx, $event)" @remove="remove(idx)" />
        </v-col>
      </v-row>


      <div v-if="items.length===0" class="text-center pa-6 grey--text">No photos selected — click "Select photos" to add.</div>
    </v-card>
  </div>
</template>

<script>
import PhotoCard from './PhotoCard.vue'
import { ref, watch, toRaw } from 'vue'

export default {
  name: 'PhotoUploader',
  components:{ PhotoCard },
  props: {
    items: { type: Array, default: () => [] }
  },
  emits: ['update:items'],
  setup(props, { emit }){
    const items = ref([...props.items])

    watch(()=>props.items, (v)=>{ items.value = [...v] })

    const onFiles = async (e) => {
      const files = Array.from(e.target.files || [])
      const promises = files.map(async (file)=>{
        const dataUrl = await readFileAsDataURL(file)
        return {
          id: cryptoRandomId(),
          file, // keep original File for submission
          preview: dataUrl,
          size: '10x15',
          qty: 1
        }
      })
      const newItems = await Promise.all(promises)
      items.value.push(...newItems)
      emit('update:items', items.value)
      e.target.value = null
    }

    const updateItem = (idx, payload) => {
      items.value[idx] = payload
      emit('update:items', items.value)
    }

    const remove = (idx) => {
      items.value.splice(idx,1)
      emit('update:items', items.value)
    }

    const clearAll = ()=>{ items.value = []; emit('update:items', items.value) }

    return { items, onFiles, updateItem, remove, clearAll }
  }
}

function readFileAsDataURL(file){
  return new Promise((res, rej)=>{
    const r = new FileReader()
    r.onload = ()=>res(r.result)
    r.onerror = rej
    r.readAsDataURL(file)
  })
}

function cryptoRandomId(){
  return Math.random().toString(36).slice(2,9)
}
</script>