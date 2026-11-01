<template>
  <div class="min-h-screen bg-[#FAF8F5] text-stone-800 flex flex-col font-sans">
    <template v-if="authStore.isAuthenticated && !route.meta.isPublic">
      <!-- Top Header -->
      <AppNavbar @toggle-sidebar="mobileSidebarOpen = !mobileSidebarOpen" />

      <!-- Main Workspace Layout -->
      <div class="flex-1 flex max-w-[1600px] w-full mx-auto relative">
        <!-- Sidebar Navigation -->
        <AppSidebar :mobile-open="mobileSidebarOpen" @close="mobileSidebarOpen = false" />

        <!-- Content Area with Smooth In/Out Route Transition -->
        <main class="flex-1 min-w-0 px-3.5 py-4 sm:px-6 sm:py-6 lg:px-8 lg:py-7 pb-24 lg:pb-10">
          <RouterView v-slot="{ Component, route: currentRoute }">
            <Transition name="page-fade" mode="out-in">
              <component :is="Component" :key="currentRoute.path" />
            </Transition>
          </RouterView>
        </main>
      </div>

      <!-- Mobile & Tablet Bottom Quick Navigation Bar (< lg) -->
      <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white/95 backdrop-blur-xl border-t border-stone-200/90 px-2 py-1.5 shadow-[0_-4px_20px_rgba(28,25,23,0.05)]">
        <div class="grid grid-cols-5 gap-1 max-w-md mx-auto">
          <RouterLink
            to="/dashboard"
            class="cursor-pointer flex flex-col items-center justify-center py-1.5 rounded-xl text-[10px] font-bold transition-all"
            :class="route.path === '/dashboard' ? 'text-teal-700 bg-teal-50/70' : 'text-stone-500 hover:text-stone-900'"
          >
            <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Beranda</span>
          </RouterLink>

          <RouterLink
            to="/pickup"
            class="cursor-pointer flex flex-col items-center justify-center py-1.5 rounded-xl text-[10px] font-bold transition-all"
            :class="route.path === '/pickup' ? 'text-teal-700 bg-teal-50/70' : 'text-stone-500 hover:text-stone-900'"
          >
            <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Scan Pagi</span>
          </RouterLink>

          <RouterLink
            to="/return"
            class="cursor-pointer flex flex-col items-center justify-center py-1.5 rounded-xl text-[10px] font-bold transition-all"
            :class="route.path === '/return' ? 'text-emerald-700 bg-emerald-50/70' : 'text-stone-500 hover:text-stone-900'"
          >
            <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Scan Siang</span>
          </RouterLink>

          <RouterLink
            to="/laptops"
            class="cursor-pointer flex flex-col items-center justify-center py-1.5 rounded-xl text-[10px] font-bold transition-all"
            :class="route.path === '/laptops' ? 'text-teal-700 bg-teal-50/70' : 'text-stone-500 hover:text-stone-900'"
          >
            <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Laptop</span>
          </RouterLink>

          <button
            type="button"
            @click="mobileSidebarOpen = true"
            class="cursor-pointer flex flex-col items-center justify-center py-1.5 rounded-xl text-[10px] font-bold text-stone-500 hover:text-stone-900 transition-all"
          >
            <svg class="w-4 h-4 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span>Menu</span>
          </button>
        </div>
      </nav>
    </template>

    <template v-else>
      <!-- Standalone Public / Login View with Smooth In/Out Transition -->
      <RouterView v-slot="{ Component, route: currentRoute }">
        <Transition name="page-fade" mode="out-in">
          <component :is="Component" :key="currentRoute.path" />
        </Transition>
      </RouterView>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { RouterView, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import AppNavbar from '@/components/layout/AppNavbar.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'

const authStore = useAuthStore()
const route = useRoute()
const mobileSidebarOpen = ref(false)

watch(
  () => route.fullPath,
  () => {
    mobileSidebarOpen.value = false
  }
)

onMounted(() => {
  if (authStore.isAuthenticated) {
    authStore.fetchUser()
  }
})
</script>
