<template>
  <aside class="w-64 bg-white border-r border-slate-200 shrink-0 hidden md:block min-h-[calc(100vh-65px)] shadow-xs">
    <div class="p-4 space-y-6">
      <!-- Session Actions -->
      <div>
        <h3 class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Operasional Scanner</h3>
        <nav class="space-y-1">
          <RouterLink
            to="/pickup"
            class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/pickup') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-5 h-5 mr-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Scan Pengambilan (Pagi)
          </RouterLink>

          <RouterLink
            to="/return"
            class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/return') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-5 h-5 mr-3 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Scan Pengembalian (Siang)
          </RouterLink>
        </nav>
      </div>

      <!-- Main Navigation -->
      <div>
        <h3 class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Monitoring</h3>
        <nav class="space-y-1">
          <RouterLink
            to="/dashboard"
            class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/dashboard') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-5 h-5 mr-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            Dashboard Utama
          </RouterLink>

          <RouterLink
            to="/monitoring"
            class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/monitoring') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-5 h-5 mr-3 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            Monitoring Real-time
          </RouterLink>

          <RouterLink
            to="/notifications"
            class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/notifications') ? 'bg-amber-50 text-amber-700 border border-amber-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-5 h-5 mr-3 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            Log WhatsApp WA
          </RouterLink>
        </nav>
      </div>

      <!-- Master Data -->
      <div>
        <h3 class="px-3 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Master Data</h3>
        <nav class="space-y-1">
          <RouterLink
            to="/students"
            class="flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/students') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Data Siswa & QR
          </RouterLink>

          <RouterLink
            to="/classes"
            class="flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/classes') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Data Kelas
          </RouterLink>

          <RouterLink
            to="/parents"
            class="flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/parents') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
            </svg>
            Data Wali Murid
          </RouterLink>

          <RouterLink
            to="/laptops"
            class="flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/laptops') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Aset Laptop
          </RouterLink>

          <RouterLink
            to="/schedules"
            class="flex items-center px-3 py-2 rounded-xl text-sm font-medium transition-all"
            :class="isActive('/schedules') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/80 font-semibold shadow-xs' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
          >
            <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            Sesi Jadwal
          </RouterLink>
        </nav>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { useRoute } from 'vue-router'

const route = useRoute()
const isActive = (path) => route.path === path
</script>
