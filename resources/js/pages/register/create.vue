<template>
    <div>
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input class="input" type="text" v-model="name" placeholder="minimum 2 symbols" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.name">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" v-model="password" placeholder="minumum 8 symbols" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.password">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">Password Confirmation</label>
            <div class="control">
                <input class="input" type="password" v-model="password_confirmation" placeholder="repeat password" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.password_confirmation">{{ error }}</p>
            </div>
        </div>
        <div class="control">
            <button class="button is-link" @click="register">Register</button>
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
                errors: {
                    name: [],
                    password: [],
                    password_confirmation: [],
                }
            }
        },
        methods: {
            resetErrors() {
                this.errors.name = []
                this.errors.password = []
                this.errors.password_confirmation = []
            },
            validateFields() {
                if (this.name.length < 2) {
                    this.errors.name.push('Name must be more then 1 symbols.');
                }
                if (this.name.length > 255) {
                    this.errors.name.push('Name must be less then 256 symbols.');
                }
                if (this.password.length < 8) {
                    this.errors.password.push('Password must be more then 7 symbols.');
                }
                if (this.password !== this.password_confirmation) {
                    this.errors.password_confirmation.push('Password Confirmation and Password must be the same.');
                }
            },
            register() {
                this.resetErrors()

                this.validateFields()

                if (this.errors.name.length || this.errors.password.length || this.errors.password_confirmation.length) {
                    return;
                }

                axios.post('/api/register', {
                    name: this.name,
                    password: this.password,
                    password_confirmation: this.password_confirmation
                }).then(response => {
                    this.success({
                        message: "Welcome " + response.data.name,
                    })
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
