import Vue from 'vue'
import router from './routes/router'
import store from './stores/store'
import Application from './views/Application'
import BootstrapVue from 'bootstrap-vue'
import Snotify, { SnotifyPosition } from 'vue-snotify'
import './bootstrap';
import { library } from '@fortawesome/fontawesome-svg-core'
import {
    faAngleDoubleUp, faBars, faBook, faBuilding, faCheck, faChevronLeft, faChevronRight, faChevronDown, faCircle, faCopy, faDownload,
    faEdit, faEnvelope, faExchangeAlt, faExternalLinkAlt, faFilter, faGlobe, faHome, faImages, faInfoCircle,
    faLayerGroup, faLink, faLock, faLockOpen, faPaste, faPen, faPlus, faQrcode, faSave, faSearch, faSearchPlus,
    faSignOutAlt, faSitemap, faSlidersH, faStar, faTimes, faTrashAlt, faUnlink, faUser, faUserCog, faUserPlus, faUsers,
} from '@fortawesome/free-solid-svg-icons'
import { faCreativeCommons, faOrcid } from "@fortawesome/free-brands-svg-icons";
import { FontAwesomeIcon, FontAwesomeLayers } from '@fortawesome/vue-fontawesome'
import VueLazyload from 'vue-lazyload'
import Avatar from 'vue-avatar'
import VueCookies from 'vue-cookies';
import VueScrollTo from 'vue-scrollto'
import ToggleButton from 'vue-js-toggle-button'
import VueTagsInput from '@johmun/vue-tags-input'
import LoadScript from 'vue-plugin-load-script';
import CheckUserLoginService from '@/services/CheckUserLoginService';

//set Axios base url
window.axios.defaults.baseURL = process.env.MIX_API_URL;

// register tags box component globally
Vue.component('vue-tags-input', VueTagsInput)

const snoptions = {
  toast: {
    position: SnotifyPosition.rightTop
  }
}

// Add Vue Bootstrap and Snotify to the global Vue App
Vue.use(BootstrapVue, {
  // Set the default popover boundary to viewport. We have lots of popovers that appear inside smaller scrolling
  // containers (like the sections inside the panel detail view), and these would be positioned strangely because the
  // actual default for this property is scrollParent.
  BPopover: {
    boundary: 'viewport',
    placement: 'auto'
  }
});
Vue.use(Snotify, snoptions)

//add toggle button
Vue.use(ToggleButton)

// Include a cookie handler
Vue.use(VueCookies);

// Add scroll to links behaviour
Vue.use(VueScrollTo)

// Include a method for injection of remote scripts
Vue.use(LoadScript);

// Add Fontawesome to the global Vue App
library.add([
    faAngleDoubleUp, faBars, faBook, faBuilding, faCheck, faChevronLeft, faChevronRight, faChevronDown, faCircle, faCopy,
    faCreativeCommons, faDownload, faEdit, faEnvelope, faExchangeAlt, faExternalLinkAlt, faFilter, faGlobe, faHome,
    faImages, faInfoCircle, faLayerGroup, faLink, faLock, faLockOpen, faOrcid, faPaste, faPen, faPlus, faQrcode,
    faSave, faSearch, faSearchPlus, faSignOutAlt, faSitemap, faSlidersH, faStar, faTimes, faTrashAlt, faUnlink, faUser,
    faUserCog, faUserPlus, faUsers,
])
Vue.component('font-awesome-icon', FontAwesomeIcon)
Vue.component('font-awesome-layers', FontAwesomeLayers)

//use Lazy Loading images
Vue.use(VueLazyload, {error:"/images/broken-image.jpg"})

//create Avatars dynamically
Vue.component('avatar', Avatar)

/**
 * IMPORTANT - check for existing login before mounting the Vue app
 */
CheckUserLoginService.verifyLogin().then(() => {

  /**
   * Next, we will create a fresh Vue application instance and attach it to
   * the page. Then, you may begin adding components to this application
   * or customize the JavaScript scaffolding to fit your unique needs.
   */
  if(document.getElementById("dashboard")) {
    const app = new Vue({
        router,
        store,
        render: h => h(Application)
    }).$mount('#dashboard');
  }

});
