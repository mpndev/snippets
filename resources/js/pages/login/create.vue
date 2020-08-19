<template>
    <div>
        <div class="field">
            <label class="label">{{ $t('Name') }}</label>
            <div class="control">
                <input class="input" type="text" v-model="name" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.name">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">{{ $t('Password') }}</label>
            <div class="control">
                <input class="input" type="password" v-model="password" @focusin="resetErrors">
            </div>
        </div>
        <div class="control">
            <button class="button is-link" @click="login">{{ $t('Login') }}</button>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                Auth: Auth,
                name: '',
                password: '',
                errors: {
                    name: []
                }
            }
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('login')
        },
        methods: {
            resetErrors() {
                this.errors.name = []
            },
            login() {
                this.resetErrors()

                axios.post('/api/login', {
                    name: this.name,
                    password: this.password
                }).then(response => {
                    this.success({message: `${this.$t('Welcome')} ${response.data.name}!`})
                    this.Auth.update(response.data)
                    this.$router.push({ name: 'snippets.index' })
                }).catch(error => {
                    let parsed_errors = ''
                    if (error.response && error.response.data) {
                        if (error.response.data.name) {
                            for(let sub_error of error.response.data.name) {
                                parsed_errors += sub_error + "\n"
                            }
                        }
                    }
                    this.errors.name.push(parsed_errors)
                })
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
