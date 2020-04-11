<template>
    <div>
        <search></search>

        <paginator v-if="has_results" :paginated_data="paginated_data"></paginator>
        <div v-if="show_rings" class="box">
            <div class="columns is-centered">
                <ring-loader class="column is-narrow"></ring-loader>
            </div>
        </div>

        <div class="columns">
            <div class="column is-2">
                <div class="box">
                    <most-liked-snippets :most_liked_snippets="most_liked_snippets"></most-liked-snippets>
                </div>
            </div>
            <div class="column">
                <div class="box">
                    <div v-if="has_results" v-for="snippet in paginated_data.data" class="box has-background-light">
                        <snippet-summary :key="snippet.id" :snippet="snippet" @snippet-was-deleted="snippetWasDeleted" @favorite-was-changed="updateMostLikedSnippets"></snippet-summary>
                    </div>
                    <div v-if="!has_results" class="columns is-centered">
                        <div v-if="!show_rings" class="column">
                            <div class="colun"></div>
                            <p class="title has-text-centered is-3">No results found.</p>
                        </div>
                    </div>
                    <div v-if="show_rings" v-for="i in 5" class="columns is-centered">
                        <ring-loader class="column is-narrow"></ring-loader>
                    </div>
                </div>
            </div>
        </div>

        <paginator v-if="has_results" :paginated_data="paginated_data"></paginator>
        <div v-if="show_rings" class="box">
            <div class="columns is-centered">
                <ring-loader class="column is-narrow"></ring-loader>
            </div>
        </div>
    </div>
</template>

<script>
    import Paginator from './../../components/Paginator'
    import SnippetSummary from './../../components/SnippetSummary'
    import Search from "./../../components/Search";
    import MostLikedSnippets from "./../../components/MostLikedSnippets";
    export default {
        components: {
            SnippetSummary: SnippetSummary,
            Paginator: Paginator,
            search: Search,
            MostLikedSnippets: MostLikedSnippets
        },
        data: () => {
            return {
                Auth: Auth,
                paginated_data: {
                    data: []
                },
                most_liked_snippets: [],
                has_results: false,
                show_rings: true
            }
        },
        mounted() {
            let query = this.$router.currentRoute.fullPath.substr(this.$router.currentRoute.path)
            let api_token_param = ''
            if (this.Auth.check()) {
                api_token_param = 'api_token=' + this.Auth.user.api_token
                if (query.length > 1) {
                    query = query + '&' + api_token_param
                }
                else {
                    query = '/?' + api_token_param
                }
            }
            axios.get('/api/snippets' + query).then(response => {
                this.paginated_data = response.data
                if (this.paginated_data.data.length) {
                    this.has_results = true
                }
                this.show_rings = false
            }).catch(error => {
                this.error({message: error.toString()})
            })
            axios('/api/snippets?limit=5&most-liked-snippets=true').then(response => {
                this.most_liked_snippets = response.data.data
            })
        },
        methods: {
            snippetWasDeleted(id) {
                if (this.paginated_data.data.length > 1) {
                    this.$router.go(0)
                }
                else {
                    this.$router.push({ name: 'snippets.index' })
                }
            },
            updateMostLikedSnippets() {
                axios('/api/snippets?limit=5&most-liked-snippets=true').then(response => {
                    this.most_liked_snippets = response.data.data
                })
            }
        },
        notifications: require('../../GlobalNotifications')
    }
    // компонент за бутоните
    // codemirror за едитабъл код (create/edit снипет)
    // бутон за модал съдържащ настройките на бутона
    // форм модел
    // сниппет модел
</script>
