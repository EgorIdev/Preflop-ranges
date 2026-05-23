<script setup lang="ts">

import type {
  RangeHand
} from '../../types/rangeHand'

const props = defineProps<{
  hand: string

  rangeHand?: RangeHand
}>()

const emit = defineEmits<{
  (e: 'mousedown'): void
  (e: 'mouseenter'): void
}>()

</script>

<template>

  <div
    @mousedown="emit('mousedown')"
    @mouseenter="emit('mouseenter')"
    class="
      relative
      aspect-square
      rounded
      overflow-hidden
      cursor-pointer
      border
      border-zinc-700
      select-none
    "
  >

    <!-- RAISE COLOR -->
    <div
      v-if="rangeHand?.raise_percentage"

      class="
        absolute
        left-0
        top-0
        bottom-0
        bg-red-600
      "

      :style="{
        width:
          rangeHand.raise_percentage + '%'
      }"
    />

    <!-- CALL COLOR -->
    <div
      v-if="rangeHand?.call_percentage"

      class="
        absolute
        top-0
        bottom-0
        bg-green-600
      "

      :style="{
        left:
          rangeHand.raise_percentage + '%',

        width:
          rangeHand.call_percentage + '%'
      }"
    />

    <!-- HAND LABEL -->
    <div
      class="
        absolute
        inset-0
        flex
        items-center
        justify-center
        text-sm
        font-semibold
        text-white
      "
    >
      {{ hand }}
    </div>

  </div>

</template>