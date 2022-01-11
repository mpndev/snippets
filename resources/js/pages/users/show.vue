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
            <table v-if="user" class="table is-fullwidth is-bordered has-text-centered">
                <tbody>
                <tr>
                    <th>{{ $t('Name') }}</th>
                    <td>{{ user.name }}</td>
                </tr>
                <tr>
                    <th>{{ $t('Email') }}</th>
                    <td>{{ user.email }}</td>
                </tr>
                <tr>
                    <th><logo class="default-login-logo"></logo></th>
                    <td>
                        <span v-if="user.id != Auth.user.id || user.github_id || user.google_id || user.facebook_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th><span class="fab fa-github has-text-dark"></span></th>
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
                    <th v-if="Auth.user.id == user.id">{{ $t('My snippets') }}</th>
                    <th v-else>{{ $t('Snippets') }}</th>
                    <td>{{ user.snippets.length }}</td>
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
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
