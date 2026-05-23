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

import {
  getRangeGroups,
  createRangeGroup
} from '../services/rangeGroupService'

import type {
  RangeGroup
} from '../types/range-group'

const ranges = ref<Range[]>([])
const rangeName = ref('')
const selectedHands = ref<RangeHand[]>([])
const currentRangeId = ref<number | null>(null)
const currentAction =
  ref<'raise' | 'call'>('raise')

const currentPercentage =
  ref(100)

const groups = ref<RangeGroup[]>([])
const groupName = ref('')
const currentGroupId =
  ref<number | null>(null)

const selectGroup = (
  groupId: number
) => {

  currentGroupId.value = groupId
}
const loadGroups = async () => {

  try {

    groups.value =
      await getRangeGroups()

  } catch (error) {

    console.error(error)
  }
}

const handleCreateGroup = async () => {

  try {

    await createRangeGroup(
      groupName.value
    )

    await loadGroups()

    groupName.value = ''

  } catch (error) {

    console.error(error)
  }
}

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

      raise_percentage:
        item.raise_percentage,

      call_percentage:
        item.call_percentage,

      fold_percentage:
        item.fold_percentage,
    }))
}

const toggleHand = (hand: string) => {

  const existing =
    selectedHands.value.find(
      item => item.hand === hand
    )

  // ЕСЛИ РУКИ ЕЩЕ НЕТ
  if (!existing) {

    selectedHands.value.push({

      hand,

      raise_percentage:
        currentAction.value === 'raise'
          ? currentPercentage.value
          : 0,

      call_percentage:
        currentAction.value === 'call'
          ? currentPercentage.value
          : 0,

      fold_percentage:
        100 - currentPercentage.value,
    })

    return
  }

  // ОБНОВЛЯЕМ СУЩЕСТВУЮЩУЮ РУКУ

  if (currentAction.value === 'raise') {

    existing.raise_percentage =
      currentPercentage.value

    existing.fold_percentage =
      100
      - existing.raise_percentage
      - existing.call_percentage
  }

  if (currentAction.value === 'call') {

    existing.call_percentage =
      currentPercentage.value

    existing.fold_percentage =
      100
      - existing.raise_percentage
      - existing.call_percentage
  }

  // НЕ ДАЕМ УЙТИ В МИНУС

  if (existing.fold_percentage < 0) {
    existing.fold_percentage = 0
  }
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
      rangeName.value,
      currentGroupId.value
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
  loadGroups()
})
</script>

<template>

  <div class="min-h-screen bg-zinc-900 text-white p-8">

    <!-- HEADER -->
    <div
      class="
        flex
        items-center
        justify-between
        mb-8
      "
    >

      <h1 class="text-4xl font-bold">
        Preflop Ranges
      </h1>

      <div class="flex gap-4">

        <button
          class="
            bg-blue-600
            px-4
            py-2
            rounded-xl
          "
        >
          Editor
        </button>

        <button
          class="
            bg-zinc-700
            px-4
            py-2
            rounded-xl
          "
        >
          Trainer
        </button>

      </div>

    </div>

    <!-- MAIN LAYOUT -->
    <div
      class="
        grid
        grid-cols-1
        lg:grid-cols-2
        gap-8
        items-start
      "
    >

      <!-- LEFT PANEL -->
      <div class="space-y-6">

        <!-- CREATE GROUP -->
        <div
          class="
            bg-zinc-800
            p-6
            rounded-xl
          "
        >

          <div class="flex gap-4">

            <input
              v-model="groupName"
              type="text"
              placeholder="Group name"
              class="
                bg-zinc-900
                px-4
                py-2
                rounded-xl
                w-full
              "
            />

            <button
              @click="handleCreateGroup"
              class="
                bg-purple-600
                px-6
                py-2
                rounded-xl
              "
            >
              Create Group
            </button>

          </div>

        </div>

        <!-- CREATE RANGE -->
        <div
          class="
            bg-zinc-800
            p-6
            rounded-xl
          "
        >

          <div class="flex gap-4">

            <input
              v-model="rangeName"
              type="text"
              placeholder="Range name"
              class="
                bg-zinc-900
                px-4
                py-2
                rounded-xl
                w-full
              "
            />

            <button
              @click="handleCreateRange"
              class="
                bg-green-600
                px-6
                py-2
                rounded-xl
              "
            >
              Create
            </button>

          </div>

        </div>


        <!-- GROUPS -->
        <div class="space-y-6">

          <div
            v-for="group in groups"
            :key="group.id"
            @click="selectGroup(group.id)"
            class="
              rounded-xl
              p-4
              transition
              cursor-pointer
            "
            :class="
              currentGroupId === group.id
                ? 'bg-zinc-700 ring-2 ring-blue-500'
                : 'bg-zinc-800'
            "
          >

            <!-- GROUP HEADER -->
            <div
              class="
                flex
                items-center
                justify-between
                mb-4
              "
            >

              <div
                class="
                  text-2xl
                  font-bold
                "
              >
                {{ group.name }}
              </div>

              <div class="flex gap-2">

                <button
                  class="
                    bg-yellow-600
                    px-3
                    py-1
                    rounded-lg
                  "
                >
                  Rename
                </button>

                <button
                  class="
                    bg-blue-600
                    px-3
                    py-1
                    rounded-lg
                  "
                >
                  Save
                </button>

                <button
                  class="
                    bg-red-600
                    px-3
                    py-1
                    rounded-lg
                  "
                >
                  Delete
                </button>

              </div>

            </div>

            <!-- RANGES -->
            <div class="space-y-2">

              <div
                v-for="range in group.ranges"
                :key="range.id"
                @click.stop="loadRange(range)"
                class="
                  bg-zinc-900
                  p-4
                  rounded-xl
                  cursor-pointer
                "
              >

                <div class="flex justify-between">

                  <div class="font-semibold">
                    {{ range.name }}
                  </div>

                  <div class="flex gap-2">

                    <button
                      @click.stop="handleSaveRange"
                      class="
                        bg-blue-600
                        px-3
                        py-1
                        rounded-lg
                      "
                    >
                      Save
                    </button>

                    <button
                      class="
                        bg-yellow-600
                        px-3
                        py-1
                        rounded-lg
                      "
                    >
                      Rename
                    </button>

                    <button
                      class="
                        bg-purple-600
                        px-3
                        py-1
                        rounded-lg
                      "
                    >
                      Copy
                    </button>

                    <button
                      @click.stop="
                        handleDeleteRange(range.id)
                      "
                      class="
                        bg-red-600
                        px-3
                        py-1
                        rounded-lg
                      "
                    >
                      Delete
                    </button>

                  </div>

                </div>

              </div>

            </div>

          </div>

        </div>

      </div>

      <!-- RIGHT PANEL -->
      <div>

        <!-- ACTION BUTTONS -->
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

          <div
            class="
              flex
              items-center
              gap-4
              mb-6
            "
          >

            <div class="w-24">
              {{ currentPercentage }}%
            </div>

            <input
              v-model="currentPercentage"
              type="range"
              min="0"
              max="100"
              class="w-full"
            />

          </div>          

        </div>

        <!-- RANGE GRID -->
        <RangeGrid
          :selectedHands="selectedHands"
          @toggle-hand="toggleHand"
        />

      </div>

    </div>

  </div>

</template>