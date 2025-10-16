<template>
  <v-card class="pa-4">
    <h3>Order Summary</h3>
    <div v-if="items.length===0" class="grey--text pa-6">No items yet</div>

    <v-list v-else>
      <v-list-item v-for="(it, idx) in items" :key="it.id">
        <v-list-item-avatar>
          <v-img :src="it.preview" />
        </v-list-item-avatar>
        <v-list-item-content>
          <div class="text-subtitle-2">Size: {{ it.size }}</div>
          <div class="text-caption">Qty: {{ it.qty }}</div>
        </v-list-item-content>
      </v-list-item>
    </v-list>

    <v-textarea v-model="note" label="Notes (optional)" rows="2" class="mt-4" />

    <div class="d-flex align-center mt-4">
      <v-btn :disabled="items.length===0 || submitting" @click="onSubmit" elevation="2">Submit Order</v-btn>
      <v-spacer />
      <div v-if="submitting">Sending...</div>
    </div>
  </v-card>
</template>

<script>
import { ref } from 'vue'
export default {
  name: 'OrderSummary',
  props: { items: { type: Array, default: () => [] } },
  emits: ['submit-order'],
  setup(props, { emit }){
    const note = ref('')
    const submitting = ref(false)

    const onSubmit = async ()=>{
      if(props.items.length===0) return alert('Add at least one photo')
      submitting.value = true

      // create FormData
      const form = new FormData()
      const meta = props.items.map((it, i)=>({ id: it.id, filename: it.file?.name || `photo-${i}.jpg`, size: it.size, qty: it.qty }))
      form.append('meta', JSON.stringify({ items: meta, note: note.value }))

      // append files
      props.items.forEach((it, idx)=>{
        if(it.file) form.append('file_' + idx, it.file, it.file.name)
      })

      try{
        await emit('submit-order', form)
      }finally{
        submitting.value = false
      }
    }

    return { note, submitting, onSubmit }
  }
}
</script>