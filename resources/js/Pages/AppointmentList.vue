<script setup>
import { ref, watch } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
  appointments: Object,
})

const processing = ref(false)
const search = ref('')
const todayOnly = ref(false)

const markDone = (id) => {
  if (processing.value) return
  if (confirm('Mark this appointment as done?')) {
    processing.value = true
    router.put(`/appointments/${id}/complete`, {}, {
      onFinish: () => {
        processing.value = false
        router.reload({ only: ['appointments'] })
      }
    })
  }
}

const cancelAppointment = (id) => {
  if (processing.value) return
  if (confirm('Are you sure you want to cancel this appointment permanently?')) {
    processing.value = true
    router.delete(`/appointments/${id}`, {
      onFinish: () => {
        processing.value = false
        router.reload({ only: ['appointments'] })
      }
    })
  }
}

// Re-fetch when filter/search changes
watch([search, todayOnly], () => {
  router.get('/appointments', {
    search: search.value,
    today: todayOnly.value,
  }, {
    preserveState: true,
    replace: true,
  })
})

// Format date and time
const formatDate = (dateStr) => {
  const [y, m, d] = dateStr.split('-')
  return `${d}-${m}-${y}`
}

const formatTime = (timeStr) => {
  const [h, m] = timeStr.split(':')
  const hr = parseInt(h)
  const period = hr >= 12 ? 'PM' : 'AM'
  const displayHour = hr % 12 === 0 ? 12 : hr % 12
  return `${displayHour}:${m} ${period}`
}
</script>

<template>
  <div class="p-4 sm:p-6">
    <h1 class="text-2xl font-bold mb-4 text-blue-700">📋 Pending Appointments</h1>

    <!-- Filters + Add Button -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between bg-white shadow-sm p-4 rounded mb-6">
      <!-- Filters -->
      <div class="flex flex-col sm:flex-row gap-4 items-center">
        <input
          v-model="search"
          type="text"
          placeholder="Search name or phone..."
          class="border border-gray-300 rounded px-3 py-2 w-full sm:w-64 focus:outline-none focus:ring focus:ring-blue-300"
        />
        <label class="inline-flex items-center text-sm gap-2">
          <input type="checkbox" v-model="todayOnly" class="accent-blue-600" />
          <span>Today's Appointments</span>
        </label>
      </div>

      <!-- Add Button -->
      <Link
        href="/appointments/create"
        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-700 border border-blue-600 text-sm font-medium rounded hover:bg-blue-600 hover:text-white transition"
      >
        ➕ New Appointment
      </Link>
    </div>

    <!-- Appointment Table -->
    <div class="overflow-x-auto bg-white shadow rounded-xl">
      <table class="min-w-full text-left border">
        <thead class="bg-blue-100 text-sm">
          <tr>
            <th class="p-3 border text-center">Name</th>
            <th class="p-3 border text-center">Phone</th>
            <th class="p-3 border text-center">Date</th>
            <th class="p-3 border text-center">Time</th>
            <th class="p-3 border text-center">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="a in appointments.data" :key="a.id" class="border-b hover:bg-gray-50">
            <td class="p-3 text-center">{{ a.name }}</td>
            <td class="p-3 text-center">{{ a.phone }}</td>
            <td class="p-3 text-center">{{ formatDate(a.appointment_date) }}</td>
            <td class="p-3 text-center">{{ formatTime(a.appointment_time) }}</td>
            <td class="p-3 text-center space-x-2">
              <button
                @click="markDone(a.id)"
                :disabled="processing"
                class="px-3 py-1 text-sm bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50"
              >
                ✅ Done
              </button>
              <button
                @click="cancelAppointment(a.id)"
                :disabled="processing"
                class="px-3 py-1 text-sm bg-red-100 border border-red-700 text-red-700 rounded hover:bg-red-700 hover:text-white transition disabled:opacity-50"
              >
                ❌ Cancel
              </button>
            </td>
          </tr>
          <tr v-if="appointments.data.length === 0">
            <td colspan="5" class="text-center py-4 text-gray-500">No appointments found.</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-center flex-wrap gap-2">
      <button
        v-for="(link, index) in appointments.links"
        :key="index"
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
