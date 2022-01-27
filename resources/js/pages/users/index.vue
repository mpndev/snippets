<template>
    <div class="columns">
        <div class="column is-6 is-offset-3">
            <table :class="{ 'darkmod': Auth.isDarkMod() }" class="table is-fullwidth is-bordered has-text-centered background-is-white text-is-dark">
                <tbody>
                <tr v-for="user in users">
                    <th>
                        <span :class="{ 'darkmod': Auth.isDarkMod() }" class="text-is-dark is-clickable" @click="show(user)">{{ user.name }} ({{ user.email }})</span>
                    </th>
                    <td>
                        <button class="button is-info fa fa-eye is-small" @click="show(user)"></button>
                        <button v-if="user.id != Auth.user.id" class="button is-danger fa fa-trash-alt is-small" @click="destroy(user)"></button>
                    </td>
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
                Auth: Auth,
                users: []
            }
        },
        created() {
            axios.get('/api/users?api_token=' + this.Auth.getApiToken()).then(response => {
                this.users = response.data
            }).catch(error => {
                this.error({message: error.response.data.user[0]})
            })
        },
        methods: {
            show(user) {
                this.$router.push({ name: 'users.show', params: { user: user.id } })
            },
            destroy(user) {
                axios.post('/api/users/' + user.id + '?api_token=' + this.Auth.getApiToken(), {
                    _method: 'DELETE'
                }).then(response => {
                    this.success({message: `${this.$t('User was successful deleted. Also all of his snippets and snippets related data.')} (${user.email})`})
                    axios.get('/api/users?api_token=' + this.Auth.getApiToken()).then(response => {
                        this.users = response.data
                    }).catch(error => {
                        this.error({message: error.response.data.user[0]})
                    })
                }).catch(error => {
                    this.error({message: error.toString()})
                })
            },
        },
        components: {
            Logo: Logo
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
