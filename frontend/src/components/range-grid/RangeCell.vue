<script setup lang="ts">
defineProps<{
  hand: string
}>()
</script>

<template>

  <div
    class="
      aspect-square
      bg-zinc-800
      rounded
      flex
      items-center
      justify-center
      text-sm
      font-semibold
      cursor-pointer
      hover:bg-zinc-700
      transition
    "
  >
    {{ hand }}
  </div>

</template>