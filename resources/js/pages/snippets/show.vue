<template>
    <div>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div v-if="snippet.id">
                        <button class="button is-success fa fa-clipboard" title="copy code to the clipboard" @click="copy"></button>
                        <button v-if="user && user.id == snippet.user_id" class="button is-warning fa fa-edit" title="edit the snippet" @click="edit(snippet)"></button>
                        <button v-if="user && user.id == snippet.user_id" class="button is-danger fa fa-trash-alt" title="delete the snippet" @click="destroy(snippet)"></button>
                        <button v-if="user" class="button is-dark fas fa-code-branch" title="fork the snippet" @click="createFork(snippet)"></button>
                        <button v-if="user && user.favorite_snippets.some(favorite_snippet => favorite_snippet.id === snippet.id)" class="button is-danger fas fa-heart is-outlined" title="remove from favorite" @click="removeFromFavoriteSnippets(snippet)"></button>
                        <button v-if="user && user.favorite_snippets.every(favorite_snippet => favorite_snippet.id != snippet.id)" class="button is-dark fas fa-heart-broken is-outlined" title="add to favorite" @click="addToFavoriteSnippets(snippet)"></button>
                    </div>
                    <ring-loader v-else class="is-narrow"></ring-loader>
                    <hr>
                    <div>
                        <p v-if="snippet.user"><b>Author:</b> {{ snippet.user.name }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>Title:</b> {{ snippet.title }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>Description:</b> {{ snippet.description }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>Created: </b> {{ snippet.created_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <p v-if="snippet.id"><b>Last update: </b> {{ snippet.updated_at_for_humans }}</p>
                        <ring-loader v-else class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.tags" class="tags">
                            <span><b>Tags:</b></span>
                            <span v-if="snippet.tags.length > 0" v-for="(tag, tag_index) in snippet.tags" class="tag is-success">
                                {{ tag.name }}
                            </span>
                            <span v-if="snippet.tags.length == 0">0</span>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.forks_quantity > 0" v-for="fork in snippet.forks"><b>Fork:</b> <a :href="'/snippets/' + fork.id">{{ fork.title }}</a></p>
                            <p v-if="snippet.forks_quantity == 0"><b>Forks:</b>0</p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                    <hr>
                    <div>
                        <div v-if="snippet.id">
                            <p v-if="snippet.parent"><b>Forked from:</b> <a :href="'/snippets/' + snippet.parent.id">{{ snippet.parent.title }}</a></p>
                            <p v-if="snippet.parent == undefined"><b>Do not have parent fork</b></p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                </div>
            </div>
            <div class="column is-9">
                <pre v-if="snippet.id" class="box"><code>{{ snippet.body }}</code></pre>
                <ring-loader v-else class="column is-narrow"></ring-loader>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data: () => {
            return {
                snippet: {},
                user: null
            }
        },
        created() {
            this.user = this.$root.user
        },
        mounted() {
            axios.get('/api/snippets/' + this.$router.currentRoute.params.snippet).then(response => {
                this.snippet = response.data
            }).catch(error => {
                this.$router.push({ name: 'snippets.index' })
                this.failToLoadSnippet({
                    message: error.toString()
                })
            })
        },
        methods: {
            copy() {
                this.$copyText(this.snippet.body).then(() => {
                    this.snippetWasCopied({
                        title: 'Copied to clipbord.',
                    })
                }, () => {
                    this.snippetWasNotCopied({
                        title: 'Cannot copy snippet.',
                        message: 'Maybe your browser do not allow this.'
                    })
                })
            },
            edit(snippet) {
                this.$router.push({ name: 'snippets.edit', params: { snippet: snippet.id }})
            },
            destroy(snippet) {
                Event.$emit('show-message', {
                    message: 'Do you confirm deletion?',
                    type: 'warning',
                    callback: () => {
                        axios.post('/api/snippets/' + snippet.id + '?api_token=' + this.user.api_token, {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$router.push({ name: 'snippets.index' })
                            this.successfulDeletedSnippet({
                                message: 'Snippet was successful deleted. Also all of his tag and fans.'
                            })
                        }).catch(error => {
                            this.failedToDeleteSnippet({
                                message: error.toString()
                            })
                        })
                    }
                })
            },
            createFork(snippet) {
                this.$router.push({ name: 'snippets.forks.create', params: { snippet: snippet.id }})
            },
            addToFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.id}`, {
                    'api_token': this.user.api_token
                }).then(response => {
                    this.user.favorite_snippets.push(snippet)
                    this.$root.user = this.user
                    localStorage.user = JSON.stringify(this.$root.user)
                    this.successfulAddingSnippetToFavorite()
                }).catch(error => {
                    this.failingAddingSnippetToFavorite({
                        message: error.toString()
                    })
                })
            },
            removeFromFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.id}`, {
                    'api_token': this.user.api_token,
                    '_method': 'DELETE'
                }).then(response => {
                    this.user.favorite_snippets = this.user.favorite_snippets.filter(s => s.id != snippet.id)
                    this.$root.user = this.user
                    localStorage.user = JSON.stringify(this.$root.user)
                    this.successfulRemovingSnippetFromFavorite()
                }).catch(error => {
                    this.failingRemovingSnippetFromFavorite({
                        message: error.toString()
                    })
                })
            }
        },
        notifications: {
            failToLoadSnippet: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            snippetWasCopied: {
                title: '',
                message: '',
                type: 'success'
            },
            snippetWasNotCopied: {
                title: '',
                message: '',
                type: 'warn'
            },
            successfulDeletedSnippet: {
                title: 'Success!',
                message: '',
                type: 'success'
            },
            failedToDeleteSnippet: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            successfulAddingSnippetToFavorite: {
                title: 'Success!',
                message: 'Snippet was added to your favorite snippets',
                type: 'success'
            },
            failingAddingSnippetToFavorite: {
                title: 'Error!',
                message: '',
                type: 'error'
            },
            successfulRemovingSnippetFromFavorite: {
                title: 'Success!',
                message: 'Snippet was removed from your favorite snippets',
                type: 'success'
            },
            failingRemovingSnippetFromFavorite: {
                title: 'Error!',
                message: '',
                type: 'error'
            }
        }
    }
</script>
