<template>
    <div class="box"><h2><b>{{ $t('Login into google, please wait...') }}</b></h2></div>
</template>

<script>
    export default {
        data: () => {
            return {
                Auth: Auth,
            }
        },
        created() {
            this.loginGoogleCallback()
        },
        methods: {
            loginGoogleCallback() {
                axios.post('/api/login/google/store', {
                    code: this.$route.query.code
                }).then(response => {
                    this.Auth.update(response.data)
                    this.success({message: `${this.$t('Welcome')} ${response.data.name}!`})
                    this.$router.push({ name: 'snippets.index' })
                }).catch(error => {
                    this.$router.push({ name: 'login.create' })
                })
            }
        },
        notifications: require('../../../GlobalNotifications')
    }
</script>
