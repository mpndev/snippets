<style>
    .default-login-logo {
        width: 1rem;
        box-shadow: 0 0 5px 5px #fff;
        border-radius: 4px;
    }
</style>

<template>
    <div class="columns">
        <div class="column is-6 is-offset-3">
            <table v-if="user" :class="{ 'darkmod': Auth.isDarkMod() }" class="table is-fullwidth is-bordered has-text-centered background-is-white text-is-dark">
                <tbody>
                <tr>
                    <th><span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark">{{ $t('Name') }}</span></th>
                    <td><span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark">{{ user.name }}</span></td>
                </tr>
                <tr>
                    <th><span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark">{{ $t('Email') }}</span></th>
                    <td><span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark">{{ user.email }}</span></td>
                </tr>
                <tr>
                    <th><logo class="default-login-logo"></logo></th>
                    <td>
                        <span v-if="user.id != Auth.user.id || user.github_id || user.google_id || user.facebook_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th><span :class="{ 'darkmod': Auth.isDarkMod() }" class="fab fa-github text-is-dark"></span></th>
                    <td>
                        <span v-if="user.id != Auth.user.id || !user.github_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th><span class="fab fa-google has-text-danger"></span></th>
                    <td>
                        <span v-if="user.id != Auth.user.id || !user.google_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th><span class="fab fa-facebook has-text-info"></span></th>
                    <td>
                        <span v-if="user.id != Auth.user.id || !user.facebook_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th v-if="Auth.user.id == user.id"><span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark">{{ $t('My snippets') }}</span></th>
                    <th v-else><span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark">{{ $t('Snippets') }}</span></th>
                    <td>{{ user.snippets.length }}</td>
                </tr>
                <tr>
                    <th v-if="Auth.user.id == user.id"><span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark">{{ $t('Site theme mode.') }}</span></th>
                    <th v-else>—</th>
                    <td>
                        <button v-if="Auth.user.id == user.id" class="button is-info is-small" @click="toggleThemeMod()">{{ $t('Switch') }}</button>
                        <span v-else>—</span>
                    </td>
                </tr>
                </tbody>
            </table>
            <div v-else class="columns is-centered">
                <ring-loader class="column is-narrow snippet-summary-ring"></ring-loader>
            </div>
        </div>
    </div>
</template>

<script>
    import Logo from '../../components/Logo'

    export default {
        data: () => {
            return {
                Auth: Auth,
                user: null
            }
        },
        created() {
            axios.get('/api/users/' + this.$route.params.user + '?api_token=' + this.Auth.getApiToken()).then(response => {
                this.user = response.data
            }).catch(error => {
                this.error({message: error.response.data.user[0]})
            })
        },
        components: {
          Logo: Logo
        },
        methods: {
            logout() {
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
            },
            toggleThemeMod() {
                let darkmod = this.Auth.isDarkMod()
                axios.post("/api/users/" + this.Auth.user.id + "/darkmod", {
                    'darkmod': !darkmod,
                    'api_token': Auth.getApiToken(),
                    '_method': 'PUT'
                }).then(response => {
                    this.Auth.setDarkmod(response.data.darkmod)
                    this.user = this.Auth.user
                    this.$forceUpdate()
                })
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
