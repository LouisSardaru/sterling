<template>
  <div>
    {{$page.error}}
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
          Order #{{ order.id }}
        </span>
      </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          Order #{{ order.id }}
        </h2>
      </div>
    </div>

    <TrashedMessage v-if="can.restore && order.deleted_at" class="mt-4" @restore="restoreModalState = true">
      This order has been deleted.
    </TrashedMessage>

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
              <div class="col-span-6">
                <SelectInput v-model="form.status" :errors="$page.errors.status" label="Status" :required="true">
                  <option value="1">
                    Unassigned
                  </option>
                  <option value="2">
                    Preparing
                  </option>
                  <option value="3">
                    Ready for pickup
                  </option>
                  <option value="4">
                    Delivered
                  </option>
                </SelectInput>
              </div>

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
        <span v-if="can.delete && !order.deleted_at" class="inline-flex rounded-md shadow-sm">
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

    <div class="flex items-center justify-between mt-8">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          Manage order items
        </h2>
      </div>
    </div>

    <div class="flex items-center justify-between mt-4">
      <div class="flex-1 min-w-0 max-w-lg">
        <TextInput v-model="recipeFilters.search" label="Search recipes" :hidden-label="true" placeholder="Search recipes">
          <svg slot="icon" class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
          </svg>
        </TextInput>
      </div>
      <div class="flex pl-4">
        <Filters @reset="reset()">
          <div>
            <label for="recipes-filter" class="block text-sm font-medium leading-5 text-gray-700">
              Recipes
            </label>
            <div class="mt-1">
              <div class="max-w-lg rounded-md shadow-sm sm:max-w-xs">
                <select id="recipes-filter" ref="recipes-filter" v-model="recipeFilters.recipes" class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"> 
                  <option value="all">
                    All
                  </option>
                  <option value="added">
                    Added to order
                  </option>  
                </select>
              </div>
            </div>
          </div>

          <div class="mt-2">
            <label for="per-page-filter" class="block text-sm font-medium leading-5 text-gray-700">
              Per page
            </label>
            <div class="mt-1">
              <div class="max-w-lg rounded-md shadow-sm sm:max-w-xs">
                <select id="per-page-filter" ref="per-page-filter" v-model="recipeFilters.pagination" class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
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
                  Amount
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                  Unit price
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="recipe in recipes.data" :key="recipe.id">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                  <div class="flex items-center">
                    <div>
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
                  {{ quantityUsed(recipe.id) }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                  &dollar; {{ recipe.formatted_price }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <div class="flex flex-row justify-end items-center">
                    <button v-tooltip.top="'Update'" @click="openAdjustRecipeQuantityModal(recipe.id, recipe.name)" v-if="orderRecipeId(recipe.id)" class="mr-2 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                      </svg>
                    </button>
                    <button v-tooltip.top="'Remove'" v-if="orderRecipeId(recipe.id)" @click="deleteRecipe(recipe.id)" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
                    <button v-tooltip.top="'Add'" v-if="!orderRecipeId(recipe.id)" @click="openAddRecipeModal(recipe.id, recipe.name)" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
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
                          {{ recipesLowerLimit }}
                        </span>
                        to
                        <span class="font-medium">
                          {{ recipesUpperLimit }}
                        </span>
                        of
                        <span class="font-medium">
                          {{ recipes.total }}
                        </span>
                        results
                      </p>
                    </div>
                    <div class="flex-1 flex justify-between sm:justify-end">
                      <inertia-link preserve-scroll preserve-state :href="recipesPreviousLink.url" :class="recipesPreviousLink.url ? 'text-gray-700 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150' : ' text-gray-300 cursor-default'" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md bg-white">
                        Previous
                      </inertia-link>
                      <inertia-link preserve-scroll preserve-state :href="recipesNextLink.url" :class="recipesNextLink.url ? 'text-gray-700 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150' : ' text-gray-300 cursor-default'" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md bg-white">
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


    <Modal :state="deleteModalState" @close="deleteModalState = false" @confirm="destroy()" type="danger">
      <h1 slot="header"> 
        Delete order
      </h1>
      
      <p>
        Are you sure you want to delete <span class="font-medium">Order #{{ order.id }}</span>?
        The order can still be restored afterwards if you change your mind.
      </p>

      <span slot="button">
        Delete
      </span>
    </Modal>

    <Modal :state="restoreModalState" @close="restoreModalState = false" @confirm="restore()" type="success">
      <h1 slot="header"> 
        Restore order
      </h1>
      
      <p>
        Are you sure you want to restore <span class="font-medium">Order #{{ order.id }}</span>?
      </p>

      <span slot="button">
        Restore
      </span>
    </Modal>

    <Modal :state="addRecipeModalState" @close="addRecipeModalState = false; recipeQuantity = '1'" @confirm="addRecipe(selectedRecipeId)" type="success">
      <h1 slot="header"> 
        Add recipe
      </h1>
      
      <div>
        Please enter an amount for <span class="font-medium">{{ selectedRecipeName }}</span>.
      </div>
      <TextInput class="mt-2 max-w-xs w-full mx-auto" v-model="recipeQuantity" :errors="$page.errors.quantity" placeholder="Enter quantity" label="Quantity" :centered="true" :hidden-label="true" :required="true" />

      <span slot="button">
        Add Recipe
      </span>
    </Modal>

    <Modal :state="adjustRecipeQuantityModalState" @close="adjustRecipeQuantityModalState = false; recipeQuantity = '1'" @confirm="updateRecipe(selectedRecipeId)" type="success">
      <h1 slot="header"> 
        Adjust recipe quantity
      </h1>
      
      <div>
        Please enter an amount for <span class="font-medium">{{ selectedRecipeName }}</span>.
      </div>
      <TextInput class="mt-2 max-w-xs w-full mx-auto" v-model="recipeQuantity" :errors="$page.errors.quantity" placeholder="Enter quantity" label="Quantity" :centered="true" :hidden-label="true" :required="true" />

      <span slot="button">
        Adjust quantity
      </span>
    </Modal>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import TrashedMessage from '@/Shared/TrashedMessage'
import Modal from '@/Shared/Modal'
import Checkbox from '@/Shared/Checkbox'
import Filters from '@/Shared/Filters'

import mapValues from 'lodash/mapValues'
import pickBy from 'lodash/pickBy'
import throttle from 'lodash/throttle'

export default {
  metaInfo() {
    return {
      title: `Order #${this.order.id}`,
    }
  },
  layout: Layout,
  components: {
    TrashedMessage,
    Modal,
    Checkbox,
    SelectInput,
    TextInput,
    Filters
  },
  props: {
    order: Object,
    restaurant_tables: Array,
    recipes: Object,
    filters:Object,
    added_recipes: Array
  },
  remember: 'form',
  data() {
    return {
      can: Object,
      sending: false,
      form: {
        status: this.order.status,
        restaurant_table_id: this.order.restaurant_table_id || 'select',
        isDineIn: this.order.restaurant_table_id ? true : false
      },
      deleteModalState: false,
      restoreModalState: false,
      selectedRecipeId: null,
      selectedRecipeName: '',
      addRecipeModalState: false,
      adjustRecipeQuantityModalState: false,
      recipeQuantity: 1,
      recipeFilters: {
        search: this.filters.search || '',
        recipes: this.filters.recipes || 'all',
        pagination: this.filters.pagination || '10',
      },
    }
  },
  computed: {
    recipesPreviousLink() {
      return this.recipes.links[0]
    },
    recipesNextLink() {
      return this.recipes.links[this.recipes.links.length - 1]
    },
    recipesLowerLimit() {
      if (((this.recipes.currentPage - 1) * this.recipeFilters.pagination) + 1 > this.recipes.total) {
        return this.recipes.total
      }

      return ((this.recipes.currentPage - 1) * this.recipeFilters.pagination) + 1
    },
    recipesUpperLimit() {
      if (this.recipes.currentPage * this.recipeFilters.pagination > this.recipes.total) {
        return this.recipes.total
      }

      return this.recipes.currentPage * this.recipeFilters.pagination
    },
  },
  methods: {
    submit() {
      this.sending = true

      const data = new FormData()
      data.append('restaurant_table_id', this.form.restaurant_table_id != 'select' ? this.form.restaurant_table_id : '')
      data.append('is_dine_in', this.form.isDineIn ? 1 : 0)
      data.append('status', this.form.status)
      data.append('_method', 'put')

      this.$inertia.post(this.route('orders.update', this.order.id), data)
        .then(() => {
          this.sending = false
        })
    },
    destroy() {
      this.deleteModalState = false
      this.$inertia.delete(this.route('orders.destroy', this.order.id))
    },
    restore() {
      this.restoreModalState = false
      this.$inertia.put(this.route('orders.restore', this.order.id))
    },
    reset() {
      this.recipeFilters.search = ''
      this.recipeFilters.recipes = 'all'
      this.recipeFilters.pagination = '10'
    },
    orderRecipeId(id) {
      const exists = this.added_recipes.filter( recipe => {
        return recipe.recipe_id === id
      })

      if (!exists.length) {
        return false
      }

      return exists[0].id
    },
    quantityUsed(id) {
      const recipes = this.added_recipes.filter( recipe => {
        return recipe.recipe_id === id
      })

      if (!recipes.length) {
        return 'N/A'
      }

      return recipes[0].quantity
    },
    openAddRecipeModal(id, name) {
      this.selectedRecipeId = id
      this.selectedRecipeName = name
      this.addRecipeModalState = true
    },
    openAdjustRecipeQuantityModal(id, name) {
      this.selectedRecipeId = id
      this.selectedRecipeName = name
      this.recipeQuantity = this.quantityUsed(id)
      this.adjustRecipeQuantityModalState = true
    },
    addRecipe(id) {
      const data = new FormData()
      data.append('quantity', this.recipeQuantity || '')
      data.append('_method', 'post')

      this.$inertia.post(this.route('orders.add_recipe', {'order': this.order.id, 'recipe': id }), data)

      this.addRecipeModalState = false
      this.recipeQuantity = '1'
    },
    updateRecipe(id) {
      const data = new FormData()
      data.append('quantity', this.recipeQuantity || '')
      data.append('_method', 'post')

      this.$inertia.post(this.route('orders.update_recipe', this.orderRecipeId(id)), data)

      this.adjustRecipeQuantityModalState = false
      this.recipeQuantity = '1'
    },
    deleteRecipe(id) {
      const data = new FormData()
      data.append('quantity', this.recipeQuantity || '')
      data.append('_method', 'delete')

      this.$inertia.delete(this.route('orders.remove_recipe', this.orderRecipeId(id)), data)
    }
  },
  watch: {
    recipeFilters: {
      handler: throttle(function() {
        let query = pickBy(this.recipeFilters)
        this.$inertia.replace(this.route('orders.edit', this.order.id), {
          data: Object.keys(query).length ? query : { remember: 'forget' }
        })
      }, 150),
      deep: true,
    },
  },
}
</script>
