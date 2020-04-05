import Vue from 'vue'
import VueRouter from 'vue-router'
import routes from './routes'
import Navbar from './components/Navbar'
import Message from './components/Message'
import RingLoader from 'vue-spinner/src/RingLoader'
import VueClipboard from 'vue-clipboard2'
import VueNotifications from 'vue-notifications'
import miniToastr from 'mini-toastr'

window.Event = new Vue()

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

let user = localStorage.user ? JSON.parse(localStorage.user) : null
window.Initializer = null

let app = new Vue({
    el: '#app',
    router: new VueRouter(routes),
    mounted() {
        Event.$on('login', this.userWasLogin)
        Event.$on('logout', this.userWasLoggedOut)
        Event.$on('register', this.userRegistered)
    },
    watch: {
       user(value) {
           localStorage.user = JSON.stringify(value)
       }
    },
    data: () => {
        return {
            user: user
        }
    },
    components: {
        navbar: Navbar
    },
    methods: {
        userWasLogin(user) {
            this.user = user
            this.$router.push({ name: 'snippets.index' })
        },
        userWasLoggedOut() {
            this.user = null
            this.$router.push({ name: 'login.create' })
        },
        userRegistered(user) {
            this.user = user
            this.$router.push({ name: 'snippets.index' })
        }
    }
})
