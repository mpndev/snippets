<template>
    <div>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div v-if="snippet.id">
                        <button v-if="!snippet.public && Auth.check() && Auth.user.id === snippet.user_id" class="button is-danger fa fa-lock" :title="$t('This snippet is visible only to you!')"></button>
                        <button v-if="snippet.public && Auth.check() && Auth.user.id === snippet.user_id" class="button is-warning fa fa-lock" :title="$t('This snippet is visible to everyone!')"></button>
                        <button class="button is-success fa fa-clipboard" :title="$t('copy code to the clipboard')" @click="copy"></button>
                        <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-warning fa fa-edit" :title="$t('edit the snippet')" @click="edit(snippet)"></button>
                        <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-danger fa fa-trash-alt" :title="$t('delete the snippet')" @click="destroy(snippet)"></button>
                        <button v-if="Auth.check()" class="button is-dark fas fa-code-branch" :title="$t('fork the snippet')" @click="createFork(snippet)"></button>
                        <button v-if="Auth.check() && Auth.isFavoriteSnippet(snippet)" class="button is-danger fas fa-heart is-outlined" :title="$t('remove from favorite')" @click="removeFromFavoriteSnippets(snippet)"></button>
                        <button v-if="Auth.check() && Auth.isNotFavoriteSnippet(snippet)" class="button is-dark fas fa-heart-broken is-outlined" :title="$t('add to favorite')" @click="addToFavoriteSnippets(snippet)"></button>
                    </div>
                    <ring-loader v-else class="is-narrow"></ring-loader>
                    <hr>
                    <div>
                        <p v-if="snippet.user"><b>{{ $t('Author') }}:</b> {{ snippet.user.name }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>{{ $t('Title') }}:</b> {{ snippet.title }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>{{ $t('Description') }}:</b> {{ snippet.description }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>{{ $t('Created') }}: </b> {{ snippet.created_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>{{ $t('Last update') }}: </b> {{ snippet.updated_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.tags" class="tags">
                            <span><b>{{ $t('Tags') }}:</b></span>
                            <span v-if="snippet.tags.length > 0" v-for="tag in snippet.tags" class="tag is-success is-unselectable" @click="findByTag(tag)">
                                {{ tag.name }}
                            </span>
                            <span v-if="snippet.tags.length == 0">0</span>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.forks_quantity > 0" v-for="fork in snippet.forks">
                                <span v-if="(fork.public) || (!fork.public && Auth.check() && Auth.user.id === fork.user_id)"><b>{{ $t('Fork') }}:</b> <a :href="'/snippets/' + fork.id">{{ fork.title }}</a></span>
                                <span v-else-if="(!fork.public && Auth.check() && Auth.user.id !== fork.user_id) || (!fork.public && !Auth.check())"><b>{{ $t('Fork') }}:</b> "{{ fork.title }}" <button style="cursor: auto" class="button is-danger fa fa-lock is-small"></button></span>
                            </p>
                            <p v-if="snippet.forks_quantity == 0"><b>{{ $t('Forks') }}:</b>0</p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.parent">
                                <span v-if="(snippet.parent.public) || (!snippet.parent.public && Auth.check() && Auth.user.id === snippet.parent.user_id)"><b>{{ $t('Forked from') }}:</b> <a :href="'/snippets/' + snippet.parent.id">{{ snippet.parent.title }}</a></span>
                                <span v-else-if="(!snippet.parent.public && Auth.check() && Auth.user.id !== snippet.parent.user_id) || (!snippet.parent.public && !Auth.check())"><b>{{ $t('Forked from') }}:</b> "{{ snippet.parent.title }}" <button style="cursor: auto" class="button is-danger fa fa-lock is-small"></button></span>
                            </p>
                            <p v-if="snippet.parent == undefined"><b>{{ $t('Do not have parent fork') }}</b></p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                </div>
            </div>
            <div class="column is-9" @mousedown.ctrl="runIfLink" @click.meta="runIfLink">
                <Editor v-if="snippet.id" :snippet="snippet" :options="snippet.settings"></Editor>
            </div>
        </div>
    </div>
</template>

<script>
    import Editor from '../../components/Editor'

    export default {
        components: {
            Editor: Editor
        },
        data: () => {
            return {
                snippet: {},
                Auth: Auth
            }
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('snippet')
            axios.get('/api/snippets/' + this.$router.currentRoute.params.snippet + (this.Auth.check() ? '?api_token=' + this.Auth.user.api_token : '')).then(response => {
                response.data.settings = JSON.parse(response.data.settings)
                this.snippet = response.data
                this.snippet.settings.readOnly = true
                this.snippet.settings.cursorHeight = 0
                this.snippet.settings.lineNumbers = false
                this.initializeTheme()
            }).catch(error => {
                this.$router.push({ name: 'snippets.index' })
                this.error({message: error.toString()})
            })
        },
        methods: {
            copy() {
                this.$copyText(this.snippet.body).then(() => {
                    this.success({message: this.$t('Copied to clipbord.')})
                }, () => {
                    this.warn({message: this.$t('Maybe your browser do not allow this.')})
                })
            },
            edit(snippet) {
                this.$router.push({ name: 'snippets.edit', params: { snippet: snippet.id }})
            },
            destroy(snippet) {
                Event.$emit('show-message', {
                    message: this.$t('Do you confirm deletion?'),
                    type: 'warning',
                    callback: () => {
                        axios.post('/api/snippets/' + snippet.id + '?api_token=' + this.Auth.getApiToken(), {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$router.push({ name: 'snippets.index' })
                            this.success({message: this.$t('Snippet was successful deleted. Also all of his tag and fans.')})
                        }).catch(error => {
                            this.error({message: error.toString()})
                        })
                    }
                })
            },
            findByTag(tag) {
                this.$router.push({ name: 'snippets.index', query: { "with-tags": tag.name, page: 1 } })
            },
            createFork(snippet) {
                this.$router.push({ name: 'snippets.forks.create', params: { snippet: snippet.id }})
            },
            addToFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.id}`, {
                    'api_token': this.Auth.getApiToken()
                }).then(response => {
                    this.Auth.addToFavoriteSnippets(snippet)
                    this.success({message: this.$t('Snippet was added to yours favorite snippets.')})
                }).catch(error => {
                    this.error({message: error.toString()})
                })
            },
            removeFromFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.id}`, {
                    'api_token': this.Auth.getApiToken(),
                    '_method': 'DELETE'
                }).then(response => {
                    this.Auth.removeFromFavoriteSnippets(snippet)
                    this.success({message: this.$t('Snippet was removed from yours favorite snippets.')})
                }).catch(error => {
                    this.error({message: error.toString()})
                })
            },
            initializeTheme() {
                const theme = this.snippet.settings.theme
                const stylesheet = document.createElement('link')
                stylesheet.setAttribute("data-current-theme", theme)
                stylesheet.setAttribute("rel", "stylesheet")
                stylesheet.setAttribute("type", "text/css")
                stylesheet.setAttribute("href", `/css/themes/${theme}.css`)
                document.getElementsByTagName("head")[0].appendChild(stylesheet);
            },
            runIfLink() {
                this.snippet.body.split(' ').map(word => {
                    if (/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_\+.~#?&//=]*)/.test(word.trim())) {
                        window.open(word, '_blank')
                    }
                })
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
