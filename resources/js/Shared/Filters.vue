<template>
  <div class="relative inline-block">
    <div>
      <button v-tooltip.top="'Filters'" @click="state = !state" class="flex items-center text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition duration-150 ease-in-out" aria-label="Options" id="options-menu" aria-haspopup="true" aria-expanded="true">
        <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
          <path d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
        </svg>
      </button>
    </div>
    <transition
      enter-active-class="transition ease-out duration-100"
      enter-class="transform opacity-0 scale-95"
      enter-to-class="transform opacity-100 scale-100"
      leave-active-class="transition ease-in duration-75"
      leave-class="transform opacity-100 scale-100"
      leave-to-class="transform opacity-0 scale-95"
    >
      <div ref="dropdown" v-show="state" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg">
        <div class="rounded-md bg-white shadow-xs relative z-30">
          <div class="pb-1" role="menu" aria-orientation="vertical" aria-labelledby="filters-menu">
            <button @click="reset()" class="w-full block px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 hover:text-gray-900 active:outline-none active:bg-gray-200 active:text-gray-900 transition duration-150 ease-in-out" role="menuitem">
              Reset
            </button>
            <div class="border-t border-gray-100"></div>
            <div class="px-4 py-2">
              <slot></slot>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  data() {
    return {
      state: false,
    }
  },
  methods: {
    close(e) {
      if (!this.$el.contains(e.target)){
        this.state = false
      } 
    },
    reset() {
      this.$emit('reset')
    }
  },
  created () {
    document.addEventListener('click', this.close)
  },
  beforeDestroy () {
    document.removeEventListener('click',this.close)
  },
}
</script>