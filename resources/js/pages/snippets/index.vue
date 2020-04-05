<template>
    <div>
        <search></search>

        <div class="box">
            <paginator v-if="has_results" :paginated_data="paginated_data"></paginator>
            <div v-if="show_rings" class="columns is-centered">
                <ring-loader class="column is-narrow"></ring-loader>
            </div>
        </div>

        <div class="box">
            <div v-if="has_results" v-for="snippet in paginated_data.data" class="box has-background-light">
                <snippet-summary :key="snippet.id" :snippet="snippet" @snippet-was-deleted="snippetWasDeleted"></snippet-summary>
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

        <div class="box">
            <paginator v-if="has_results" :paginated_data="paginated_data"></paginator>
            <div v-if="show_rings" class="columns is-centered">
                <ring-loader class="column is-narrow"></ring-loader>
            </div>
        </div>
    </div>
</template>

<script>
    import Paginator from './../../components/Paginator'
    import SnippetSummary from './../../components/SnippetSummary'
    import Search from "./../../components/Search";
    export default {
        components: {
            SnippetSummary: SnippetSummary,
            Paginator: Paginator,
            search: Search
        },
        data: () => {
            return {
                paginated_data: {
                    data: []
                },
                has_results: false,
                show_rings: true
            }
        },
        mounted() {
            let query = this.$router.currentRoute.fullPath.substr(this.$router.currentRoute.path)
            let api_token_param = ''
            if (this.$root.user) {
                api_token_param = 'api_token=' + this.$root.user.api_token
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
                this.somethingWentWrong()
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
            }
        },
        notifications: {
            somethingWentWrong: {
                title: 'Error!',
                message: 'Something went wrong...',
                type: 'error'
            }
        }
    }
</script>
