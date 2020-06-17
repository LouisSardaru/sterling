<template>
  <div>
    <div>
      <nav class="sm:hidden">
        <inertia-link :href="route('ingredients')" class="flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Back
        </inertia-link>
      </nav>
      <nav class="hidden sm:flex items-center text-sm leading-5 font-medium">
        <inertia-link :href="route('ingredients')" class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          Ingredients
        </inertia-link>
        <svg class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-700 transition duration-150 ease-in-out">
          {{ ingredient.name }}
        </span>
      </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          {{ ingredient.name }}
        </h2>
      </div>
    </div>

    <TrashedMessage v-if="can.restore && ingredient.deleted_at" class="mt-4" @restore="restoreModalState = true">
      This ingredient has been deleted.
    </TrashedMessage>

    <form @submit.prevent="submit">
      <div class="my-4 bg-white shadow px-4 py-5 rounded sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Ingredient information
            </h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Make sure to update the quantity when your ingredients expire.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-6">
                <TextInput v-model="form.name" :errors="$page.errors.name" label="Name" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.quantity" :errors="$page.errors.quantity" label="Quantity" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.emergency_quantity" :errors="$page.errors.emergency_quantity" label="Emergency quantity" :required="true" />
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-end">
        <span v-if="can.delete && !ingredient.deleted_at" class="inline-flex rounded-md shadow-sm">
          <button @click="deleteModalState = true" type="button" class="py-2 px-4 border border-red-300 rounded-md text-sm leading-5 font-medium text-red-700 hover:text-red-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-red-50 active:text-red-800 transition duration-150 ease-in-out">
            Delete
          </button>
        </span>
        <span class="ml-3 inline-flex rounded-md shadow-sm">
          <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
            Save
          </button>
        </span>
      </div>
    </form>

    <Modal :state="deleteModalState" @close="deleteModalState = false" @confirm="destroy()" type="danger">
      <h1 slot="header"> 
        Delete ingredient
      </h1>
      
      <p>
        Are you sure you want to delete <span class="font-medium">{{ ingredient.name }}</span>?
        The ingredient can still be restored afterwards if you change your mind.
      </p>

      <span slot="button">
        Delete
      </span>
    </Modal>

    <Modal :state="restoreModalState" @close="restoreModalState = false" @confirm="restore()" type="success">
      <h1 slot="header"> 
        Restore ingredient
      </h1>
      
      <p>
        Are you sure you want to restore <span class="font-medium">{{ ingredient.name }}</span>?
      </p>

      <span slot="button">
        Restore
      </span>
    </Modal>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import Modal from '@/Shared/Modal'
import Checkbox from '@/Shared/Checkbox'

export default {
  metaInfo() {
    return {
      title: this.ingredient.name,
    }
  },
  layout: Layout,
  components: {
    TextInput,
    TrashedMessage,
    Modal,
    Checkbox,
  },
  props: {
    can: Object,
    ingredient: Object,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.ingredient.name,
        quantity: this.ingredient.quantity,
        emergency_quantity: this.ingredient.emergency_quantity,
      },
      deleteModalState: false,
      restoreModalState: false
    }
  },
  methods: {
    submit() {
      this.sending = true

      const data = new FormData()
      data.append('name', this.form.name || '')
      data.append('quantity', this.form.quantity || '')
      data.append('emergency_quantity', this.form.emergency_quantity || '')
      data.append('_method', 'put')

      this.$inertia.post(this.route('ingredients.update', this.ingredient.id), data)
        .then(() => {
          this.sending = false
        })
    },
    destroy() {
      this.deleteModalState = false
      this.$inertia.delete(this.route('ingredients.destroy', this.ingredient.id))
    },
    restore() {
      this.restoreModalState = false
      this.$inertia.put(this.route('ingredients.restore', this.ingredient.id))
    }
  },
}
</script>
