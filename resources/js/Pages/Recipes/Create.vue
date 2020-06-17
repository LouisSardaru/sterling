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
              Recipe information
            </h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Make sure to fill in all the fields in this section. Ingredients can be added when editing the recipe.
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
                <TextInput v-model="form.dietary_restrictions" :errors="$page.errors.dietary_restrictions" label="Dietary restrictions" help="e.g. Vegan, Gluten free etc." :optional="true" />
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

export default {
  metaInfo: { title: 'Create recipe' },
  layout: Layout,
  components: {
    TextInput,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: null,
        description: null,
        dietary_restrictions: null,
        calories: null,
        price: null,
        product_image: null
      },
    }
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
      data.append('product_image', this.form.product_image || '')

      this.$inertia.post(this.route('recipes.store'), data)
        .then(() => this.sending = false)
    },
  },
}
</script>
