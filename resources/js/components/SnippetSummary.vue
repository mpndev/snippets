<template>
    <div class="columns">
        <div class="column">
            <div>
                <div>
                    <p>
                        <a :href="`/snippets/${snippet.id}`" class="title has-text-dark is-4">Title: {{ snippet.title }}</a>
                    </p>
                    <p>
                        <a :href="`/snippets/${snippet.id}`" class="title has-text-dark is-6">Description: {{ parsed_description }}</a>
                    </p>
                    <p class="tags">
                        <span v-for="tag in sortedTags" class="tag is-success is-unselectable" @click="findByTag(tag)">{{ tag.name }}</span>
                    </p>
                </div>
            </div>
        </div>
        <div class="column is-narrow">
            <button class="button is-success fa fa-clipboard" title="copy code to the clipboard" @click="copy"></button>
            <button class="button is-info fa fa-eye" title="see the snippet" @click="show(snippet)"></button>
            <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-warning fa fa-edit" title="edit the snippet" @click="edit(snippet)"></button>
            <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-danger fa fa-trash-alt" title="delete the snippet" @click="destroy(snippet)"></button>
            <button v-if="Auth.check()" class="button is-dark fas fa-code-branch" title="fork the snippet" @click="createFork(snippet)"></button>
            <button v-if="Auth.check() && Auth.user.favorite_snippets.some(favorite_snippet => favorite_snippet.id === snippet.id)" class="button is-danger fas fa-heart is-outlined" title="remove from favorite" @click="removeFromFavoriteSnippets(snippet)"></button>
            <button v-if="Auth.check() && Auth.user.favorite_snippets.every(favorite_snippet => favorite_snippet.id != snippet.id)" class="button is-dark fas fa-heart-broken is-outlined" title="add to favorite" @click="addToFavoriteSnippets(snippet)"></button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['snippet'],
        data: () => {
            return {
                Auth: Auth
            }
        },
        computed: {
            sortedTags() {
                return this.snippet.tags.sort((tag1, tag2) => (tag1.name > tag2.name) ? 1 : -1)
            },
            parsed_description() {
                return (this.snippet.description.length > 140) ? this.snippet.description.substring(0, 137) + '...' : this.snippet.description
            }
        },
        methods: {
            copy() {
                this.$copyText(this.snippet.body).then(() => {
                    this.success({message: 'Copied to clipbord.'})
                }, () => {
                    this.warn({message: 'Cannot copy snippet. Maybe your browser do not allow this.'})
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
                        axios.post('/api/snippets/' + snippet.id + '?api_token=' + this.Auth.user.api_token, {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$emit('snippet-was-deleted', snippet.id)
                            this.success({message: 'Snippet is deleted.'})
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
                    'api_token': this.Auth.user.api_token
                }).then(response => {
                    this.Auth.user.favorite_snippets.push(snippet)
                    localStorage.user = JSON.stringify(this.Auth.user)
                    this.success({message: 'Snippet was added to yours favorite snippets.'})
                }).catch(error => {
                    this.error({
                        message: error.toString()
                    })
                })
            },
            removeFromFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.id}`, {
                    'api_token': this.Auth.user.api_token,
                    '_method': 'DELETE'
                }).then(response => {
                    this.Auth.user.favorite_snippets = this.Auth.user.favorite_snippets.filter(s => s.id != snippet.id)
                    localStorage.user = JSON.stringify(this.Auth.user)
                    this.success({message: 'Snippet was removed from yours favorite snippets.'})
                }).catch(error => {
                    this.error({message: error.toString()})
                })
            }
        },
        notifications: require('../GlobalNotifications')
    }
</script>
