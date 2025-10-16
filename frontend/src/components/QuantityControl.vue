<template>
  <div class="d-flex align-center">
    <v-btn small @click="decrement" :disabled="modelValue<=1">-</v-btn>
    <v-text-field
        v-model.number="local"
        type="number"
        style="width:72px"
        dense
        hide-details
    />
    <v-btn small @click="increment">+</v-btn>
  </div>
</template>

<script>
import { ref, watch } from 'vue'
export default {
  name: 'QuantityControl',
  props: { modelValue: { type: Number, default: 1 } },
  emits: ['update:modelValue'],
  setup(props, { emit }){
    const local = ref(props.modelValue)
    watch(()=>props.modelValue, v=>local.value = v)
    watch(local, v=>emit('update:modelValue', Number(v)))

    const increment = ()=> local.value = Number(local.value) + 1
    const decrement = ()=> { if(local.value>1) local.value = Number(local.value) - 1 }

    return { local, increment, decrement }
  }
}
</script>