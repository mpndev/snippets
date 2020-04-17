<style>
    .most-liked-snippets-ring {
        min-height: 12.5rem
    }
</style>

<template>
    <aside v-if="most_liked_snippets.length" class="menu">
        <p class="menu-label title is-6">
            Top 5 liked snippets:
        </p>
        <ul class="menu-list">
            <li v-for="snippet in most_liked_snippets">
                <a class="title is-7" :href="`/snippets/${snippet.id}`">
                    <span class="tag is-success is-inline"><span class="fa fa-heart has-text-danger"></span> X {{ snippet.fans_quantity }}</span>
                    {{ snippet.title }}
                </a>
            </li>
        </ul>
    </aside>
    <ring-loader v-else ring-loader class="column most-liked-snippets-ring is-narrow"></ring-loader>
</template>

<script>
    export default {
        props: ['most_liked_snippets']
    }
</script>
