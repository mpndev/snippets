<template>
    <div>
        <div class="columns is-centered mt-5">
            <div class="column box is-1 has-text-centered">
                <p class="title is-1 has-text-danger">404</p>
            </div>
        </div>
        <div class="columns is-centered">
            <div class="column is-8">
                <p class="box title is-1">{{ $t('Hmmm, the page you looking for is not found.') }}</p>
            </div>
        </div>
        <div class="columns is-centered">
            <div class="column is-4 has-text-centered">
                <span class="box title is-4 has-text-danger" style="text-decoration: line-through;">{{ $router.currentRoute.path }}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            document.querySelector('title').innerHTML = this.$t('404 not found!')
        }
    }
</script>
