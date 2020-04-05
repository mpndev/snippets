<template>
    <div class="columns">
        <div class="column">
            <a :href="`/snippets/${snippet.id}`">
                <p class="title has-text-dark is-4">Title: {{ snippet.title }}</p>
                <p class="title has-text-dark is-6">Description: {{ parsed_description }}</p>
            </a>
        </div>
        <div class="column is-narrow">
            <button class="button is-success fa fa-clipboard" title="copy code to the clipboard" @click="copy"></button>
            <button class="button is-info fa fa-eye" title="see the snippet" @click="show(snippet)"></button>
            <button v-if="user && user.id == snippet.user_id" class="button is-warning fa fa-edit" title="edit the snippet" @click="edit(snippet)"></button>
            <button v-if="user && user.id == snippet.user_id" class="button is-danger fa fa-trash-alt" title="delete the snippet" @click="destroy(snippet)"></button>
            <button v-if="user" class="button is-dark fas fa-code-branch" title="fork the snippet" @click="createFork(snippet)"></button>
            <button v-if="user && user.favorite_snippets.some(favorite_snippet => favorite_snippet.id === snippet.id)" class="button is-danger fas fa-heart is-outlined" title="remove from favorite" @click="removeFromFavoriteSnippets(snippet)"></button>
            <button v-if="user && user.favorite_snippets.every(favorite_snippet => favorite_snippet.id != snippet.id)" class="button is-dark fas fa-heart-broken is-outlined" title="add to favorite" @click="addToFavoriteSnippets(snippet)"></button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['snippet'],
        data: () => {
            return {
                user: null
            }
        },
        created() {
            if (this.$root.user) {
                this.user = this.$root.user
            }
        },
        computed: {
            parsed_description() {
                return (this.snippet.description.length > 140) ? this.snippet.description.substring(0, 137) + '...' : this.snippet.description
            }
        },
        methods: {
            copy() {
                this.$copyText(this.snippet.body).then(() => {
                    this.snippetWasCopied({
                        message: 'Copied to clipbord.'
                    })
                }, () => {
                    this.snippetWasNotCopied({
                        title: 'Cannot copy snippet.',
                        message: 'Maybe your browser do not allow this.'
                    })
                })
            },
            show(snippet) {
                this.$router.push({ name: 'snippets.show', params: { snippet: snippet.id} })
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
                            this.$emit('snippet-was-deleted', snippet.id)
                            this.snippetWasDeleted()
                        }).catch(error => {
                            this.somethingWentWrongWithDeletion({
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
            snippetWasCopied: {
                title: '',
                message: '',
                type: 'success',
            },
            snippetWasNotCopied: {
                title: '',
                message: '',
                type: 'warn',
            },
            snippetWasDeleted: {
                title: 'Success!',
                message: 'Snippet was successful deleted. Also all of his tag and fans.',
                type: 'success'
            },
            somethingWentWrongWithDeletion: {
                title: '',
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
