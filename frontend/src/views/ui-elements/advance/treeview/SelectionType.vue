<script setup lang="ts">
import { ref, shallowRef } from 'vue';
// common components
import UiChildCard from '@/components/shared/UiChildCard.vue';

// selection type
interface TreeNode {
  id: number;
  title: string;
  children?: TreeNode[];
}
type SelectStrategyProp = 'leaf' | 'single-leaf' | 'independent' | 'single-independent' | 'classic';

const strategy = shallowRef<SelectStrategyProp>('leaf');
const selected = shallowRef<TreeNode[]>([]);
const selectionItems = ref<TreeNode[]>([
  {
    id: 1,
    title: 'Root',
    children: [
      { id: 2, title: 'Child #1' },
      { id: 3, title: 'Child #2' },
      {
        id: 4,
        title: 'Child #3',
        children: [
          { id: 5, title: 'Grandchild #1' },
          { id: 6, title: 'Grandchild #2' }
        ]
      }
    ]
  }
]);
</script>

<template>
  <UiChildCard title="Selection type">
    <v-select
      v-model="strategy"
      :items="['leaf', 'single-leaf', 'independent', 'single-independent', 'classic']"
      label="Selection type"
      variant="outlined"
    ></v-select>

    <v-row>
      <v-col cols="12" md="6">
        <v-treeview
          v-model:selected="selected"
          :items="selectionItems"
          :select-strategy="strategy"
          item-value="id"
          return-object
          selectable
        ></v-treeview>
      </v-col>

      <v-divider vertical></v-divider>

      <v-col class="pa-6" cols="12" md="6">
        <template v-if="!selected.length">No nodes selected.</template>

        <template v-else>
          <div v-for="node in selected" :key="node.id">
            {{ node.title }}
          </div>
        </template>
      </v-col>
    </v-row>
  </UiChildCard>
</template>
