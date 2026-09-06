<template>
  <div class="min-h-screen bg-slate-50 text-slate-800 flex flex-col font-sans selection:bg-indigo-500 selection:text-white">
    <template v-if="authStore.isAuthenticated">
      <!-- Navbar Top Header -->
      <AppNavbar />

      <!-- Main Layout Body with Sidebar -->
      <div class="flex-1 flex overflow-hidden">
        <!-- Sidebar Navigation -->
        <AppSidebar />

        <!-- Main Workspace View -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto max-w-7xl mx-auto w-full">
          <RouterView />
        </main>
      </div>
    </template>

    <template v-else>
      <!-- Login View Full Screen -->
      <RouterView />
    </template>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppNavbar from '@/components/layout/AppNavbar.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'

const authStore = useAuthStore()

onMounted(() => {
  if (authStore.isAuthenticated) {
    authStore.fetchUser()
  }
})
</script>
