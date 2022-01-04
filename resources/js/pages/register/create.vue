<template>
    <div>
        <div class="field">
            <label class="label">{{ $t('Name') }}</label>
            <div class="control">
                <input class="input" type="text" v-model="name" :placeholder="$t('minimum 2 symbols')" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.name">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">{{ $t('Password') }}</label>
            <div class="control">
                <input class="input" type="password" v-model="password" :placeholder="$t('minumum 8 symbols')" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.password">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">{{ $t('Password Confirmation') }}</label>
            <div class="control">
                <input class="input" type="password" v-model="password_confirmation" :placeholder="$t('repeat password')" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.password_confirmation">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">{{ $t('Email') }}</label>
            <div class="control">
                <input class="input" type="email" v-model="email" :placeholder="$t('Email')" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.email">{{ error }}</p>
            </div>
        </div>
        <div class="control">
            <button class="button is-link" @click="register">{{ $t('Register') }}</button>
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
                password_confirmation: '',
                email: '',
                errors: {
                    name: [],
                    password: [],
                    password_confirmation: [],
                    email: [],
                }
            }
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('registration')
        },
        methods: {
            resetErrors() {
                this.errors.name = []
                this.errors.password = []
                this.errors.password_confirmation = []
                this.errors.email = []
            },
            validateFields() {
                if (this.name.length < 2) {
                    this.errors.name.push(this.$t('Name must be more then 1 symbols.'));
                }
                if (this.name.length > 255) {
                    this.errors.name.push(this.$t('Name must be less then 256 symbols.'));
                }
                if (this.password.length < 8) {
                    this.errors.password.push(this.$t('Password must be more then 7 symbols.'));
                }
                if (this.password !== this.password_confirmation) {
                    this.errors.password_confirmation.push(this.$t('Password Confirmation and Password must be the same.'));
                }
                if (this.email.length == 0) {
                    this.errors.email.push(this.$t('Please provide email.'));
                }
                else if (!String(this.email).toLowerCase().match(/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i)) {
                    this.errors.email.push(this.$t('Please provide valid email.'));
                }
            },
            register() {
                this.resetErrors()

                this.validateFields()

                if (this.errors.name.length || this.errors.password.length || this.errors.password_confirmation.length || this.errors.email.length) {
                    return;
                }

                axios.post('/api/register', {
                    name: this.name,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    email: this.email
                }).then(response => {
                    this.success({
                        message: "Welcome " + response.data.name,
                    })
                    this.Auth.update(response.data)
                    this.$router.push({ name: 'snippets.index' })
                }).catch(error => {
                    if (error.response.data.email) {
                        this.errors.email.push(this.$t('Email has already been taken.'))
                        return
                    }
                    let parsed_errors = ''

                    if (error.response && error.response.data) {
                        if (error.response.data.name) {
                            for(let sub_error of error.response.data.name) {
                                parsed_errors += sub_error + "\n"
                            }
                        }

                        if (error.response.data.password) {
                            for(let sub_error of error.response.data.password) {
                                parsed_errors += sub_error + "\n"
                            }
                        }

                        if (error.response.data.password_confirmation) {
                            for(let sub_error of error.response.data.password_confirmation) {
                                parsed_errors += sub_error + "\n"
                            }
                        }
                    }

                    this.error({
                        message: parsed_errors,
                    })
                })
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
