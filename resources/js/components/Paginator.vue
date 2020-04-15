<template>
    <div v-if="paginated_data.data.length > 4" class="box">
        <nav class="pagination is-right" role="navigation" aria-label="pagination">
            <span class="tag is-light is-large is-unselectable">{{ paginated_data.from }} - {{ paginated_data.to }} / {{ paginated_data.total }}</span>
            <ul class="pagination-list">
                <li v-if="paginated_data.current_page > 2">
                    <span class="pagination-link" aria-label="first page" @click="changePage(1)">first page</span>
                </li>
                <li v-if="paginated_data.current_page > 2">
                    <span class="pagination-ellipsis">&hellip;</span>
                </li>
                <li v-if="paginated_data.current_page > 2">
                    <span class="pagination-link" @click="changePage(paginated_data.current_page - 1)">previous page</span>
                </li>
                <li v-if="paginated_data.current_page > 2">
                    <span class="pagination-link" :aria-label="paginated_data.current_page - 1" @click="changePage(paginated_data.current_page - 2)">{{ paginated_data.current_page - 2 }}</span>
                </li>

                <li v-if="paginated_data.current_page > 1">
                    <span class="pagination-link" :aria-label="paginated_data.current_page - 1" @click="changePage(paginated_data.current_page - 1)">{{ paginated_data.current_page - 1 }}</span>
                </li>

                <li>
                    <span class="pagination-link is-current" :aria-label="'Page ' + paginated_data.current_page" aria-current="page" @click="changePage(paginated_data.current_page)">{{ paginated_data.current_page }}</span>
                </li>

                <li v-if="paginated_data.current_page < paginated_data.last_page">
                    <span class="pagination-link" :aria-label="paginated_data.current_page + 1" @click="changePage(paginated_data.current_page + 1)">{{ paginated_data.current_page + 1 }}</span>
                </li>

                <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                    <span class="pagination-link" :aria-label="paginated_data.current_page + 2" @click="changePage(paginated_data.current_page + 2)">{{ paginated_data.current_page + 2 }}</span>
                </li>
                <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                    <span class="pagination-link" :area-label="'next page'" @click="changePage(paginated_data.current_page + 1)">next page</span>
                </li>
                <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                    <span class="pagination-ellipsis">&hellip;</span>
                </li>
                <li v-if="paginated_data.current_page < (paginated_data.last_page - 1)">
                    <span class="pagination-link" aria-label="last page" @click="changePage(paginated_data.last_page)">last page</span>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    export default {
        props: ['paginated_data'],
        methods: {
            changePage(to) {
                let query = {...this.$route.query}
                query.page = to
                this.$router.replace({ name: 'snippets.index', query: query}).catch(e => {})
            }
        }
    }
</script>
