<template>
  <div>
    <div>
      <nav class="sm:hidden">
        <inertia-link :href="route('orders')" class="flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Back
        </inertia-link>
      </nav>
      <nav class="hidden sm:flex items-center text-sm leading-5 font-medium">
        <inertia-link :href="route('orders')" class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          Orders
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
              Order information
            </h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Select whether the order is takeout or select the appropriate table.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-6 sm:col-span-1">
                <Checkbox :label="form.isDineIn ? 'Dine in' : 'Takeout'" :status="form.isDineIn" @toggle="form.isDineIn = !form.isDineIn" />
              </div>

              <div v-show="form.isDineIn" class="col-span-6 sm:col-span-5">
                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <SelectInput v-show="form.isDineIn" v-model="form.restaurant_table_id" :errors="$page.errors.restaurant_table_id" label="Table" :required="true">
                    <option value="select">Select a table</option>
                    <option v-for="(table, index) in restaurant_tables" :value="table.id" :key="index">
                      Table #{{ table.table_number }}
                    </option>
                  </SelectInput>
                </transition>
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
import SelectInput from '@/Shared/SelectInput'
import Checkbox from '@/Shared/Checkbox'

export default {
  metaInfo: { title: 'Create order' },
  layout: Layout,
  components: {
    SelectInput,
    Checkbox
  },
  props: {
    restaurant_tables: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        restaurant_table_id: 'select',
        isDineIn: true
      },
    }
  },
  methods: {
    submit() {
      this.sending = true

      const data = new FormData()

      data.append('restaurant_table_id', this.form.restaurant_table_id != 'select' ? this.form.restaurant_table_id : '')
      data.append('is_dine_in', this.form.isDineIn ? 1 : 0)

      this.$inertia.post(this.route('orders.store'), data)
        .then(() => this.sending = false)
    },
  },
}
</script>
