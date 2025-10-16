import { createApp } from 'vue'
import App from './App.vue'
import 'vuetify/lib/styles/main.css'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'


const vuetify = createVuetify({
    components,
    directives,
})


createApp(App).use(vuetify).mount('#app')
