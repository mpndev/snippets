<style>
    .navbar {
        z-index: 30;
    }
    .navbar-menu {
        padding: 0;
    }
</style>

<template>
    <nav class="navbar is-primary is-fixed-top">
        <div class="container" @mouseleave="burger_is_on = false">
            <div class="navbar-brand">
                <h1 class="navbar-item">
                    <router-link :to="{ name: 'snippets.index' }">
                        <logo></logo>
                    </router-link>
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
                    <router-link v-if="Auth.check()" :to="{ name: 'snippets.create' }" class="navbar-item has-text-white glowing-text">{{ $t('Create Snippet') }}</router-link>
                    <router-link class="navbar-item has-text-white" :to="{ name: 'tags.index' }">{{ $t('Tags') }}</router-link>
                    <language-switcher class="navbar-item has-text-white" />
                    <router-link v-if="Auth.check()" class="navbar-item has-text-white" :to="{ name: 'users.show', params: { user: Auth.user.id } }">
                        {{ Auth.user.name }}
                        <span v-if="Auth.user.facebook_id" class="fab fa-facebook has-text-info ml-2"></span>
                        <span v-if="Auth.user.github_id" class="fab fa-github has-text-dark ml-2"></span>
                        <span v-if="Auth.user.google_id" class="fab fa-google has-text-danger ml-2"></span>
                    </router-link>
                    <router-link v-if="Auth.check() && Auth.hasAbility('manage_users')" class="navbar-item has-text-white" :to="{ name: 'users.index' }">{{ $t('Manage Users') }}</router-link>
                    <router-link v-if="Auth.guest()" :to="{ name: 'login.create' }" class="navbar-item has-text-white glowing-text" @click="burger_is_on = false">{{ $t('Login') }}</router-link>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import LanguageSwitcher from "./LanguageSwitcher"
    import Logo from "./Logo"
    export default {
        components: {
            LanguageSwitcher: LanguageSwitcher,
            Logo: Logo,
        },
        data: () => {
            return {
                Auth: Auth,
                burger_is_on: false
            }
        },
        methods: {
            profile() {
                this.burger_is_on = false
                this.$router.push({ name: 'profile.show' })
            }
        },
        notifications: require('../GlobalNotifications')
    }
</script>

<style scoped="scoped">
    .glowing-text {
        animation: glow 10s linear infinite;
    }
    @-webkit-keyframes glow {
        0% {
            text-shadow: 6px 0 0 orange, -6px 0 0 orange, 0 6px 0 orange, 0 -6px 0 orange;
        }
        20% {
            text-shadow: 0 0 0 orange;
        }
        100% {
            text-shadow: 0 0 0 orange;
        }
    }
</style>
