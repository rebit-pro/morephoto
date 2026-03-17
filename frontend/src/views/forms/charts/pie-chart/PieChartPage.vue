<script setup lang="ts">
import { ref } from 'vue';
// common components
import BaseBreadcrumb from '@/components/shared/BaseBreadcrumb.vue';
import UiParentCard from '@/components/shared/UiParentCard.vue';

// pie charts
import BasicPieChart from './BasicPieChart.vue';
import DonutChart from './DonutChart.vue';
import GaugeChart from './GaugeChart.vue';
import SizeCard from './SizeCard.vue';
import LegendChart from './LegendChart.vue';
import TextOverrideChart from './TextOverrideChart.vue';
import CustomLegend from './CustomLegend.vue';
import OverlayPatterns from './OverlayPatterns.vue';

// theme breadcrumb
const page = ref({ title: 'Pie Charts' });
const breadcrumbs = ref([
  {
    title: 'Charts',
    disabled: false,
    href: '#'
  },
  {
    title: 'Pie Charts',
    disabled: true,
    href: '#'
  }
]);
</script>

<template>
  <BaseBreadcrumb :title="page.title" :breadcrumbs="breadcrumbs" />
  <v-row>
    <v-col cols="12" md="4">
      <UiParentCard title="Basic">
        <BasicPieChart />
      </UiParentCard>
    </v-col>
    <v-col cols="12" md="4">
      <UiParentCard title="Donut">
        <DonutChart />
      </UiParentCard>
    </v-col>
    <v-col cols="12" md="4">
      <UiParentCard title="Gauge">
        <GaugeChart />
      </UiParentCard>
    </v-col>
    <v-col cols="12" md="6">
      <UiParentCard title="With Legend" class="overflow-hidden">
        <LegendChart />
      </UiParentCard>
    </v-col>
    <v-col cols="12" md="6">
      <UiParentCard title="Item text overrides" class="overflow-hidden">
        <TextOverrideChart />
      </UiParentCard>
    </v-col>
    <v-col cols="12">
      <UiParentCard title="Size">
        <SizeCard />
      </UiParentCard>
    </v-col>
    <v-col cols="12" xl="6">
      <UiParentCard title="Custom Legend" class="overflow-hidden">
        <CustomLegend />
      </UiParentCard>
    </v-col>
    <v-col cols="12" xl="6">
      <UiParentCard title="Overlay patterns" class="overflow-hidden">
        <OverlayPatterns />
      </UiParentCard>
    </v-col>
  </v-row>
</template>
