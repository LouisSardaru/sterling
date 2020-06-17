<template>
  <div>
    <div>
      <nav class="sm:hidden">
        <inertia-link :href="route('restaurant_tables')" class="flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Back
        </inertia-link>
      </nav>
      <nav class="hidden sm:flex items-center text-sm leading-5 font-medium">
        <inertia-link :href="route('restaurant_tables')" class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          Tables
        </inertia-link>
        <svg class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-700 transition duration-150 ease-in-out">
          Create
        </span>
      </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          Create
        </h2>
      </div>
    </div>

    <form @submit.prevent="submit">
      <div class="my-4 bg-white shadow px-4 py-5 rounded sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Table information
            </h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Make sure to fill in the table number. A waiter responsible for this table can be chosen later.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.table_number" :errors="$page.errors.table_number" label="Number" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <SelectInput v-model="form.user_id" :errors="$page.errors.user_id" label="Waiter" :optional="true">
                  <option value="select">Select a waiter</option>
                  <option v-for="(user, index) in users" :value="user.id" :key="index">
                    {{ user.first_name }} {{ user.last_name }}
                  </option>
                </SelectInput>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-end">
        <span class="ml-3 inline-flex rounded-md shadow-sm">
          <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
            Save
          </button>
        </span>
      </div>
    </form>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'

export default {
  metaInfo: { title: 'Create tables' },
  layout: Layout,
  components: {
    TextInput,
    SelectInput
  },
  props: {
    users: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        user_id: 'select',
        table_number: null
      },
    }
  },
  methods: {
    submit() {
      this.sending = true

      const data = new FormData()
      data.append('user_id', this.form.user_id !== 'select' ? this.form.user_id : '')
      data.append('table_number', this.form.table_number || '')

      this.$inertia.post(this.route('restaurant_tables.store'), data)
        .then(() => this.sending = false)
    },
  },
}
</script>
