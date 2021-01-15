import Vue from 'vue'
import router from './routes/router'
import store from './stores/store'
import Dashboard from './views/Dashboard'
import BootstrapVue from 'bootstrap-vue'
import Snotify, { SnotifyPosition } from 'vue-snotify'
import './bootstrap';
import { library } from '@fortawesome/fontawesome-svg-core'
import { faLink, faHome, faEdit, faSearchPlus, faDownload, faSave, faTrashAlt, faCheck, faPlus, faLock, faLockOpen,
  faExchangeAlt, faUsers, faLayerGroup, faFilter, faCircle, faSignOutAlt, faCopy, faPaste, faExternalLinkAlt,
  faStar, faBook, faInfoCircle, faTimes, faSearch, faChevronLeft, faChevronRight} from '@fortawesome/free-solid-svg-icons'
import { faCreativeCommons } from "@fortawesome/free-brands-svg-icons";
import { FontAwesomeIcon, FontAwesomeLayers } from '@fortawesome/vue-fontawesome'
import VueLazyload from 'vue-lazyload'
import Avatar from 'vue-avatar'
import VueScrollTo from 'vue-scrollto'
import ToggleButton from 'vue-js-toggle-button'
import VueTagsInput from '@johmun/vue-tags-input'

// register tags box component globally
Vue.component('vue-tags-input', VueTagsInput)

const snoptions = {
  toast: {
    position: SnotifyPosition.rightTop
  }
}

// Add Vue Bootstrap and Snotify to the global Vue App
Vue.use(BootstrapVue)
Vue.use(Snotify, snoptions)

//add toggle button
Vue.use(ToggleButton)

// Add scroll to links behaviour
Vue.use(VueScrollTo)


// Add Fontawesome to the global Vue App
library.add([faLink, faHome, faEdit, faSave, faCheck, faTrashAlt, faSearchPlus, faCopy, faPaste, faDownload, faPlus,
  faLock, faLockOpen, faExchangeAlt, faUsers, faLayerGroup, faFilter, faCircle, faSignOutAlt, faExternalLinkAlt,
  faStar, faBook, faInfoCircle, faTimes, faSearch, faChevronLeft, faChevronRight, faCreativeCommons])
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)

//use Lazy Loading images
Vue.use(VueLazyload, {error:"/images/broken-image.jpg"})

//create Avatars dynamically
Vue.component('avatar', Avatar)


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
if(document.getElementById("dashboard")) {
  const app = new Vue({
      router,
      store,
      render: h => h(Dashboard)
  }).$mount('#dashboard');
}
