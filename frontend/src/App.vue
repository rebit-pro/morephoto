<template>
  <v-app>
    <v-main>
      <v-container class="pa-6">
        <v-row align="center">
          <v-col cols="12" class="d-flex justify-space-between align-center">
            <h1>Photo Printing</h1>
            <div>Quick print — select photos, sizes & quantities</div>
          </v-col>
        </v-row>


        <v-row>
          <v-col cols="12" md="8">
            <photo-uploader v-model:items="items" />
          </v-col>


          <v-col cols="12" md="4">
            <order-summary :items="items" @submit-order="submitOrder" />
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>


<script>
import PhotoUploader from './components/PhotoUploader.vue'
import OrderSummary from './components/OrderSummary.vue'
import { ref } from 'vue'


export default {
  components: { PhotoUploader, OrderSummary },
  setup(){
    const items = ref([])


    const submitOrder = async (payload) => {
// payload: FormData created by OrderSummary
      const base = import.meta.env.VITE_API_BASE_URL || '/api'
      try{
        const resp = await fetch(`${base}/orders`, { method: 'POST', body: payload })
        if(!resp.ok) throw new Error(await resp.text())
        alert('Order submitted successfully')
        items.value = []
      }catch(e){
        console.error(e)
        alert('Submit failed: ' + (e.message || e))
      }
    }


    return { items, submitOrder }
  }
}
</script>


<style src="./styles.css"></style>