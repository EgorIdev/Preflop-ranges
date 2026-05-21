<script setup lang="ts">
defineProps<{
  hand: string
  selected: boolean
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
      aspect-square
      rounded
      flex
      items-center
      justify-center
      text-sm
      font-semibold
      cursor-pointer
      transition
      border
      select-none
    "
    :class="selected
      ? 'bg-red-600 border-red-400'
      : 'bg-zinc-800 border-zinc-700 hover:bg-zinc-700'
    "
  >
    {{ hand }}
  </div>

</template>