import { InertiaApp } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue'
import VueMeta from 'vue-meta'
import Vue from 'vue'
import VTooltip from 'v-tooltip'

VTooltip.enabled = window.innerWidth > 768

Vue.config.productionTip = false
Vue.mixin({ methods: { route: window.route } })
Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.use(VueMeta)
Vue.use(VTooltip)

let app = document.getElementById('app')

new Vue({
  metaInfo: {
    titleTemplate: (title) => title ? `${title} | sterling` : 'sterling'
  },
  render: h => h(InertiaApp, {
    props: {
      initialPage: JSON.parse(app.dataset.page),
      resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    },
  }),
}).$mount(app)
