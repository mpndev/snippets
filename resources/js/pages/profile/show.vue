<style>
    .default-login-logo {
        width: 1rem;
        box-shadow: 0 0 5px 5px #fff;
        border-radius: 4px;
    }
</style>

<template>
    <div class="columns mt-5">
        <div class="column is-6 is-offset-3">
            <table class="table is-fullwidth is-bordered has-text-centered">
                <tbody>
                <tr>
                    <th>{{ $t('Name') }}</th>
                    <td>{{ Auth.user.name }}</td>
                </tr>
                <tr>
                    <th>{{ $t('Email') }}</th>
                    <td>{{ Auth.user.email }}</td>
                </tr>
                <tr>
                    <th><logo class="default-login-logo"></logo></th>
                    <td>
                        <span v-if="Auth.user.github_id || Auth.user.google_id || Auth.user.facebook_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th><span class="fab fa-github has-text-dark"></span></th>
                    <td>
                        <span v-if="!Auth.user.github_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th><span class="fab fa-google has-text-danger"></span></th>
                    <td>
                        <span v-if="!Auth.user.google_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th><span class="fab fa-facebook has-text-info"></span></th>
                    <td>
                        <span v-if="!Auth.user.facebook_id">—</span>
                        <button v-else class="button is-danger is-small" @click="logout">{{ $t('Logout') }}</button>
                    </td>
                </tr>
                <tr>
                    <th>{{ $t('My snippets') }}</th>
                    <td>{{Auth.user.snippets.length}}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import Logo from '../../components/Logo'

    export default {
        data: () => {
            return {
                Auth: Auth
            }
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
