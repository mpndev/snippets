<style>
    .snippet-preview {
        min-height: 140px;
    }
</style>

<template>
    <div class="columns snippet-preview">
        <div class="column">
            <div>
                <p>
                    <a :href="`/snippets/${snippet.id}`" class="title has-text-dark is-6">{{ snippet.title }}</a>
                </p>
                <p>
                    <a :href="`/snippets/${snippet.id}`" class="title has-text-dark is-7">{{ parsed_description }}</a>
                </p>
                <p class="tags">
                    <span v-for="tag in sortedTags" class="tag title is-7 is-success is-unselectable has-cursor-pointer" @click="findByTag(tag)">{{ tag.name }}</span>
                </p>
                <p class="is-unselectable">
                    <span class="tag title is-7 has-background-grey-lighter fa fa-clock">
                        <span class="has-cursor-pointer" @click="findByDay">&nbsp;{{ snippet.created_at_for_humans }}</span>
                        <span class="has-cursor-pointer" @click="findByAuthor">&nbsp;by {{ snippet.user.name }}</span>
                    </span>
                </p>
            </div>
        </div>
        <div class="column is-narrow">
            <button class="button is-success fa fa-clipboard is-small" title="copy code to the clipboard" @click="copy"></button>
            <button class="button is-info fa fa-eye is-small" title="see the snippet" @click="show(snippet)"></button>
            <button v-if="Auth.check() && Auth.isOwner(snippet)" class="button is-warning fa fa-edit is-small" title="edit the snippet" @click="edit(snippet)"></button>
            <button v-if="Auth.check() && Auth.isOwner(snippet)" class="button is-danger fa fa-trash-alt is-small" title="delete the snippet" @click="destroy(snippet)"></button>
            <button v-if="Auth.check()" class="button is-dark fas fa-code-branch is-small" title="fork the snippet" @click="createFork(snippet)"></button>
            <button v-if="Auth.check() && Auth.isFavoriteSnippet(snippet)" class="button is-danger fas fa-heart is-outlined is-small" title="remove from favorite" @click="removeFromFavoriteSnippets(snippet)"></button>
            <button v-if="Auth.check() && Auth.isNotFavoriteSnippet(snippet)" class="button is-dark fas fa-heart-broken is-outlined is-small" title="add to favorite" @click="addToFavoriteSnippets(snippet)"></button>
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
                return (this.snippet.description.length > 170) ? this.snippet.description.substring(0, 167) + '...' : this.snippet.description
            }
        },
        methods: {
            copy() {
                this.$copyText(this.snippet.body).then(() => {
                    this.success({message: 'Copied to clipbord.'})
                    axios.post(`/api/snippets/actions/copy/${this.snippet.id}`, {
                        '_method': 'PUT'
                    }).then(response => {
                        this.$emit('snippet-was-copied', response.data)
                    })
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
                        axios.post('/api/snippets/' + snippet.id + '?api_token=' + this.Auth.getApiToken(), {
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
                    'api_token': this.Auth.getApiToken()
                }).then(response => {
                    this.Auth.addToFavoriteSnippets(snippet)
                    this.success({message: 'Snippet was added to yours favorite snippets.'})
                    this.$emit('favorite-was-changed', snippet)
                }).catch(error => {
                    this.error({
                        message: error.toString()
                    })
                })
            },
            removeFromFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.id}`, {
                    'api_token': this.Auth.getApiToken(),
                    '_method': 'DELETE'
                }).then(response => {
                    this.Auth.removeFromFavoriteSnippets(snippet)
                    this.success({message: 'Snippet was removed from yours favorite snippets.'})
                    this.$emit('favorite-was-changed', snippet)
                }).catch(error => {
                    this.error({message: error.toString()})
                })
            },
            findByAuthor() {
                this.$router.push({ name: 'snippets.index', query: { "snippets-by-author": this.snippet.user.id } })
            },
            findByDay() {
                this.$router.push({ name: 'snippets.index', query: { "snippets-created-at-the-same-day-as": this.snippet.id } })
            }
        },
        notifications: require('../GlobalNotifications')
    }
</script>
