<style>
    .navbar-menu {
        padding: 0;
    }
</style>

<template>
    <nav class="navbar is-primary is-fixed-top">
        <div class="container" @mouseleave="burger_is_on = false">
            <div class="navbar-brand">
                <h1 class="navbar-item">
                    <router-link :to="{ name: 'snippets.index' }">{{ $t('Snippets') }}</router-link>
                </h1>
                <span class="navbar-burger burger" @click="burger_is_on = !burger_is_on">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </div>
            <div class="navbar-menu is-relative" :class="{ 'is-active': burger_is_on }">
                <div class="navbar-end has-background-primary has-text-centered" @click="burger_is_on = false">
                    <router-link class="navbar-item has-text-white" :to="{ name: 'snippets.index' }">{{ $t('Home') }}</router-link>
                    <router-link v-if="Auth.check()" :to="{ name: 'snippets.create' }" class="navbar-item has-text-white">{{ $t('Create Snippet') }}</router-link>
                    <router-link class="navbar-item has-text-white" :to="{ name: 'tags.index' }">{{ $t('Tags') }}</router-link>
                    <language-switcher class="navbar-item has-text-white" />
                    <a v-if="Auth.check()" class="navbar-item has-text-white" @click="logout">{{ $t('Logout') }}</a>
                    <router-link v-if="Auth.guest()" :to="{ name: 'login.create' }" class="navbar-item has-text-white" @click="burger_is_on = false">{{ $t('Login') }}</router-link>
                    <router-link v-if="Auth.guest()" :to="{ name: 'register.create' }" class="navbar-item has-text-white" @click="burger_is_on = false">{{ $t('Register') }}</router-link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import LanguageSwitcher from "./LanguageSwitcher";
    export default {
        components: {
            LanguageSwitcher: LanguageSwitcher
        },
        data: () => {
            return {
                Auth: Auth,
                burger_is_on: false
            }
        },
        methods: {
            logout() {
                this.burger_is_on = false
                axios.post('/api/logout', {
                    api_token: this.Auth.getApiToken(),
                    _method: 'DELETE'
                }).then(response => {
                    this.success({message: `${this.$t('See ya later')} ${this.Auth.getName()}!`})
                    this.Auth.logout()
                    this.$router.push({ name: 'login.create' })
                }).catch(error => {
                    this.error({message: error.response.data.user[0]})
                })
            }
        },
        notifications: require('../GlobalNotifications')
    }
</script>
