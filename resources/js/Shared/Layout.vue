<template>
  <div class="h-screen flex overflow-hidden bg-white">
    <div :class="{ 'pointer-events-none' : !isOffCanvasMenuOpen }" class="md:hidden">
      <div class="fixed inset-0 flex z-40">
        <transition
          enter-active-class="transition-opacity ease-linear duration-300"
          enter-class="opacity-0"
          enter-to-class="opacity-100"
          leave-active-class="transition-opacity ease-linear duration-300"
          leave-class="opacity-100"
          leave-to-class="opacity-0"
        >
          <div v-show="isOffCanvasMenuOpen" @click="toggleOffCanvasMenu" class="fixed inset-0">
            <div class="absolute inset-0 bg-gray-600 opacity-75"></div>
          </div>
        </transition>
        <transition
          enter-active-class="transition ease-in-out duration-300 transform"
          enter-class="-translate-x-full"
          enter-to-class="translate-x-0"
          leave-active-class="transition ease-in-out duration-300 transform"
          leave-class="translate-x-0"
          leave-to-class="-translate-x-full"
        >
          <div v-show="isOffCanvasMenuOpen" class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
            <div class="absolute top-0 right-0 -mr-14 p-1">
              <button @click="toggleOffCanvasMenu" class="flex items-center justify-center h-12 w-12 rounded-full focus:outline-none focus:bg-gray-600" aria-label="Close sidebar">
                <svg class="h-6 w-6 text-white" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
              <div class="flex-shrink-0 flex items-center px-4">
                <Logo />
              </div>
              <nav class="mt-5 px-2">
                <MainMenuMobile @close="toggleOffCanvasMenu" :url="url()" />
              </nav>
            </div>
            <div class="flex-shrink-0 flex justify-between border-t border-gray-200 p-4">
              <inertia-link :href="route('users.view', $page.auth.user.id)" class="flex-shrink-0 group block focus:outline-none">
                <div class="flex items-center">
                  <div>
                    <p class="text-base leading-6 font-medium text-gray-700 group-hover:text-gray-900">
                      {{ $page.auth.user.first_name }} {{ $page.auth.user.last_name }}
                    </p>
                    <p class="text-sm leading-5 font-medium text-gray-500 group-hover:text-gray-700 group-focus:underline transition ease-in-out duration-150">
                      View profile
                    </p>
                  </div>
                </div>
              </inertia-link>
              <inertia-link :href="route('logout')" method="post" class="flex flex-shrink-0 group block">
                <div class="flex items-center">
                  <div class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                      <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                  </div>
                </div>
              </inertia-link>
            </div>
          </div>
        </transition>
        <div class="flex-shrink-0 w-14">
        </div>
      </div>
    </div>

    <div class="hidden md:flex md:flex-shrink-0">
      <div class="flex flex-col w-64 border-r border-gray-200 bg-white">
        <div class="h-0 flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
          <div class="flex items-center flex-shrink-0 px-4">
            <inertia-link :href="route('dashboard')">
              <Logo />
            </inertia-link>
          </div>
          <nav class="mt-5 flex-1 px-2 bg-white">
            <MainMenu :url="url()" />
          </nav>
        </div>
        <div class="flex-shrink-0 flex justify-between border-t border-gray-200 p-4">
          <inertia-link :href="route('users.view', $page.auth.user.id)" class="flex-shrink-0 group block">
            <div class="flex items-center">
              <div>
                <p class="text-sm leading-5 font-medium text-gray-700 group-hover:text-gray-900">
                  {{ $page.auth.user.first_name }} {{ $page.auth.user.last_name }}
                </p>
                <p class="text-xs leading-4 font-medium text-gray-500 group-hover:text-gray-700 transition ease-in-out duration-150">
                  View profile
                </p>
              </div>
            </div>
          </inertia-link>
          <inertia-link :href="route('logout')" method="post" class="flex flex-shrink-0 group block">
            <div class="flex items-center">
              <div class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                  <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                </svg>
              </div>
            </div>
          </inertia-link>
        </div>
      </div>
    </div>
    <div class="flex flex-col w-0 flex-1 overflow-hidden">
      <div class="md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3">
        <button @click="toggleOffCanvasMenu" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:bg-gray-200 transition ease-in-out duration-150" aria-label="Open sidebar">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
      <main class="flex-1 relative z-0 overflow-y-auto pt-2 pb-6 focus:outline-none md:py-6" tabindex="0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 relative z-50">
          <flash-messages class="z-50" />
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8 z-0">
          <slot />
        </div>
      </main>
    </div>
  </div>
</template>

<script>
import Dropdown from '@/Shared/Dropdown'
import FlashMessages from '@/Shared/FlashMessages'
import Logo from '@/Shared/Logo'
import MainMenu from '@/Shared/MainMenu'
import MainMenuMobile from '@/Shared/MainMenuMobile'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Logo,
    MainMenu,
    MainMenuMobile,
  },
  data() {
    return {
      showUserMenu: false,
      isOffCanvasMenuOpen: false
    }
  },
  methods: {
    hideDropdownMenus() {
      this.showUserMenu = false
    },
    url() {
      return location.pathname.substr(1)
    },
    toggleOffCanvasMenu () {
      this.isOffCanvasMenuOpen = !this.isOffCanvasMenuOpen
    }
  },
}
</script>
