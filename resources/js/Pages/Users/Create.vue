<template>
  <div>
    <div>
      <nav class="sm:hidden">
        <inertia-link :href="route('users')" class="flex items-center text-sm leading-5 font-medium text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          <svg class="flex-shrink-0 -ml-1 mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
          </svg>
          Back
        </inertia-link>
      </nav>
      <nav class="hidden sm:flex items-center text-sm leading-5 font-medium">
        <inertia-link :href="route('users')" class="text-gray-500 hover:text-gray-700 transition duration-150 ease-in-out">
          Users
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
              Account information
            </h3>
            <p class="mt-1 text-sm leading-5 text-gray-500">
              Make sure to fill in all the fields in this section.
            </p>
          </div>
          <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="grid grid-cols-6 gap-6">
              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.first_name" :errors="$page.errors.first_name" label="First name" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.last_name" :errors="$page.errors.last_name" label="Last name" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.email" :errors="$page.errors.email" label="Email" type="email" :required="true" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <TextInput v-model="form.password" :errors="$page.errors.password" label="Password" type="password" />
              </div>

              <div class="col-span-6 sm:col-span-3">
                <SelectInput v-model="form.role" label="Role">
                  <option value="select">Select a role</option>
                  <option v-for="(role, index) in roles" :value="role.name" :key="index">
                    {{ role.name }}
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
  metaInfo: { title: 'Create user' },
  layout: Layout,
  components: {
    TextInput,
    SelectInput,
  },
  props: {
    roles: Array,
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        first_name: null,
        last_name: null,
        email: null,
        password: null,
        role: 'select',
      },
    }
  },
  methods: {
    submit() {
      this.sending = true

      const data = new FormData()
      data.append('first_name', this.form.first_name || '')
      data.append('last_name', this.form.last_name || '')
      data.append('email', this.form.email || '')
      data.append('password', this.form.password || '')
      data.append('role', this.form.role || 'select')

      this.$inertia.post(this.route('users.store'), data)
        .then(() => this.sending = false)
    },
  },
}
</script>
