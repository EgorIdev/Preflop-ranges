<script setup lang="ts">
import { computed } from 'vue'

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

const backgroundStyle = computed(() => {

  if (!props.rangeHand) {

    return {
      background: '#27272a',
    }
  }

  const raise =
    props.rangeHand.raise_percentage

  const call =
    props.rangeHand.call_percentage

  const fold =
    props.rangeHand.fold_percentage

  const raiseColor = '#dc2626'
  const callColor = '#16a34a'
  const foldColor = '#27272a'

  const raiseEnd = raise

  const callEnd =
    raise + call

  return {
    background: `
      linear-gradient(
        to bottom,

        ${raiseColor} 0%,
        ${raiseColor} ${raiseEnd}%,

        ${callColor} ${raiseEnd}%,
        ${callColor} ${callEnd}%,

        ${foldColor} ${callEnd}%,
        ${foldColor} 100%
      )
    `,
  }
})
</script>

<template>

  <div
    @mousedown="emit('mousedown')"
    @mouseenter="emit('mouseenter')"

    class="
      w-11
      h-11
      aspect-square
      rounded
      flex
      items-center
      justify-center
      text-[18px]
      font-semibold
      cursor-pointer
      transition
      border
      border-zinc-700
      select-none
      hover:brightness-110
    "

    :style="backgroundStyle"
  >
    {{ hand }}
  </div>

</template>