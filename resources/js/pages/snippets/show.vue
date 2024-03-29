<style>
    .brake-url-to-fit {
        overflow-wrap: break-word;
        word-wrap: break-word;
        word-break: break-word;
        hyphens: auto;
    }
    .brake-url-to-fit:hover {
        background-color: #80808073;
    }
    a.darkmod:hover {
        background-color: #b9b9b9;
    }
    .tags {
        align-items: normal !important;
    }
</style>

<template>
    <div>
        <not-found v-if="not_found"></not-found>
        <div v-if="!not_found" class="columns">
            <div class="column is-3">
                <div :class="{ 'darkmod': Auth.isDarkMod() }" class="box background-is-white text-is-grey-dark">
                    <div v-if="snippet.id">
                        <button v-if="!snippet.public && Auth.check() && Auth.user.id === snippet.user_id" class="button is-danger fa fa-lock" :title="$t('This snippet is visible only to you!')"></button>
                        <button v-if="snippet.public && Auth.check() && Auth.user.id === snippet.user_id" class="button is-warning fa fa-lock" :title="$t('This snippet is visible to everyone!')"></button>
                        <button class="button is-success fa fa-clipboard" :title="$t('copy code to the clipboard')" @click="copy"></button>
                        <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-warning fa fa-edit" :title="$t('edit the snippet')" @click="edit(snippet)"></button>
                        <button v-if="Auth.check() && Auth.user.id == snippet.user_id" class="button is-danger fa fa-trash-alt" :title="$t('delete the snippet')" @click="destroy(snippet)"></button>
                        <button v-if="Auth.check()" :class="{ 'darkmod': Auth.isDarkMod() }" class="button is-dark fas fa-code-branch" :title="$t('fork the snippet')" @click="createFork(snippet)"></button>
                        <button v-if="Auth.check() && Auth.isFavoriteSnippet(snippet)" class="button is-danger fas fa-heart is-outlined" :title="$t('remove from favorite')" @click="removeFromFavoriteSnippets(snippet)"></button>
                        <button v-if="Auth.check() && Auth.isNotFavoriteSnippet(snippet)" :class="{ 'darkmod': Auth.isDarkMod() }" class="button is-dark fas fa-heart-broken is-outlined" :title="$t('add to favorite')" @click="addToFavoriteSnippets(snippet)"></button>
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
                            <span v-if="snippet.tags.length > 0" v-for="(tag, tagKey) in snippet.tags" :class="{'ml-1': tagKey === 0}" class="tag is-success is-unselectable" @click="findByTag(tag)">
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
                                <span v-if="(fork.public) || (!fork.public && Auth.check() && Auth.user.id === fork.user_id)"><b>{{ $t('Fork') }}:</b> <a :class="{ 'darkmod': Auth.isDarkMod() }" :href="'/snippets/' + fork.slug">{{ fork.title }}</a></span>
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
                                <span v-if="(snippet.parent.public) || (!snippet.parent.public && Auth.check() && Auth.user.id === snippet.parent.user_id)"><b>{{ $t('Forked from') }}:</b> <a :class="{ 'darkmod': Auth.isDarkMod() }" :href="'/snippets/' + snippet.parent.slug">{{ snippet.parent.title }}</a></span>
                                <span v-else-if="(!snippet.parent.public && Auth.check() && Auth.user.id !== snippet.parent.user_id) || (!snippet.parent.public && !Auth.check())"><b>{{ $t('Forked from') }}:</b> "{{ snippet.parent.title }}" <button style="cursor: auto" class="button is-danger fa fa-lock is-small"></button></span>
                            </p>
                            <p v-if="snippet.parent == undefined"><b>{{ $t('Do not have parent fork') }}</b></p>
                        </div>
                        <ring-loader v-if="!snippet.id" class="is-narrow"></ring-loader>
                    </div>
                </div>
            </div>
            <div class="column is-9">
                <Editor v-if="snippet.id" :snippet="snippet" :options="snippet.settings"></Editor>
            </div>
        </div>
        <div v-if="!not_found" class="columns">
            <div v-if="urls && urls.length" class="column is-3">
                <div :class="{ 'darkmod': Auth.isDarkMod() }" class="box background-is-white text-is-grey-dark">
                    <p><b>{{ $t('Detected URLs in the snippet:') }}</b></p>
                    <ul v-for="url in urls">
                        <li><a :class="{ 'darkmod': Auth.isDarkMod() }" class="brake-url-to-fit" :href="url" target="_blank">{{ url }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div v-if="!not_found" class="columns">
            <div v-if="snippet.public" class="column is-3">
                <div :class="{ 'darkmod': Auth.isDarkMod() }" class="box background-is-white text-is-grey-dark">
                    <share-it :url="current_domain + '/snippets/' + snippet.slug" :targets="['facebook', 'linkedin', 'twitter']" :shareConfig="share_it_settings" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Editor from '../../components/Editor'
    import NotFound from '../../pages/NotFound'

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
        components: {
            Editor: Editor,
            NotFound: NotFound
        },
        data: () => {
            return {
                not_found: false,
                share_it_settings: share_it_settings,
                snippet: {},
                Auth: Auth,
                urls: [],
                current_domain: 'https://www.' + window.location.hostname,
            }
        },
        mounted() {
            document.querySelector('title').innerHTML = this.$t('snippet')
            axios.get('/api/snippets/' + this.$router.currentRoute.params.snippet_id_or_slug + (this.Auth.check() ? '?api_token=' + this.Auth.user.api_token : '')).then(response => {
                response.data.settings = JSON.parse(response.data.settings)
                this.snippet = response.data
                document.querySelectorAll('[rel="canonical"]')[0].href = 'https://www.' + window.location.hostname + '/snippets/' + this.snippet.slug
                this.snippet.settings.readOnly = true
                this.snippet.settings.cursorHeight = 0
                this.snippet.settings.lineNumbers = false
                this.initializeTheme()
                let urls_pattern = /\b((?:[a-z][\w-]+:(?:\/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'".,<>?«»“”‘’]))/ig
                this.urls = [...new Set(this.snippet.body.match(urls_pattern))]
            }).catch(error => {
                if (error.response.status == 404) {
                    this.not_found = true
                    return
                }
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
                this.$router.push({ name: 'snippets.forks.create', params: { snippet_id_or_slug: snippet.slug }})
            },
            addToFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.slug}`, {
                    'api_token': this.Auth.getApiToken()
                }).then(response => {
                    this.Auth.addToFavoriteSnippets(snippet)
                    this.success({message: this.$t('Snippet was added to yours favorite snippets.')})
                }).catch(error => {
                    this.error({message: error.toString()})
                })
            },
            removeFromFavoriteSnippets(snippet) {
                axios.post(`/api/snippets/favorite/${snippet.slug}`, {
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
            }
        },
        metaInfo() {
            return {
                title: this.snippet.title,
                meta: [
                    {
                        name: 'description',
                        content: this.snippet.description
                    },
                    {
                        property: 'og:title',
                        content: this.snippet.title
                    },
                    {
                        property: 'og:site_name',
                        content: 'Snippets'
                    },
                    {
                        property: 'og:description',
                        content: this.snippet.description
                    },
                    {
                        property: 'og:type',
                        content: 'snippet of code'
                    },
                    {
                        property: 'og:url',
                        content: 'https://www.' + window.location.hostname + '/snippets/' + this.snippet.slug
                    },
                    {
                        name: 'robots',
                        content: 'index,follow'
                    }
                ]
            }
        },
        notifications: require('../../GlobalNotifications')
    }
</script>
