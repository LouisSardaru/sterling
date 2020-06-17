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
          Table #{{ restaurant_table.table_number }}
        </span>
      </nav>
    </div>
    <div class="mt-2 flex items-center justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          Table #{{ restaurant_table.table_number }}
        </h2>
      </div>
      <div class="flex flex-row items-center pl-2">
        <inertia-link v-if="can.edit" v-tooltip.top="'Edit'" :href="route('restaurant_tables.edit', restaurant_table.id)" class="mr-2 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
          <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
        </inertia-link>
        <button v-tooltip.top="'Delete'" v-if="can.delete && !restaurant_table.deleted_at" @click="deleteModalState = true" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
          <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
        </button>
        <button v-tooltip.top="'Restore'" v-else-if="can.restore && restaurant_table.deleted_at" @click="restoreModalState = true" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
          <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
            <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
        </button>
      </div>
    </div>

    <div class="my-4 bg-white shadow overflow-hidden rounded sm:rounded-lg">
      <div class="px-4 py-5 border-b border-gray-200 sm:px-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Table information
        </h3>
        <p class="mt-1 max-w-2xl text-sm leading-5 text-gray-500">
          Table number and currently assigned waiter.
        </p>
      </div>
      <div class="px-4 py-5 sm:px-6">
        <dl class="grid grid-cols-1 col-gap-4 row-gap-8 sm:grid-cols-2">
          <div class="sm:col-span-1">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Number
            </dt>
            <dd class="mt-1 text-sm leading-5 text-gray-900">
              Table #{{ restaurant_table.table_number }}
            </dd>
          </div>
          <div class="sm:col-span-1">
            <dt class="text-sm leading-5 font-medium text-gray-500">
              Waiter
            </dt>
            <dd v-if="restaurant_table.user_id" class="mt-1 text-sm leading-5 text-gray-900">
              {{ restaurant_table.user.first_name }} {{ restaurant_table.user.last_name }}
            </dd>
            <dd v-else class="mt-1 text-sm leading-5 text-gray-900">
              N/A
            </dd>
          </div>
        </dl>
      </div>
    </div>

    <Modal :state="deleteModalState" @close="deleteModalState = false" @confirm="destroy()" type="danger">
      <h1 slot="header"> 
        Delete table
      </h1>
      
      <p>
        Are you sure you want to delete <span class="font-medium">Table #{{ restaurant_table.table_number }}</span>?
        The table can still be restored afterwards if you change your mind.
      </p>

      <span slot="button">
        Delete
      </span>
    </Modal>

    <Modal :state="restoreModalState" @close="restoreModalState = false" @confirm="restore()" type="success">
      <h1 slot="header"> 
        Restore table
      </h1>
      
      <p>
        Are you sure you want to restore <span class="font-medium">Table #{{ restaurant_table.table_number }}</span>?
      </p>

      <span slot="button">
        Restore
      </span>
    </Modal>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import Modal from '@/Shared/Modal'

export default {
  metaInfo() {
    return {
      title: `Table #${this.restaurant_table.table_number}`,
    }
  },
  layout: Layout,
  components: {
    Modal,
  },
  props: {
    can: Object,
    restaurant_table: Object,
  },
  data() {
    return {
      deleteModalState: false,
      restoreModalState: false
    }
  },
  methods: {
    destroy() {
      this.deleteModalState = false
      this.$inertia.delete(this.route('restaurant_tables.destroy', this.restaurant_table.id))
    },
    restore() {
      this.restoreModalState = false
      this.$inertia.put(this.route('restaurant_tables.restore', this.restaurant_table.id))
    }
  },
}
</script>
