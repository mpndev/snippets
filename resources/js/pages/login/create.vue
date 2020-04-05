<template>
    <div>
        <div class="field">
            <label class="label">Name</label>
            <div class="control">
                <input class="input" type="text" v-model="name" @focusin="resetErrors">
                <p class="has-text-danger" v-for="error in errors.name">{{ error }}</p>
            </div>
        </div>
        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" v-model="password" @focusin="resetErrors">
            </div>
        </div>
        <div class="control">
            <button class="button is-link" @click="login">Login</button>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                name: '',
                password: '',
                errors: {
                    name: []
                }
            }
        },
        created() {
            if (this.$root.user) {
                this.$router.push({ name: 'snippets.index' })
            }
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
                    this.showLoginSuccessMessage({ message: `Welcome ${response.data.name}!` })
                    Event.$emit('login', response.data)
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
        notifications: {
            showLoginSuccessMessage: {
                title: 'Successful login.',
                message: 'welcome',
                type: 'success'
            }
        }
    }
</script>
