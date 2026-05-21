<script setup lang="ts">
import { generateGrid } from '../../utils/poker'

import RangeCell from './RangeCell.vue'

const grid = generateGrid()
</script>

<template>

  <div
    class="
      grid
      grid-cols-13
      gap-1
    "
  >

    <template
      v-for="row in grid"
      :key="row[0]"
    >

      <RangeCell
        v-for="hand in row"
        :key="hand"
        :hand="hand"
      />

    </template>

  </div>

</template>