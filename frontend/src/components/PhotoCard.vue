<template>
  <v-card>
    <v-img :src="item.preview" height="180px" />
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <v-select
              :items="sizes"
              v-model="local.size"
              label="Size"
              dense
              hide-details
          />
        </v-col>

        <v-col cols="12" class="d-flex align-center">
          <quantity-control v-model="local.qty" />
          <v-spacer />
          <v-btn icon @click="$emit('remove')" aria-label="Remove photo">
            <svg style="width:24px;height:24px" viewBox="0 0 24 24"><path fill="currentColor" d="M9,3V4H4V6H5V19A2,2 0 0,0 7,21H17A2,2 0 0,0 19,19V6H20V4H15V3H9Z"/></svg>
          </v-btn>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import QuantityControl from './QuantityControl.vue'
import { reactive, watch } from 'vue'

export default {
  name: 'PhotoCard',
  components: { QuantityControl },
  props: { item: { type: Object, required: true } },
  emits: ['update-item','remove'],
  setup(props, { emit }){
    const sizes = ['10x15','15x21','20x30']
    const local = reactive({ ...props.item })

    watch(local, ()=>{
      emit('update-item', { ...local })
    }, { deep: true })

    return { local, sizes }
  }
}
</script>