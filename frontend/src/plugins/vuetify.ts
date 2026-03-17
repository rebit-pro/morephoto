import { createVuetify } from 'vuetify';
import { aliases, mdi } from 'vuetify/iconsets/mdi-svg';
import { VFileUpload, VFileUploadItem } from 'vuetify/labs/VFileUpload';
import { VPie } from 'vuetify/labs/VPie';
import { VCalendar } from 'vuetify/labs/VCalendar';
import { VMaskInput } from 'vuetify/labs/VMaskInput';
import { icons } from './mdi-icon'; // Import icons from separate file
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { PurpleTheme, GreenTheme, PinkTheme, YellowTheme, SeaGreenTheme, OliveGreenTheme, SpeechBlueTheme } from '@/theme/LightTheme';
import {
  DarkPurpleTheme,
  DarkGreenTheme,
  DarkSpeechBlueTheme,
  DarkOliveGreenTheme,
  DarkPinkTheme,
  DarkYellowTheme,
  DarkSeaGreenTheme
} from '@/theme/DarkTheme';
import { createVueI18nAdapter } from 'vuetify/locale/adapters/vue-i18n';
import { createI18n, useI18n } from 'vue-i18n';
import { messages } from '@/utils/locales/messages';

export const i18n = createI18n({
  legacy: false,
  locale: 'en',
  fallbackLocale: 'en',
  messages
});

export default createVuetify({
  locale: {
    adapter: createVueI18nAdapter({ i18n, useI18n })
  },
  components: {
    ...components,
    VFileUpload,
    VFileUploadItem,
    VPie,
    VCalendar,
    VMaskInput
  },
  directives,
  icons: {
    defaultSet: 'mdi',
    aliases: {
      ...aliases,
      ...icons
    },
    sets: {
      mdi
    }
  },
  theme: {
    defaultTheme: 'PurpleTheme',
    themes: {
      PurpleTheme,
      GreenTheme,
      PinkTheme,
      YellowTheme,
      SeaGreenTheme,
      OliveGreenTheme,
      SpeechBlueTheme,
      DarkPurpleTheme,
      DarkGreenTheme,
      DarkSpeechBlueTheme,
      DarkOliveGreenTheme,
      DarkPinkTheme,
      DarkYellowTheme,
      DarkSeaGreenTheme
    }
  },
  defaults: {
    VBtn: {},
    VCard: {
      rounded: 'md'
    },
    VTextField: {
      rounded: 'lg'
    },
    VTooltip: {
      // set v-tooltip default location to top
      location: 'top'
    }
  }
});
