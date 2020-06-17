<template>
  <div>
    <div>
      <nav class="sm:hidden">
        <inertia-link :href="route('recipes')" class="flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Back
        </inertia-link>
      </nav>
      <nav class="hidden sm:flex items-center text-sm leading-5 font-medium">
        <inertia-link :href="route('recipes')" class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          Recipes
        </inertia-link>
        <svg class="flex-shrink-0 mx-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
        </svg>
        <span class="text-gray-700 transition duration-150 ease-in-out">
          {{ recipe.name}}
        </span>
      </nav>
    </div>
    <div class="mt-2 md:flex md:items-center md:justify-between">
      <div class="flex-1 min-w-0">
        <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
          {{ recipe.name}}
        </h2>
      </div>
    </div>

    <TrashedMessage v-if="can.restore && recipe.deleted_at" class="mt-4" @restore="restoreModalState = true">
      This recipe has been deleted.
    </TrashedMessage>

    <form @submit.prevent="submit">
      <div class="my-4 bg-white shadow px-4 py-5 rounded sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
          <div class="md:col-span-1">
            <h3 class="text-lg font-medium leading-6 text-gray-900">
              Recipe information
            </h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Only fill in the password field in case you need to change the password.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.name" :errors="$page.errors.name" label="Name" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.description" :errors="$page.errors.description" label="Description" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.dietary_restrictions" :errors="$page.errors.dietary_restrictions" label="Dietary restrictions" :optional="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.calories" :errors="$page.errors.calories" label="Calories" :optional="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.price" :errors="$page.errors.price" label="Price" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <label for="file-input" class="block text-sm font-medium leading-5 text-gray-700">
                  Recipe image
                </label>
                <div class="text-gray-700 mt-1">
                  <label class="cursor-pointer shadow-sm flex flex-row items-center px-3 py-1 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                    <svg class="w-6 h-6 pr-2" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                      <path d="M16.88 9.1A4 4 0 0 1 16 17H5a5 5 0 0 1-1-9.9V7a3 3 0 0 1 4.52-2.59A4.98 4.98 0 0 1 17 8c0 .38-.04.74-.12 1.1zM11 11h3l-4-4-4 4h3v3h2v-3z" />
                    </svg>
                    <span class="text-sm font-medium leading-5 my-1">{{ form.product_image == null ? 'Select an image' : 'Image loaded'}}</span>
                    <input id="file-input" ref="file_input" type='file' class="hidden" @change="onFileChange"/>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-end">
        <span v-if="can.delete && !recipe.deleted_at" class="inline-flex rounded-md shadow-sm">
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
          Manage ingredients
        </h2>
      </div>
    </div>

    <div class="flex items-center justify-between mt-4">
      <div class="flex-1 min-w-0 max-w-lg">
        <TextInput v-model="ingredientFilters.search" label="Search ingredients" :hidden-label="true" placeholder="Search ingredients">
          <svg slot="icon" class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
          </svg>
        </TextInput>
      </div>
      <div class="flex pl-4">
        <Filters @reset="reset()">
          <div class="mt-2">
            <label for="ingredients-filter" class="block text-sm font-medium leading-5 text-gray-700">
              Ingredients
            </label>
            <div class="mt-1">
              <div class="max-w-lg rounded-md shadow-sm sm:max-w-xs">
                <select id="ingredients-filter" ref="ingredients-input" v-model="ingredientFilters.ingredients" class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                  <option value="all">
                    All
                  </option>
                  <option value="added">
                    Added to recipe
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
                <select id="per-page-filter" ref="ingredients-input" v-model="ingredientFilters.pagination" class="block form-select w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">
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
                  Quantity Used
                </th>
                <th class="px-6 py-3 border-b border-gray-200 bg-gray-50"></th>
              </tr>
            </thead>
            <tbody class="bg-white">
              <tr v-for="ingredient in ingredients.data" :key="ingredient.id">
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                  <div class="flex items-center">
                    <div>
                      <div class="text-sm leading-5 font-medium text-gray-900">
                        {{ ingredient.name }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-200 text-sm leading-5 text-gray-500">
                  {{ quantityUsed(ingredient.id) }}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap text-right border-b border-gray-200 text-sm leading-5 font-medium">
                  <div class="flex flex-row justify-end items-center">
                    <button v-tooltip.top="'Update'" @click="openAdjustIngredientQuantityModal(ingredient.id, ingredient.name)" v-if="ingredientRecipeId(ingredient.id)" class="mr-2 text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                      </svg>
                    </button>
                    <button v-tooltip.top="'Delete'" v-if="ingredientRecipeId(ingredient.id)" @click="deleteRecipe(ingredient.id)" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
                    <button v-tooltip.top="'Add'" v-if="!ingredientRecipeId(ingredient.id)" @click="openAddIngredientModal(ingredient.id, ingredient.name)" class="text-gray-400 hover:text-gray-600 transition duration-150 ease-in-out">
                      <svg class="w-6 h-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-show="!ingredients.data.length">
                <td colspan="4">
                  <div class="bg-white py-3 flex items-center justify-between w-full border-b border-gray-200">
                    <div class="flex-1 text-center py-4 px-4 sm:px-6 whitespace-no-wrap text-sm leading-5 text-gray-500">
                      No ingredients found
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
                          {{ ingredientsLowerLimit }}
                        </span>
                        to
                        <span class="font-medium">
                          {{ ingredientsUpperLimit }}
                        </span>
                        of
                        <span class="font-medium">
                          {{ ingredients.total }}
                        </span>
                        results
                      </p>
                    </div>
                    <div class="flex-1 flex justify-between sm:justify-end">
                      <inertia-link preserve-scroll preserve-state :href="ingredientsPreviousLink.url" :class="ingredientsPreviousLink.url ? 'text-gray-700 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150' : ' text-gray-300 cursor-default'" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md bg-white">
                        Previous
                      </inertia-link>
                      <inertia-link preserve-scroll preserve-state :href="ingredientsNextLink.url" :class="ingredientsNextLink.url ? 'text-gray-700 hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150' : ' text-gray-300 cursor-default'" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md bg-white">
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
        Delete recipe
      </h1>

      <p>
        Are you sure you want to delete <span class="font-medium">{{ recipe.name }}</span>?
        The recipe can still be restored afterwards if you change your mind.
      </p>

      <span slot="button">
        Delete
      </span>
    </Modal>

    <Modal :state="restoreModalState" @close="restoreModalState = false" @confirm="restore()" type="success">
      <h1 slot="header">
        Restore recipe
      </h1>

      <p>
        Are you sure you want to restore <span class="font-medium">{{ recipe.name }}</span>?
      </p>

      <span slot="button">
        Restore
      </span>
    </Modal>

    <Modal :state="addIngredientModalState" @close="addIngredientModalState = false; ingredientQuantity = ''" @confirm="addIngredient(selectedIngredientId)" type="success">
      <h1 slot="header">
        Add ingredient
      </h1>

      <div>
        Please enter a quantity for <span class="font-medium">{{ selectedIngredientName }}</span>.
      </div>
      <TextInput class="mt-2 max-w-xs w-full mx-auto" v-model="ingredientQuantity" :errors="$page.errors.quantity" placeholder="Enter quantity" label="Quantity" :hidden-label="true" :required="true" />

      <span slot="button">
        Add Ingredient
      </span>
    </Modal>

    <Modal :state="adjustIngredientQuantityModalState" @close="adjustIngredientQuantityModalState = false; ingredientQuantity = ''" @confirm="updateIngredient(selectedIngredientId)" type="success">
      <h1 slot="header">
        Adjust ingredient quantity
      </h1>

      <div>
        Please enter a quantity for <span class="font-medium">{{ selectedIngredientName }}</span>.
      </div>
      <TextInput class="mt-2 max-w-xs w-full mx-auto" v-model="ingredientQuantity" :errors="$page.errors.quantity" placeholder="Enter quantity" label="Quantity" :hidden-label="true" :required="true" />

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
      title: this.recipe.name,
    }
  },
  layout: Layout,
  components: {
    TextInput,
    TrashedMessage,
    Modal,
    Checkbox,
    SelectInput,
    Filters
  },
  props: {
    can: Object,
    recipe: Object,
    ingredients: Object,
    filters: Object,
    added_ingredients: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.recipe.name,
        description: this.recipe.description,
        dietary_restrictions: this.recipe.dietary_restrictions,
        calories: this.recipe.calories,
        price: this.recipe.price,
        product_image: null
      },
      deleteModalState: false,
      restoreModalState: false,
      selectedIngredientId: null,
      selectedIngredientName: '',
      addIngredientModalState: false,
      adjustIngredientQuantityModalState: false,
      ingredientQuantity: null,
      ingredientFilters: {
        search: this.filters.search || '',
        ingredients: this.filters.ingredients || 'all',
        pagination: this.filters.pagination || '10',
      },
    }
  },
  computed: {
    ingredientsPreviousLink() {
      return this.ingredients.links[0]
    },
    ingredientsNextLink() {
      return this.ingredients.links[this.ingredients.links.length - 1]
    },
    ingredientsLowerLimit() {
      if (((this.ingredients.currentPage - 1) * this.ingredientFilters.pagination) + 1 > this.ingredients.total) {
        return this.ingredients.total
      }

      return ((this.ingredients.currentPage - 1) * this.ingredientFilters.pagination) + 1
    },
    ingredientsUpperLimit() {
      if (this.ingredients.currentPage * this.ingredientFilters.pagination > this.ingredients.total) {
        return this.ingredients.total
      }

      return this.ingredients.currentPage * this.ingredientFilters.pagination
    },
  },
  methods: {
    onFileChange() {
      let files = this.$refs.file_input.files || this.$refs.file_input.dataTransfer.files
      if (!files.length)
        return
      this.form.product_image = files[0]
    },
    submit() {
      this.sending = true

      const data = new FormData()
      data.append('name', this.form.name || '')
      data.append('description', this.form.description || '')
      data.append('dietary_restrictions', this.form.dietary_restrictions || '')
      data.append('calories', this.form.calories || '')
      data.append('price', this.form.price || '')
      data.append('_method', 'put')
      data.append('product_image', this.form.product_image || '')

      this.$inertia.post(this.route('recipes.update', this.recipe.id), data)
        .then(() => {
          this.sending = false
        })
    },
    destroy() {
      this.deleteModalState = false
      this.$inertia.delete(this.route('recipes.destroy', this.recipe.id))
    },
    restore() {
      this.restoreModalState = false
      this.$inertia.put(this.route('recipes.restore', this.recipe.id))
    },
    reset() {
      this.ingredientFilters.search = ''
      this.ingredientFilters.ingredients = 'all'
      this.ingredientFilters.pagination = '10'
    },
    ingredientRecipeId(id) {
      const exists = this.added_ingredients.filter( ingredient => {
        return ingredient.ingredient_id === id
      })

      if (!exists.length) {
        return false
      }

      return exists[0].id
    },
    quantityUsed(id) {
      const ingredients = this.added_ingredients.filter( ingredient => {
        return ingredient.ingredient_id === id
      })

      if (!ingredients.length) {
        return 'N/A'
      }

      return ingredients[0].quantity
    },
    openAddIngredientModal(id, name) {
      this.selectedIngredientId = id
      this.selectedIngredientName = name
      this.addIngredientModalState = true
    },
    openAdjustIngredientQuantityModal(id, name) {
      this.selectedIngredientId = id
      this.selectedIngredientName = name
      this.ingredientQuantity = this.quantityUsed(id)
      this.adjustIngredientQuantityModalState = true
    },
    addIngredient(id) {
      const data = new FormData()
      data.append('quantity', this.ingredientQuantity || '')
      data.append('_method', 'post')

      this.$inertia.post(this.route('recipes.add_ingredient', {'recipe': this.recipe.id, 'ingredient': id }), data)

      this.addIngredientModalState = false
      this.ingredientQuantity = ''
    },
    updateIngredient(id) {
      const data = new FormData()
      data.append('quantity', this.ingredientQuantity || '')
      data.append('_method', 'post')

      this.$inertia.post(this.route('recipes.update_ingredient', this.ingredientRecipeId(id)), data)

      this.adjustIngredientQuantityModalState = false
      this.ingredientQuantity = ''
    },
    deleteRecipe(id) {
      this.$inertia.delete(this.route('recipes.remove_ingredient', this.ingredientRecipeId(id)))
    }
  },
  watch: {
    ingredientFilters: {
      handler: throttle(function() {
        let query = pickBy(this.ingredientFilters)
        this.$inertia.replace(this.route('recipes.edit', this.recipe.id), {
          data: Object.keys(query).length ? query : { remember: 'forget' }
        })
      }, 150),
      deep: true,
    },
  },
}
</script>
