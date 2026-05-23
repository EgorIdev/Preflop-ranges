<script setup lang="ts">
import { ref, onMounted } from 'vue'

import {
  getRanges,
  createRange,
  saveRangeItems,
  deleteRange,
  copyRange
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
  ref<'raise' | 'call' | 'erase'>(
    'raise'
  )

const currentPercentage =
  ref(100)

const groups = ref<RangeGroup[]>([])
const groupName = ref('')
const currentGroupId =
  ref<number | null>(null)
const openedGroups =
  ref<number[]>([])

const openedGroupMenuId =
  ref<number | null>(null)

const openedRangeMenuId =
  ref<number | null>(null)

const selectGroup = (
  groupId: number
) => {

  currentGroupId.value =
    currentGroupId.value === groupId
      ? null
      : groupId
}
const toggleGroup = (
  groupId: number
) => {

  selectGroup(groupId)

  if (
    openedGroups.value.includes(groupId)
  ) {

    openedGroups.value =
      openedGroups.value.filter(
        id => id !== groupId
      )

    return
  }

  openedGroups.value.push(groupId)
}
const toggleGroupMenu = (
  groupId: number
) => {

  openedGroupMenuId.value =
    openedGroupMenuId.value === groupId
      ? null
      : groupId

  openedRangeMenuId.value = null
}
const toggleRangeMenu = (
  rangeId: number
) => {

  openedRangeMenuId.value =
    openedRangeMenuId.value === rangeId
      ? null
      : rangeId

  openedGroupMenuId.value = null
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

// ERASE MODE

  if (
    currentAction.value === 'erase'
  ) {

    selectedHands.value =
      selectedHands.value.filter(
        item => item.hand !== hand
      )

    return
  }

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

    // LIMIT

    if (
      existing.raise_percentage
      + existing.call_percentage
      > 100
    ) {
      existing.call_percentage =
        100 - existing.raise_percentage
    }

    existing.fold_percentage =
      100
      - existing.raise_percentage
      - existing.call_percentage
  }

  if (currentAction.value === 'call') {

    existing.call_percentage =
      currentPercentage.value

    // LIMIT

    if (
      existing.raise_percentage
      + existing.call_percentage
      > 100
    ) {
      existing.raise_percentage =
        100 - existing.call_percentage
    }

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

const handleCopyRange = async (
  rangeId: number
) => {

  try {

    await copyRange(rangeId)

    await loadGroups()

    await loadRanges()

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

  <div class="min-h-screen bg-zinc-900 text-white p-8 overflow-x-hidden">

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

    <!-- MAIN -->
    <div
      class="
        grid
        grid-cols-1
        lg:grid-cols-[420px_1fr]
        gap-10
        items-start
      "
    >

      <!-- LEFT PANEL -->
      <div class="space-y-6 relative z-10">

        <!-- CREATE GROUP -->
        <div
          class="
            bg-zinc-800
            p-4
            rounded-xl
          "
        >

          <div class="flex gap-3">

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
                hover:bg-purple-500
                px-3
                py-1.5
                rounded-lg
                text-sm
                transition
              "
            >
              Create
            </button>

          </div>

        </div>

        <!-- CREATE RANGE -->
        <div
          class="
            bg-zinc-800
            p-4
            rounded-xl
          "
        >

          <div class="flex gap-3">

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
                hover:bg-green-500
                px-3
                py-1.5
                rounded-lg
                text-sm
                transition
              "
            >
              Create
            </button>

          </div>

        </div>

        <!-- GROUPS -->
        <div class="space-y-4">

          <div
            v-for="group in groups"
            :key="group.id"
            class="
              rounded-xl
              border
              border-zinc-700
              bg-zinc-800
              relative
              overflow-visible
            "
          >

            <!-- GROUP HEADER -->
            <div
              @click="toggleGroup(group.id)"
              class="
                flex
                items-center
                justify-between
                px-4
                py-3
                cursor-pointer
                transition
              "
              :class="
                currentGroupId === group.id
                  ? 'bg-zinc-700'
                  : 'bg-zinc-800'
              "
            >

              <div
                class="
                  flex
                  items-center
                  gap-3
                "
              >

                <div
                  class="
                    text-sm
                    text-zinc-400
                  "
                >
                  {{
                    openedGroups.includes(group.id)
                      ? '−'
                      : '+'
                  }}
                </div>

                <div
                  class="
                    text-xl
                    font-bold
                  "
                >
                  {{ group.name }}
                </div>

              </div>

              <!-- GROUP MENU -->
              <div class="relative">

                <button
                  @click.stop="
                    toggleGroupMenu(group.id)
                  "
                  class="
                    w-8
                    h-8
                    rounded-lg
                    hover:bg-zinc-600
                    transition
                  "
                >
                  ⋮
                </button>

                <div
                  v-if="
                    openedGroupMenuId === group.id
                  "
                  class="
                    absolute
                    right-0
                    top-10
                    w-40
                    bg-zinc-800
                    border
                    border-zinc-700
                    rounded-xl
                    shadow-2xl
                    z-[9999]
                    overflow-hidden
                  "
                >

                  <button
                    class="
                      w-full
                      text-left
                      px-4
                      py-2
                      hover:bg-zinc-700
                    "
                  >
                    Rename
                  </button>

                  <button
                    class="
                      w-full
                      text-left
                      px-4
                      py-2
                      hover:bg-zinc-700
                    "
                  >
                    Save
                  </button>

                  <button
                    class="
                      w-full
                      text-left
                      px-4
                      py-2
                      hover:bg-red-700
                    "
                  >
                    Delete
                  </button>

                </div>

              </div>

            </div>

            <!-- GROUP CONTENT -->
            <div
              v-if="
                openedGroups.includes(group.id)
              "
              class="
                bg-zinc-900
                p-3
                space-y-2
              "
            >

              <!-- RANGES -->
              <div
                v-for="range in group.ranges"
                :key="range.id"
                @click.stop="loadRange(range)"
                class="
                  bg-zinc-800
                  hover:bg-zinc-700
                  transition
                  p-3
                  rounded-xl
                  flex
                  items-center
                  justify-between
                  cursor-pointer
                "
              >

                <div class="font-semibold">
                  {{ range.name }}
                </div>

                <!-- RANGE MENU -->
                <div class="relative">

                  <button
                    @click.stop="
                      toggleRangeMenu(range.id)
                    "
                    class="
                      w-8
                      h-8
                      rounded-lg
                      hover:bg-zinc-600
                    "
                  >
                    ⋮
                  </button>

                  <div
                    v-if="
                      openedRangeMenuId === range.id
                    "
                    class="
                      absolute
                      right-0
                      top-10
                      w-40
                      bg-zinc-800
                      border
                      border-zinc-700
                      rounded-xl
                      shadow-2xl
                      z-[9999]
                      overflow-hidden
                    "
                  >

                    <button
                      @click.stop="
                        handleSaveRange
                      "
                      class="
                        w-full
                        text-left
                        px-4
                        py-2
                        hover:bg-zinc-700
                      "
                    >
                      Save
                    </button>

                    <button
                      class="
                        w-full
                        text-left
                        px-4
                        py-2
                        hover:bg-zinc-700
                      "
                    >
                      Rename
                    </button>

                    <button
                      @click.stop="
                        handleCopyRange(range.id)
                      "
                      class="
                        w-full
                        text-left
                        px-4
                        py-2
                        hover:bg-zinc-700
                      "
                    >
                      Copy
                    </button>

                    <button
                      @click.stop="
                        handleDeleteRange(range.id)
                      "
                      class="
                        w-full
                        text-left
                        px-4
                        py-2
                        hover:bg-red-700
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
      <div
        class="
          flex
          flex-col
          items-start
          pl-4
        "
      >

        <!-- ACTIONS -->
        <div
          class="
            flex
            flex-wrap
            gap-4
            items-center
            mb-6
          "
        >

          <button
            @click="currentAction = 'raise'"
            class="
              px-4
              py-2
              rounded-xl
            "
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
            class="
              px-4
              py-2
              rounded-xl
            "
            :class="
              currentAction === 'call'
                ? 'bg-green-600'
                : 'bg-zinc-700'
            "
          >
            Call
          </button>

          <button
            @click="currentAction = 'erase'"
            class="
              px-4
              py-2
              rounded-xl
            "
            :class="
              currentAction === 'erase'
                ? 'bg-zinc-500'
                : 'bg-zinc-700'
            "
          >
            Erase
          </button>

          <!-- PERCENT -->
          <div
            class="
              flex
              items-center
              gap-4
              min-w-[260px]
            "
          >

            <div class="w-16">
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

        <!-- GRID -->
        <div
          class="
            bg-zinc-900
            rounded-2xl
            p-2
          "
        >

          <RangeGrid
            :selectedHands="selectedHands"
            @toggle-hand="toggleHand"
          />

        </div>

      </div>

    </div>

  </div>

</template>