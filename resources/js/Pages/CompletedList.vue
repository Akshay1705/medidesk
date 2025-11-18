<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  appointments: Object,
  filters: Object,
})

const search = ref(props.filters.search || '')

watch(search, () => {
  router.get('/appointments/completed', { search: search.value }, {
    preserveState: true,
    replace: true,
  })
})

// Formatters
const formatDate = (dateStr) => {
  const [year, month, day] = dateStr.split('-')
  return `${day}-${month}-${year}`
}

const formatTime = (timeStr) => {
  const [hour, minute] = timeStr.split(':')
  const h = parseInt(hour)
  const ampm = h >= 12 ? 'PM' : 'AM'
  const formattedHour = h % 12 === 0 ? 12 : h % 12
  return `${formattedHour}:${minute} ${ampm}`
}
</script>

<template>
  <div class="p-6">
    <!-- Heading & Export -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
      <h1 class="text-2xl font-bold text-gray-800">✅ Completed Appointments</h1>
      <a
        href="/appointments/export"
        download
        class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-md shadow hover:bg-indigo-700 transition"
      >
        ⬇ Export
      </a>
    </div>

    <!-- Search -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        placeholder="Search name or phone..."
        class="border border-gray-300 rounded px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:ring-blue-300"
      />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white shadow rounded-xl">
      <table class="min-w-full text-left border">
        <thead class="bg-blue-100">
          <tr>
            <th class="p-3 border text-center">Name</th>
            <th class="p-3 border text-center">Phone</th>
            <th class="p-3 border text-center">Date</th>
            <th class="p-3 border text-center">Time</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in appointments.data" :key="a.id" class="border-b hover:bg-gray-50">
            <td class="p-3 text-center">{{ a.name }}</td>
            <td class="p-3 text-center">{{ a.phone }}</td>
            <td class="p-3 text-center">{{ formatDate(a.appointment_date) }}</td>
            <td class="p-3 text-center">{{ formatTime(a.appointment_time) }}</td>
          </tr>
          <tr v-if="appointments.data.length === 0">
            <td colspan="4" class="text-center py-4 text-gray-500">
              No completed appointments found.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center flex-wrap gap-2">
      <button
        v-for="(link, i) in appointments.links"
        :key="i"
        v-html="link.label"
        @click="() => link.url && router.get(link.url)"
        :disabled="!link.url"
        class="px-3 py-1 text-sm border rounded"
        :class="[
          link.active ? 'bg-blue-600 text-white font-semibold' : '',
          !link.url ? 'text-gray-400 border-gray-300 cursor-not-allowed' : 'hover:bg-blue-400'
        ]"
      />
    </div>
  </div>
</template>
