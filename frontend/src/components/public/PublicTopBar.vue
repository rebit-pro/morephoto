<template>
  <div class="public-top-bar">
    <RouterLink class="public-top-bar__brand" to="/">
      <div class="public-top-bar__logo">R</div>
      <div>
        <div class="public-top-bar__title">Rebit P2P</div>
        <div class="public-top-bar__subtitle">Контроль сделок, балансов и рисков в одном интерфейсе</div>
      </div>
    </RouterLink>

    <div class="public-top-bar__actions">
      <v-btn
        class="public-top-bar__button public-top-bar__button--docs"
        :class="{ 'public-top-bar__button--active': isDocumentationPage }"
        variant="text"
        color="white"
        rounded="lg"
        to="/documentation"
      >
        Документация
      </v-btn>
      <v-btn class="public-top-bar__button" variant="text" color="white" rounded="lg" to="/login">Вход</v-btn>
      <v-btn class="public-top-bar__button" color="secondary" variant="flat" rounded="lg" to="/register">Регистрация</v-btn>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { RouterLink, useRoute } from 'vue-router';

const route = useRoute();
const isDocumentationPage = computed(() => '/documentation' === route.path);
</script>

<style scoped lang="scss">
.public-top-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 24px 0;
}

.public-top-bar__brand {
  display: inline-flex;
  align-items: center;
  gap: 14px;
  color: inherit;
  text-decoration: none;
}

.public-top-bar__logo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 48px;
  height: 48px;
  border-radius: 16px;
  background: linear-gradient(135deg, rgba(94, 53, 177, 0.9) 0%, rgba(30, 136, 229, 0.9) 100%);
  box-shadow: 0 12px 30px rgba(15, 23, 42, 0.2);
  font-size: 1.25rem;
  font-weight: 800;
  color: #ffffff;
}

.public-top-bar__title {
  font-size: 1.1rem;
  font-weight: 800;
  line-height: 1.2;
}

.public-top-bar__subtitle {
  font-size: 0.875rem;
  line-height: 1.4;
  color: rgba(255, 255, 255, 0.72);
}

.public-top-bar__actions {
  display: inline-flex;
  align-items: center;
  justify-content: flex-end;
  gap: 12px;
}

.public-top-bar__button--docs {
  opacity: 0.86;
}

.public-top-bar__button--active {
  background: rgba(255, 255, 255, 0.1);
  opacity: 1;
}

@media (max-width: 767px) {
  .public-top-bar {
    flex-direction: column;
    align-items: stretch;
  }

  .public-top-bar__brand {
    justify-content: center;
  }

  .public-top-bar__actions {
    width: 100%;
    justify-content: stretch;
  }

  .public-top-bar__button {
    flex: 1 1 0;
  }
}
</style>
