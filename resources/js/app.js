import Vue from 'vue'
import axios from 'axios'
import VueRouter from 'vue-router'
import routes from './routes'
import Navbar from './components/Navbar'
import Message from './components/Message'
import RingLoader from 'vue-spinner/src/RingLoader'
import VueClipboard from 'vue-clipboard2'
import VueNotifications from 'vue-notifications'
import miniToastr from 'mini-toastr'
import Auth from './Auth'
import 'bulma/css/bulma.css'
import 'bulma-helpers/css/bulma-helpers.min.css'

window.axios = axios
window.Event = new Vue()
window.Initializer = null
window.Auth = Auth

Vue.use(VueRouter)
Vue.use(VueClipboard)

const toastTypes = {
    success: 'success',
    error: 'error',
    info: 'info',
    warn: 'warn'
}
miniToastr.init({types: toastTypes})
function toast ({title, message, type, timeout, cb}) {
    return miniToastr[type](message, title, timeout, cb)
}
const options = {
    success: toast,
    error: toast,
    info: toast,
    warn: toast
}
VueNotifications.config.timeout = 6000
Vue.use(VueNotifications, options)

Vue.component('ring-loader', RingLoader)

Vue.component('message', Message)

let app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
    components: {
        navbar: Navbar
    },
    data: () => {
        return {
            screen: {
                width: 0,
                height: 0
            }
        }
    },
    created() {
        window.addEventListener('resize', this.handleResize);
        this.handleResize();
    },
    destroyed() {
        window.removeEventListener('resize', this.handleResize);
    },
    methods: {
        handleResize() {
            this.screen.width = window.innerWidth;
            this.screen.height = window.innerHeight;
        }
    }
})
