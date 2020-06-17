<template>
  <div>
    <label v-if="label" :for="id" class="block text-sm font-medium leading-5 text-gray-700">
      {{ label }}
    </label>
    <div class="mt-1">
      <div class="rounded-md shadow-sm">
        <select :id="id" ref="input" v-model="selected" v-bind="$attrs" :class="{ 'border-red-300 text-red-900 placeholder-red-300 focus:border-red-300 focus:shadow-outline-red' : errors.length }" class="capitalize block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
          <slot></slot>
        </select>
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
        return `select-input-${this._uid}`
      },
    },
    value: [String, Number, Boolean],
    label: String,
    help: String,
    errors: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      selected: this.value,
    }
  },
  watch: {
    selected(selected) {
      this.$emit('input', selected)
    },
  },
  methods: {
    focus() {
      this.$refs.input.focus()
    },
    select() {
      this.$refs.input.select()
    },
  },
}
</script>
