import Vue from 'vue'
import App from './App'
import router from './router'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import Snotify, { SnotifyPosition } from 'vue-snotify'
import 'vue-snotify/styles/material.css'
import Access from '@/directives/access'
import 'vue-awesome/icons'
import lodash from 'lodash'
import Icon from 'vue-awesome/components/Icon'
import store from './store'
import '@/filters/filters.js'
import VueI18n from 'vue-i18n'
import messages from '@/lang/messages'
import VeeValidate from 'vee-validate';

Vue.config.productionTip = false

// globally (in your main .js file)
const snotifyOptions = {
	toast: {
		position: SnotifyPosition.rightTop
	}
}

const veeconfig = {
	fieldsBagName: 'veefields'
}

Vue.use(Snotify, snotifyOptions)
Vue.use(BootstrapVue)
Vue.use(VueI18n)
Vue.use(lodash)
Vue.use(VeeValidate, veeconfig);
Vue.component('v-icon', Icon)
Vue.directive('access', Access)

// Create VueI18n instance with options
const i18n = new VueI18n({
	locale: 'en',
	messages
})


new Vue({
	el: '#app',
	router,
	store,
	i18n,
	components: { App },
	template: '<App/>'
})
