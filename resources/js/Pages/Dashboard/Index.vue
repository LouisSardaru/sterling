<template>
  <div>
    <h1 class="text-3xl text-gray-900">
      Welcome, <span class="font-semibold">{{ $page.auth.user.first_name }} {{ $page.auth.user.last_name }}</span>!
    </h1>
    
    <div class="mt-8">
      <div>
        <h3 class="text-lg leading-6 font-medium text-gray-900">
          Orders today
        </h3>
        <div class="mt-3 grid grid-cols-1 gap-5 sm:grid-cols-3">
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <dl>
                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                  Placed
                </dt>
                <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                  {{ orders_placed }}
                </dd>
              </dl>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <dl>
                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                  Takeout
                </dt>
                <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                  {{ orders_takeout }}
                </dd>
              </dl>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <dl>
                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                  Fullfilled
                </dt>
                <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                  {{ orders_fullfilled.toFixed(2) }}% 
                </dd>
              </dl>
            </div>
          </div>
          <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
              <dl>
                <dt class="text-sm leading-5 font-medium text-gray-500 truncate">
                  Profit
                </dt>
                <dd class="mt-1 text-3xl leading-9 font-semibold text-gray-900">
                  &dollar; {{ profit }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'

export default {
  metaInfo: { title: 'Dashboard' },
  layout: Layout,
  props: {
    orders_placed: Number,
    orders_takeout: Number,
    orders_fullfilled: Number,
    tables_unassigned: Number,
    profit: String
  }
}
</script>
