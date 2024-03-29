<style>
    .snippet-preview {
        min-height: 140px;
    }
</style>

<template>
    <div class="columns snippet-preview">
        <div class="column is-9">
            <div>
                <p>
                    <a :class="{ 'darkmod': Auth.isDarkMod() }" :href="`/snippets/${snippet.slug}`" class="title text-is-dark is-6">
                        <text-highlight :queries="($route.query && $route.query.search) ? [$route.query.search] : []">{{ snippet.title }}</text-highlight>
                    </a>
                </p>
                <hr class="is-marginless is-hidden-desktop">
                <p>
                    <a :class="{ 'darkmod': Auth.isDarkMod() }" :href="`/snippets/${snippet.slug}`" class="title text-is-dark is-7">
                        <text-highlight :queries="($route.query && $route.query.search) ? [$route.query.search] : []">{{ parsed_description }}</text-highlight>
                    </a>
                </p>
                <p class="tags">
                    <span v-for="tag in sortedTags" class="tag title is-7 is-success is-unselectable has-cursor-pointer" @click="findByTag(tag)">{{ tag.name }}</span>
                </p>
                <p class="is-unselectable">
                    <span class="tag title is-7 background-is-grey-lighter fa fa-clock">
                        <span class="has-cursor-pointer" @click="findByDay">&nbsp;{{ snippet.created_at_for_humans }}</span>
                        <span class="has-cursor-pointer" @click="findByAuthor">&nbsp;{{ $t('by') }} {{ snippet.user.name }}</span>
                    </span>
                </p>
            </div>
        </div>
        <div class="column is-3">
            <div class="is-full-width is-justify-content-flex-end is-flex is-flex-wrap-wrap">
                <button v-if="!snippet.public && Auth.check() && Auth.user.id === snippet.user_id" class="button is-danger fa fa-lock is-small ml-1 mt-1" :title="$t('This snippet is visible only to you!')"></button>
                <button class="button is-success fa fa-clipboard is-small ml-1 mt-1" :title="$t('copy code to the clipboard')" @click="copy"></button>
                <button class="button is-info fa fa-eye is-small ml-1 mt-1" :title="$t('see the snippet')" @click="show(snippet)"></button>
                <button v-if="Auth.check() && Auth.isOwner(snippet)" class="button is-warning fa fa-edit is-small ml-1 mt-1" :title="$t('edit the snippet')" @click="edit(snippet)"></button>
                <button v-if="Auth.check() && Auth.isOwner(snippet)" class="button is-danger fa fa-trash-alt is-small ml-1 mt-1" :title="$t('delete the snippet')" @click="destroy(snippet)"></button>
                <button v-if="Auth.check()" :class="{ 'darkmod': Auth.isDarkMod() }" class="button is-dark fas fa-code-branch is-small ml-1 mt-1" :title="$t('fork the snippet')" @click="createFork(snippet)"></button>
                <button v-if="Auth.check() && Auth.isFavoriteSnippet(snippet)" class="button is-danger fas fa-heart is-outlined is-small ml-1 mt-1" :title="$t('remove from favorite')" @click="removeFromFavoriteSnippets(snippet)"></button>
                <button v-if="Auth.check() && Auth.isNotFavoriteSnippet(snippet)" :class="{ 'darkmod': Auth.isDarkMod() }" class="button is-dark fas fa-heart-broken is-outlined is-small ml-1 mt-1" :title="$t('add to favorite')" @click="addToFavoriteSnippets(snippet)"></button>
            </div>
            <div v-if="snippet.public" class="is-full-width is-justify-content-flex-end is-flex mt-5">
                <share-it :url="current_domain + '/snippets/' + snippet.slug" :targets="['facebook', 'linkedin', 'twitter']" :shareConfig="share_it_settings" />
            </div>
        </div>
    </div>
</template>

<script>
    const share_it_settings = {
        facebook: {
            size: "sm",
            icon: true,
            round: false,
            dense: true,
            outline: true,
            color: '#4267B2',
        },
        linkedin: {
            size: "sm",
            icon: true,
            round: false,
            dense: true,
            outline: true,
            color: '#2867B2',
        },
        twitter: {
            size: "sm",
            icon: true,
            round: false,
            dense: true,
            outline: true,
            color: '#1DA1F2',
        },
    }
    export default {
        props: ['snippet'],
        data: () => {
            return {
                share_it_settings: share_it_settings,
                Auth: Auth,
                current_domain: 'https://www.' + window.location.hostname,
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
                    this.$parent.success({message: this.$t('Copied to clipbord.')})
                    axios.post(`/api/snippets/actions/copy/${this.snippet.slug}`, {
                        '_method': 'PUT'
                    }).then(response => {
                        this.$emit('snippet-was-copied', response.data)
                    })
                }, () => {
                    this.$parent.warn({message: `${this.$t('Cannot copy snippet.')} ${this.$t('Maybe your browser do not allow this.')}`})
                })
            },
            show(snippet) {
                this.$router.push({ name: 'snippets.show', params: { snippet_id_or_slug: snippet.slug} })
            },
            edit(snippet) {
                this.$router.push({ name: 'snippets.edit', params: { snippet_id_or_slug: snippet.slug }})
            },
            destroy(snippet) {
                Event.$emit('show-message', {
                    message: this.$t('Do you confirm deletion?'),
                    type: 'warning',
                    callback: () => {
                        axios.post('/api/snippets/' + snippet.slug + '?api_token=' + this.Auth.getApiToken(), {
                            _method: 'DELETE'
                        }).then(response => {
                            this.$emit('snippet-was-deleted', snippet.id)
                            this.$parent.success({message: this.$t('Snippet is deleted.')})
                        }).catch(error => {
                            this.$parent.error({message: error.toString()})
                        })
                    }
                })
            },
            findByTag(tag) {
                this.$router.push({ name: 'snippets.index', query: { "with-tags": tag.name, page: 1 } })
            },
            createFork(snippet) {
                this.$router.push({ name: 'snippets.forks.create', params: { snippet_id_or_slug: snippet.slug }})
            },
            addToFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.slug}`, {
                    'api_token': this.Auth.getApiToken()
                }).then(response => {
                    this.Auth.addToFavoriteSnippets(snippet)
                    this.$parent.success({message: this.$t('Snippet was added to yours favorite snippets.')})
                    this.$emit('favorite-was-changed', snippet)
                }).catch(error => {
                    this.$parent.error({
                        message: error.toString()
                    })
                })
            },
            removeFromFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.slug}`, {
                    'api_token': this.Auth.getApiToken(),
                    '_method': 'DELETE'
                }).then(response => {
                    this.Auth.removeFromFavoriteSnippets(snippet)
                    this.$parent.success({message: this.$t('Snippet was removed from yours favorite snippets.')})
                    this.$emit('favorite-was-changed', snippet)
                }).catch(error => {
                    this.$parent.error({message: error.toString()})
                })
            },
            findByAuthor() {
                this.$router.push({ name: 'snippets.index', query: { "snippets-by-author": this.snippet.user.id } })
            },
            findByDay() {
                this.$router.push({ name: 'snippets.index', query: { "snippets-created-at-the-same-day-as": this.snippet.id } })
            }
        }
    }
</script>
