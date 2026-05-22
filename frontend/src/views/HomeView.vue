<script setup lang="ts">
import { ref, onMounted } from 'vue'

import {
  getRanges,
  createRange,
  saveRangeItems,
  deleteRange
} from '../services/rangeService'

import type { Range } from '../types/range'

import type { RangeHand } from '../types/rangeHand'

import RangeGrid from '../components/range-grid/RangeGrid.vue'

const ranges = ref<Range[]>([])
const rangeName = ref('')
const selectedHands = ref<RangeHand[]>([])
const currentRangeId = ref<number | null>(null)
const currentAction = ref<'raise' | 'call'>('raise')

const loadRanges = async () => {
  try {
    ranges.value = await getRanges()

  if (ranges.value.length > 0) {
    loadRange(ranges.value[0])
  }


  } catch (error) {
    console.error(error)
  }
}

const loadRange = (range: Range) => {

  currentRangeId.value = range.id

  selectedHands.value =
  range.items.map(item => ({
    hand: item.hand,
    action: item.action as 'raise' | 'call',
  }))
}

const toggleHand = (hand: string) => {

  const existing =
    selectedHands.value.find(
      item => item.hand === hand
    )

  if (!existing) {

    selectedHands.value.push({
      hand,
      action: currentAction.value,
    })

    return
  }

  if (existing.action === currentAction.value) {

    selectedHands.value =
      selectedHands.value.filter(
        item => item.hand !== hand
      )

    return
  }

  existing.action = currentAction.value
}

const handleSaveRange = async () => {

  try {

    if (!currentRangeId.value) {
      return
    }

    const items = selectedHands.value

    await saveRangeItems(
      currentRangeId.value,
      items
    )

    alert('Range saved')

  } catch (error) {
    console.error(error)
  }
}

const handleDeleteRange = async (
  rangeId: number
) => {

  try {

    await deleteRange(rangeId)

    if (currentRangeId.value === rangeId) {

      selectedHands.value = []

      currentRangeId.value = null
    }

    await loadRanges()

  } catch (error: any) {

    console.error(error)

    alert(
      JSON.stringify(
        error.response?.data,
        null,
        2
      )
    )
  }
}

const handleCreateRange = async () => {
  try {

    await createRange(
      rangeName.value
    )

    await loadRanges()

    rangeName.value = ''

  } catch (error: any) {

      console.error(error)

      alert(
        JSON.stringify(
          error.response?.data,
          null,
          2
        )
      )
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
          @click="loadRange(range)"
          :class="
            currentRangeId === range.id
              ? 'bg-zinc-700 border border-blue-500'
              : 'bg-zinc-800'
          "

          class="
            p-4
            rounded-xl
            cursor-pointer
            transition
          "
        >
        <div class="flex items-center justify-between">

          <div>

            <div class="text-xl font-semibold">
              {{ range.name }}
            </div>

          </div>

          <div class="flex gap-2">

            <button
              @click.stop="handleSaveRange"
              class="
                bg-blue-600
                hover:bg-blue-500
                px-4
                py-2
                rounded-xl
                transition
              "
            >
              Save
            </button>

            <button
              @click.stop="handleDeleteRange(range.id)"
              class="
                bg-red-700
                hover:bg-red-600
                px-4
                py-2
                rounded-xl
                transition
              "
            >
              Delete
            </button>

          </div>

        </div>
      </div>

      <div class="mt-12">

        <div class="flex gap-4 mb-6">

          <button
            @click="currentAction = 'raise'"
            class="px-4 py-2 rounded-xl"
            :class="
              currentAction === 'raise'
                ? 'bg-red-600'
                : 'bg-zinc-700'
            "
          >
            Raise
          </button>

          <button
            @click="currentAction = 'call'"
            class="px-4 py-2 rounded-xl"
            :class="
              currentAction === 'call'
                ? 'bg-green-600'
                : 'bg-zinc-700'
            "
          >
            Call
          </button>

        </div>

        <RangeGrid
          :selectedHands="selectedHands"
          @toggle-hand="toggleHand"
        />

      </div>
    </div>

  </div>
  
</template>