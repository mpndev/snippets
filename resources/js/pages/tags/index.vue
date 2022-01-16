<style>
    .pills-wrapper {
        text-align: center;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: baseline;
    }
    .pill-wrapper {
        position: relative;
        margin: 1rem 1rem 10rem 1rem;
        width: 20vw;
        transition: opacity .5s;
    }
    .pill-wrapper > div {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 20vw;
    }
    .pill {
        height: 4.5rem;
        text-transform: uppercase;
        font-size: 3rem;
        text-align: center;
    }
    .pill-top {
        border-radius: 8px 8px 0 0;
        box-shadow: inset 0 0 0 1px #fff;
    }
    .is-tag {
        margin: 0!important;
        padding: 0;
        font-size: 0;
        transition: padding .5s, font-size .5s;
    }
    .pill-bottom {
        border-radius: 0 0 8px 8px;
        box-shadow: inset 0 0 0 1px #fff;
    }
    .is-tag:hover {
        background-color: #00d1b2!important;
    }
    .expand-me {
        padding: 1rem;
        font-size: 1.2rem;
    }
    .blur-me {
        opacity: .5;
    }
    @media (max-width: 640px) {
        .pill-wrapper, .pill-wrapper > div {
            width: 80vw;
        }
    }
</style>

<template>
<div>
    <div v-if="tagsCollection.length">
        <div class="columns is-centered">
            <div class="column is-3">
                <h2 class="box title is-2 has-text-centered has-text-primary">{{ $t('All available tags') }}</h2>
            </div>
        </div>
        <div class="pills-wrapper">
            <div v-for="tags in tagsCollection" class="pill-wrapper background-is-white">
                <div>
                    <div tabindex="0" @focus="expandThePill" @focusout="shrinkThePill" class="background-is-dark text-is-primary pill pill-top">{{tags[0].name.substr(0, 1)}}</div>
                    <div v-for="tag in tags" class="is-tag background-is-white has-text-info has-cursor-pointer" @click="goToSnippetsWithThisTag(tag)">{{tag.name}}</div>
                    <div tabindex="0" @focus="expandThePill" @focusout="shrinkThePill" class="background-is-dark text-is-primary pill pill-bottom">&nbsp;</div>
                </div>
            </div>
        </div>
    </div>
    <div v-else class="box">
        <ring-loader></ring-loader>
    </div>
</div>
</template>

    <script>
export default {
    data: () => {
        return {
            Auth: Auth,
            tags: [],
        }
    },
    mounted() {
        document.querySelector('title').innerHTML = this.$t('tags')
        document.querySelectorAll('[rel="canonical"]')[0].href = 'https://www.' + window.location.hostname + '/tags'
        axios.get('/api/tags' + '?api_token=' + (this.Auth.check() ? this.Auth.getApiToken() : '')).then(response => {
            this.tags = response.data
        })
    },
    computed: {
        tagsCollection() {
            const tagsCollection = []
            for(let letter in this.tags) {
                tagsCollection.push(this.tags[letter])
            }
            return tagsCollection
        }
    },
    methods: {
        goToSnippetsWithThisTag(tag) {
            this.$router.push({name: 'snippets.index', query: {'with-tags': tag.name}})
        },
        expandThePill(e) {
            const target = e.target
            target.parentElement.style.zIndex = '20'
            Array.from(target.parentElement.children).filter(el => {
                if (el.classList.contains('is-tag')) {
                    el.classList.add('expand-me')
                    el.style.zIndex = '20'
                }
            })
            const siblings = Array.from(document.getElementsByClassName('pill-wrapper')).filter(el => el !== target.parentElement.parentElement)
            siblings.forEach(function(el) {
                if (!el.classList.contains('blur-me')) {
                    el.classList.add('blur-me')
                }
            })
        },
        shrinkThePill(e) {
            const target = e.target
            target.parentElement.style.zIndex = '0'
            Array.from(target.parentElement.children).filter(el => {
                if (el.classList.contains('is-tag')) {
                    el.classList.remove('expand-me')
                    el.style.zIndex = '0'
                }
            })
            const siblings = Array.from(document.getElementsByClassName('pill-wrapper')).filter(el => el !== target.parentElement.parentElement)
            siblings.forEach(function(el) {
                if (el.classList.contains('blur-me')) {
                    el.classList.remove('blur-me')
                }
            })
        }
    },
    metaInfo() {
        return {
            title: 'all available snippets tags on the site',
            meta: [
                {
                    name: 'description',
                    content: 'all available snippets tags on the site'
                },
                {
                    property: 'og:title',
                    content: 'all available snippets tags on the site'
                },
                {
                    property: 'og:site_name',
                    content: 'Snippets'
                },
                {
                    property: 'og:description',
                    content: 'all available snippets tags on the site'
                },
                {
                    property: 'og:type',
                    content: 'snippets tags'
                },
                {
                    property: 'og:url',
                    content: 'https://www.' + window.location.hostname + '/tags'
                },
                {
                    name: 'robots',
                    content: 'index,nofollow'
                }
            ]
        }
    },
}
</script>
