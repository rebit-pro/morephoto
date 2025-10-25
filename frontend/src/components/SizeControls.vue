<template>
  <div>
    <div v-for="sz in sizesList" :key="sz" class="size-row">
      <div style="width:90px">{{ labelFor(sz) }}</div>
      <quantity-control v-model="local[sz]" />
      <div class="small-muted">шт.</div>
    </div>
  </div>
</template>

<script>
import { reactive, watch } from 'vue'
import QuantityControl from './QuantityControl.vue'

export default {
  name: 'SizeControls',
  components: { QuantityControl },
  props: {
    sizes: {
      type: Object,
      default: () => ({ '10x15':0,'15x21':0,'20x30':0 })
    }
  },
  emits: ['update'],
  setup(props, { emit }) {
    const sizesList = ['10x15','15x21','20x30']
    const local = reactive({ ...props.sizes })

    watch(() => props.sizes, (v) => {
      Object.assign(local, v)
    }, { deep: true })

    watch(local, () => {
      // Normalize to integers >= 0
      const normalized = {}
      sizesList.forEach(k => {
        const n = Number(local[k]) || 0
        normalized[k] = n < 0 ? 0 : Math.floor(n)
      })
      emit('update', normalized)
    }, { deep: true })

    const labelFor = (s) => {
      if (s === '10x15') return '10 × 15'
      if (s === '15x21') return '15 × 21'
      if (s === '20x30') return '20 × 30'
      return s
    }

    return { local, sizesList, labelFor }
  }
}
</script>
