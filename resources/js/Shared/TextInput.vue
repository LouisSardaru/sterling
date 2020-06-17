<template>
  <div>
    <div class="flex justify-between">
      <label v-if="label" :for="id" :class="{ 'sr-only' : hiddenLabel }" class="block text-sm font-medium leading-5 text-gray-700">
        {{ label }}
      </label>
      <span v-if="optional" class="text-sm leading-5 text-gray-500" id="input-optional">
        Optional
      </span>
    </div>
    <div :class="hiddenLabel ? 'mt-0' : 'mt-1'" class="relative rounded-md shadow-sm">
      <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <slot name="icon"></slot>
      </div>
      <input :id="id" :class="{ 'pr-10 border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red' : errors.length, 'pl-10' : $slots.icon, 'text-center' : centered }" class="form-input block w-full sm:text-sm sm:leading-5 placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150" @input="$emit('input', $event.target.value)" :type="type" :disabled="disabled" :value="value" :placeholder="placeholder" :aria-invalid="errors.length ? true : false" :aria-describedby="`${label} text input`" />
      <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
        <svg v-if="errors.length" class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
    </div>
    <p v-if="!errors.length && help" class="mt-2 text-sm text-gray-500" id="input-description">
      {{ help }}
    </p>
    <p v-if="errors.length" class="mt-2 text-sm text-red-600" id="input-error">
      {{ errors[0] }}
    </p>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    placeholder: String,
    value: [String, Number],
    label: String,
    hiddenLabel: {
      type: Boolean,
      default: false
    },
    errors: {
      type: Array,
      default: () => [],
    },
    required: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    optional: {
      type: Boolean,
      default: false
    },
    centered: {
      type: Boolean,
      default: false
    },
    help: String
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end)
    },
  }
}
</script>
