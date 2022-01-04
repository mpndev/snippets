<template>
    <div>
        <div class="field">
            <label class="label">{{ $t('New Password') }}</label>
            <div class="control">
                <input class="input" type="password" v-model="password" :placeholder="$t('minumum 8 symbols')" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.password">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">{{ $t('New Password Confirmation') }}</label>
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
            <button class="button is-link" @click="reset">{{ $t('Reset') }}</button>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                Auth: Auth,
                token: '',
                password: '',
                password_confirmation: '',
                email: '',
                errors: {
                    password: [],
                    password_confirmation: [],
                    email: [],
                }
            }
        },
        created() {
            this.token = this.$route.query.token
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('Reset password')
        },
        methods: {
            resetErrors() {
                this.errors.password = []
                this.errors.password_confirmation = []
                this.errors.email = []
            },
            validateFields() {
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
            reset() {
                this.resetErrors()

                this.validateFields()

                if (this.errors.password.length || this.errors.password_confirmation.length || this.errors.email.length) {
                    return;
                }

                axios.post('/api/password-reset', {
                    token: this.token,
                    password: this.password,
                    password_confirmation: this.password_confirmation,
                    email: this.email
                }).then(response => {
                    this.success({
                        message: this.$t('Password was reset successfully.'),
                    })
                    this.$router.push({ name: 'login.create' })
                }).catch(error => {
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
