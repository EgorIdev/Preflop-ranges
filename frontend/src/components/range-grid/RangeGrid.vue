<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

import { generateGrid } from '../../utils/poker'

import RangeCell from './RangeCell.vue'

import type { RangeHand } from '../../types/rangeHand'

const props = defineProps<{
  selectedHands: RangeHand[]
}>()

const emit = defineEmits<{
  (e: 'toggle-hand', hand: string): void
}>()

const grid = generateGrid()

const isMouseDown = ref(false)

const handleMouseDown = (hand: string) => {
  isMouseDown.value = true

  emit('toggle-hand', hand)
}

const handleMouseEnter = (hand: string) => {

  if (isMouseDown.value) {
    emit('toggle-hand', hand)
  }
}

const stopPainting = () => {
  isMouseDown.value = false
}

onMounted(() => {
  window.addEventListener('mouseup', stopPainting)
})

onUnmounted(() => {
  window.removeEventListener('mouseup', stopPainting)
})
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

      :selected="
        props.selectedHands.some(
          item => item.hand === hand
        )
      "

      :action="
        props.selectedHands.find(
          item => item.hand === hand
        )?.action
      "

      @mousedown="handleMouseDown(hand)"
      @mouseenter="handleMouseEnter(hand)"
    />

    </template>

  </div>

</template>