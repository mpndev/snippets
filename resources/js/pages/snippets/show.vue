<template>
    <div>
        <div class="columns">
            <div class="column is-3">
                <div class="box">
                    <div v-if="snippet.id">
                        <button class="button is-success fa fa-clipboard" title="copy code to the clipboard" @click="copy"></button>
                        <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-warning fa fa-edit" title="edit the snippet" @click="edit(snippet)"></button>
                        <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-danger fa fa-trash-alt" title="delete the snippet" @click="destroy(snippet)"></button>
                        <button v-if="Auth.check()" class="button is-dark fas fa-code-branch" title="fork the snippet" @click="createFork(snippet)"></button>
                        <button v-if="Auth.check() && Auth.user.favorite_snippets.some(favorite_snippet => favorite_snippet.id === snippet.id)" class="button is-danger fas fa-heart is-outlined" title="remove from favorite" @click="removeFromFavoriteSnippets(snippet)"></button>
                        <button v-if="Auth.check() && Auth.user.favorite_snippets.every(favorite_snippet => favorite_snippet.id != snippet.id)" class="button is-dark fas fa-heart-broken is-outlined" title="add to favorite" @click="addToFavoriteSnippets(snippet)"></button>
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
                Auth: Auth
            }
        },
        mounted() {
            axios.get('/api/snippets/' + this.$router.currentRoute.params.snippet).then(response => {
                this.snippet = response.data
            }).catch(error => {
                this.$router.push({ name: 'snippets.index' })
                this.error({message: error.toString()})
            })
        },
        methods: {
            copy() {
                this.$copyText(this.snippet.body).then(() => {
                    this.success({message: 'Copied to clipbord.'})
                }, () => {
                    this.warn({message: 'Maybe your browser do not allow this.'})
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
                        axios.post('/api/snippets/' + snippet.id + '?api_token=' + this.Auth.user.api_token, {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$router.push({ name: 'snippets.index' })
                            this.success({message: 'Snippet was successful deleted. Also all of his tag and fans.'})
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
                    this.error({message: error.toString()})
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
        notifications: require('../../GlobalNotifications')
    }
</script>
