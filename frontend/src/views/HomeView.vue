<script setup lang="ts">
import { ref, onMounted } from 'vue'

import {
  getRanges,
  createRange
} from '../services/rangeService'

import type { Range } from '../types/range'

const ranges = ref<Range[]>([])
const rangeName = ref('')
const rangeSpot = ref('')

const loadRanges = async () => {
  try {
    ranges.value = await getRanges()
  } catch (error) {
    console.error(error)
  }
}

const handleCreateRange = async () => {
  try {

    await createRange(
      rangeName.value,
      rangeSpot.value
    )

    await loadRanges()

    rangeName.value = ''
    rangeSpot.value = ''

  } catch (error) {
    console.error(error)
  }
}

onMounted(() => {
  loadRanges()
})
</script>

<template>
  <div class="min-h-screen bg-zinc-900 text-white p-8">

    <h1 class="text-4xl font-bold mb-8">
      Preflop Ranges
    </h1>

    <div class="flex gap-4 mb-8">
      <button class="bg-blue-600 px-4 py-2 rounded-xl">
        Editor
      </button>

      <button class="bg-zinc-700 px-4 py-2 rounded-xl">
        Trainer
      </button>
    </div>

    <div class="bg-zinc-800 p-6 rounded-xl mb-8">

  <div class="flex gap-4">

    <input
      v-model="rangeName"
      type="text"
      placeholder="Range name"
      class="bg-zinc-900 px-4 py-2 rounded-xl w-full"
    />

    <input
      v-model="rangeSpot"
      type="text"
      placeholder="Spot"
      class="bg-zinc-900 px-4 py-2 rounded-xl w-full"
    />

    <button
      @click="handleCreateRange"
      class="bg-green-600 px-6 py-2 rounded-xl"
    >
      Create
    </button>

  </div>

</div>

    <div class="space-y-4">

      <div
        v-for="range in ranges"
        :key="range.id"
        class="bg-zinc-800 p-4 rounded-xl"
      >
        <div class="text-xl font-semibold">
          {{ range.name }}
        </div>

        <div class="text-zinc-400">
          {{ range.spot }}
        </div>
      </div>

    </div>

  </div>
</template>