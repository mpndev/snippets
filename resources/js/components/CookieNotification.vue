<style>
    #vue_cookie_privacy {
        width: 90%;
        margin: 0 auto;
        z-index: 999999;
    }
</style>

<template>
    <vue-cookie-notification v-if="is_mounted" :cookietext="cookietext" :cookiebutton="cookiebutton" :cookietextcolor="cookietextcolor" :cookietextsize="cookietextsize" :cookiebackgroundcolor="cookiebackgroundcolor" :cookiename="cookiename" :cookievalue="cookievalue" :cookieexdays="cookieexdays" ></vue-cookie-notification>
</template>

<script>
    import VueCookieNotification from 'vue-cookie-notification'
    export default {
        data: () => {
            return {
                is_mounted: false,
                cookietext: '',
                cookiebutton: '',
                cookietextcolor: 'white',
                cookietextsize: '1rem',
                cookiebackgroundcolor: 'orange',
                cookiename: 'VUECOOKIE',
                cookievalue: '12345',
                cookieexdays: 365
            }
        },
        mounted() {
            this.cookietext = this.$t('In order to give you the best experience, our website eats cookies. By continuing to use this site, you agree to our diet of cookies.')
            this.cookiebutton = this.$t('feed me')
            this.is_mounted = true
        },
        components: {
            VueCookieNotification: VueCookieNotification
        }
    }
</script>
