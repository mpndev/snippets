<template>
    <div>
        <div class="field">
            <label class="label">{{ $t('Email') }}</label>
            <div class="control">
                <input class="input" type="text" v-model="email" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.email">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">{{ $t('Password') }}</label>
            <div class="control">
                <input class="input" type="email" v-model="password" @focusin="resetErrors">
            </div>
        </div>
        <div class="control is-flex is-align-items-center">
            <button class="button is-link" @click="login">{{ $t('Login') }}</button>
            <a class="is-size-6 ml-3" @click.prevent="forgetPassword">{{ $t('Forget password?') }}</a>
        </div>
        <div class="control mt-5 is-flex is-align-items-center box">
            <span><b>{{ $t('Do not have account? Try simple') }}</b></span>
            <a class="button is-primary ml-1 mr-1" href="/register">{{ $t('Register') }}</a>
            <span><b>{{ $t('with username, email and password') }}</b></span>
            <span class="ml-1"><b>{{ $t('or') }}</b></span>
            <button class="button is-dark ml-1 mr-1" @click="loginWithGithub">{{ $t('Login with github') }}<span class="fab fa-github ml-2"></span></button>
            <span class="ml-1 mr-1"><b>, {{ $t('or') }}</b></span>
            <button class="button is-danger ml-1 mr-1" @click="loginWithGoogle">{{ $t('Login with google') }}<span class="fab fa-google ml-2"></span></button>
            <span class="ml-1 mr-1"><b>, {{ $t('or') }}</b></span>
            <button class="button is-info ml-1 mr-1" @click="loginWithFacebook">{{ $t('Login with facebook') }}<span class="fab fa-facebook ml-2"></span></button>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                Auth: Auth,
                email: '',
                password: '',
                errors: {
                    email: []
                }
            }
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('login')
        },
        methods: {
            resetErrors() {
                this.errors.email = []
            },
            login() {
                this.resetErrors()

                axios.post('/api/login', {
                    email: this.email,
                    password: this.password,
                }).then(response => {
                    this.success({message: `${this.$t('Welcome')} ${response.data.email}!`})
                    this.Auth.update(response.data)
                    this.$router.push({ name: 'snippets.index' })
                }).catch(error => {
                    let parsed_errors = ''
                    if (error.response && error.response.data) {
                        if (error.response.data.email) {
                            for(let sub_error of error.response.data.email) {
                                parsed_errors += sub_error + "\n"
                            }
                        }
                    }
                    this.errors.email.push(parsed_errors)
                })
            },
            loginWithGithub() {
                this.resetErrors()

                axios.get('/api/login/github/create').then(response => {
                    window.location = response.data.redirect_url
                }).catch(error => {})
            },
            loginWithGoogle() {
                this.resetErrors()

                axios.get('/api/login/google/create').then(response => {
                    window.location = response.data.redirect_url
                }).catch(error => {})
            },
            loginWithFacebook() {
                this.resetErrors()

                axios.get('/api/login/facebook/create').then(response => {
                    window.location = response.data.redirect_url
                }).catch(error => {})
            },
            forgetPassword() {
                this.$router.push({ name: 'password.forget' })
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
