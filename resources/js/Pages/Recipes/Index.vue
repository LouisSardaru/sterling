<template>
  <div>
    <div class="flex items-center justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          Recipes
        </h2>
      </div>
      <div class="flex">
        <span class="shadow-sm rounded-md">
          <inertia-link :href="route('recipes.create')" type="button" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:shadow-outline-indigo focus:border-indigo-700 active:bg-indigo-700 transition duration-150 ease-in-out">
            Create
          </inertia-link>
        </span>
      </div>
    </div>

    <div class="flex items-center justify-between mt-4">
      <div class="flex-1 min-w-0 max-w-lg">
        <TextInput v-model="form.search" label="Search recipes" :hidden-label="true" placeholder="Search recipes">
          <svg slot="icon" class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
          </svg>
        </TextInput>
      </div>
      <div class="flex pl-4">
        <Filters @reset="reset()">
          <div>
            <label for="per-page-filter" class="block text-sm font-medium leading-5 text-gray-700">
              Per page
            </label>
            <div class="mt-1">
              <div class="max-w-lg rounded-md shadow-sm sm:max-w-xs">
                <select id="per-page-filter" ref="per-page-filter" v-model="form.pagination" class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                  <option value="10">
                    10
                  </option>
                  <option value="25">
                    25
                  </option>
                  <option value="50">
                    50
                  </option>
                  <option value="100">
                    100
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div class="mt-2">
            <label for="trashed-filter" class="block text-sm font-medium leading-5 text-gray-700">
              Trashed
            </label>
            <div class="mt-1">
              <div class="max-w-lg rounded-md shadow-sm sm:max-w-xs">
                <select id="trashed-filter" ref="trashed-filter" v-model="form.trashed" class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                  <option value="without">
                    Without trashed
                  </option>
                  <option value="with">
                    With trashed
                  </option>
                  <option value="only">
                    Only trashed
                  </option>
                </select>
              </div>
            </div>
          </div>
        </Filters>
      </div>
    </div>

    <div class="flex flex-col py-4">
      <div class="-my-2 py-2 overflow-x-auto sm:-mx-6 sm:px-6 lg:-mx-8 lg:px-8">
        <div class="align-middle inline-block min-w-full shadow overflow-hidden rounded sm:rounded-lg border-b border-gray-200">
          <table class="min-w-full">
            <thead>
              <tr>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Name
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Description
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Price
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="recipe in recipes.data" :key="recipe.id">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img class="h-10 w-10 rounded-full" :src="recipe.thumbnail" alt="" />
                    </div>
                    <div class="ml-4">
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ recipe.name }}
                      </div>
                      <div class="text-sm leading-5 text-gray-500">
                        {{ recipe.dietary_restrictions }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                  {{ recipe.short_description }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                  &dollar; {{ recipe.formatted_price }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <div class="flex flex-row justify-end items-center">
                    <inertia-link v-if="can.view" v-tooltip.top="'View'" :href="route('recipes.view', recipe.id)" class="mr-2 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </inertia-link>
                    <inertia-link v-if="can.edit" v-tooltip.top="'Edit'" :href="route('recipes.edit', recipe.id)" class="mr-2 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </inertia-link>
                    <button v-tooltip.top="'Delete'" v-if="can.delete && !recipe.deleted_at" @click="openDeleteModal(recipe.id, recipe.name)" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                    <button v-tooltip.top="'Restore'" v-else-if="can.restore && recipe.deleted_at" @click="openRestoreModal(recipe.id, recipe.name)" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                      </svg>
                    </button>
                    <div class="text-sm leading-5 text-gray-500" v-if="!can.view && !can.delete && !can.restore && !can.edit">
                      No actions
                    </div>
                  </div>
                </td>
              </tr>
              <tr v-show="!recipes.data.length">
                <td colspan="4">
                  <div class="bg-white py-3 flex items-center justify-between w-full border-b border-gray-200">
                    <div class="flex-1 text-center py-4 px-4 sm:px-6 whitespace-no-wrap text-sm leading-5 text-gray-500">
                      No recipes found
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="4">
                  <nav class="bg-white px-4 py-3 flex items-center justify-between sm:px-6 w-full">
                    <div class="hidden sm:block">
                      <p class="text-sm leading-5 text-gray-700">
                        Showing
                        <span class="font-medium">
                          {{ lowerLimit }}
                        </span>
                        to
                        <span class="font-medium">
                          {{ upperLimit }}
                        </span>
                        of
                        <span class="font-medium">
                          {{ recipes.total }}
                        </span>
                        results
                      </p>
                    </div>
                    <div class="flex-1 flex justify-between sm:justify-end">
                      <inertia-link preserve-scroll :href="previousLink.url" :class="previousLink.url ? 'text-gray-700 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150' : ' text-gray-300 cursor-default'" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md bg-white">
                        Previous
                      </inertia-link>
                      <inertia-link preserve-scroll :href="nextLink.url" :class="nextLink.url ? 'text-gray-700 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150' : ' text-gray-300 cursor-default'" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md bg-white">
                        Next
                      </inertia-link>
                    </div>
                  </nav>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <Modal :state="deleteModalState" @close="deleteModalState = false" @confirm="destroy(selectedRecipeId)" type="danger">
      <h1 slot="header">
        Delete account
      </h1>

      <p>
        Are you sure you want to delete <span class="font-medium">{{ selectedRecipeName }}</span>?
        The recipe can still be restored afterwards if you change your mind.
      </p>

      <span slot="button">
        Delete
      </span>
    </Modal>

    <Modal :state="restoreModalState" @close="restoreModalState = false" @confirm="restore(selectedRecipeId)" type="success">
      <h1 slot="header">
        Restore recipe
      </h1>

      <p>
        Are you sure you want to restore <span class="font-medium">{{ selectedRecipeName }}</span>?
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
import SelectInput from '@/Shared/SelectInput'
import Filters from '@/Shared/Filters'
import Modal from '@/Shared/Modal'

import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'

export default {
  metaInfo: { title: 'Recipes' },
  layout: Layout,
  components: {
    TextInput,
    SelectInput,
    Filters,
    Modal,
  },
  props: {
    can: Object,
    recipes: Object,
    filters: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search || '',
        trashed: this.filters.trashed || 'without',
        pagination: this.filters.pagination || '10'
      },
      selectedRecipeName: '',
      selectedRecipeId: '',
      deleteModalState: false,
      restoreModalState: false,
    }
  },
  computed: {
    previousLink() {
      return this.recipes.links[0]
    },
    nextLink() {
      return this.recipes.links[this.recipes.links.length - 1]
    },
    lowerLimit() {
      if (((this.recipes.currentPage - 1) * this.form.pagination) + 1 > this.recipes.total) {
        return this.recipes.total
      }

      return ((this.recipes.currentPage - 1) * this.form.pagination) + 1
    },
    upperLimit() {
      if (this.recipes.currentPage * this.form.pagination > this.recipes.total) {
        return this.recipes.total
      }

      return this.recipes.currentPage * this.form.pagination
    },
  },
  watch: {
    form: {
      handler: throttle(function() {
        let query = pickBy(this.form)
        this.$inertia.replace(this.route('recipes', Object.keys(query).length ? query : { remember: 'forget' }))
      }, 150),
      deep: true,
    },
  },
  methods: {
    reset() {
      this.form.search = ''
      this.form.status = 'all'
      this.form.trashed = 'without'
      this.form.pagination = '10'
    },
    openDeleteModal(id, name) {
      this.deleteModalState = true
      this.selectedRecipeName = name
      this.selectedRecipeId = id
    },
    openRestoreModal(id, name) {
      this.restoreModalState = true
      this.selectedRecipeId = id
      this.selectedRecipeName = name
    },
    destroy(id) {
      this.deleteModalState = false
      this.$inertia.delete(this.route('recipes.destroy', id))
    },
    restore(id) {
      this.restoreModalState = false
      this.$inertia.put(this.route('recipes.restore', id))
    }
  },
}
</script>
