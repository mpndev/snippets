<template>
    <div>
        <div v-if="!sended">
            <div class="box has-text-centered">
                <b>{{ $t('Once you hit the "Send" button, we will send you email that contains a link, from witch you can set your new password.') }}</b>
            </div>
            <div class="field">
                <label class="label">{{ $t('Email') }}</label>
                <div class="control">
                    <input class="input" type="email" v-model="email" :placeholder="$t('Email')" @focusin="resetErrors">
                    <p class="has-text-danger" v-for="error in errors.email">{{ error }}</p>
                </div>
            </div>
            <div class="field">
                <div class="control">
                    <button class="button is-link" @click="send">{{ $t('Send') }}</button>
                </div>
            </div>
        </div>
        <div v-if="sended">
            <div class="box has-text-centered">
                <b>{{ $t('You can close this page and check your mail box in order to set your new password.') }} ({{ email }})</b>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                Auth: Auth,
                email: '',
                errors: {
                    email: [],
                },
                sended: false
            }
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('forget password')
        },
        methods: {
            resetErrors() {
                this.errors.email = []
            },
            validateFields() {
                if (this.email.length == 0) {
                    this.errors.email.push(this.$t('Please provide email.'));
                }
                else if (!String(this.email).toLowerCase().match(/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i)) {
                    this.errors.email.push(this.$t('Please provide valid email.'));
                }
            },
            send() {
                this.resetErrors()

                this.validateFields()

                if (this.errors.email.length) {
                    return;
                }

                axios.post('/api/password-forget', {
                    email: this.email
                }).then(response => {
                    this.success({
                        message: 'Check your email! (' + this.email + ')',
                    })
                    this.sended = true
                }).catch(error => {
                    this.success({
                        message: 'Check your email! (' + this.email + ')',
                    })
                    this.sended = true
                })
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
