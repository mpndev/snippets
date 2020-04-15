<template>
    <aside v-if="most_copied_snippets.length" class="menu">
        <p class="menu-label title is-6">
            Top 5 copied snippets:
        </p>
        <ul class="menu-list">
            <li v-for="snippet in most_copied_snippets">
                <a class="title is-7" :href="`/snippets/${snippet.id}`">
                    <span class="tag is-warning is-inline"><span class="fa fa-clipboard has-text-success"></span> X {{ snippet.times_copied }}</span>
                    {{ snippet.title }}
                </a>
            </li>
        </ul>
    </aside>
    <ring-loader v-else ring-loader class="column is-narrow"></ring-loader>
</template>

<script>
    export default {
        props: ['most_copied_snippets']
    }
</script>
