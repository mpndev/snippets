<style>
    @font-face {
        font-family: NotoColorEmojiLimited;
        unicode-range: U+1F1E6-1F1FF;
        src: url("/assets/fonts/NotoColorEmoji.ttf") format('truetype'),
        url("/assets/fonts/NotoEmoji-Regular.ttf") format('truetype'),
        url("/assets/fonts/NotoColorEmoji_WindowsCompatible.ttf") format('truetype');
    }
    .language-swicher-icon {
        font-size: 150%;
        position: relative;
        font-family: 'NotoColorEmojiLimited', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol';
    }
    .modal-languages {
        width: 100%;
        position: absolute;
        top: 0;
        right: 0;
        z-index: 999999;
    }
    .selected {
        border: 1px solid blue;
    }
</style>

<template>
    <a class="language-swicher-icon" @click.prevent.stop="openLanguageSwitcherModal" @mouseleave="showLanguages = false">
        {{ languages[currentLangcode] }}
        <div v-if="showLanguages" class="modal-languages has-background-primary	">
            <ul>
                <li v-for="(flag, language) in languages" :class="{'selected': currentLangcode === language}" @click="setLangcode(language)">{{ flag }}</li>
            </ul>
        </div>
    </a>
</template>

<script>
    export default {
        data() {
            return {
                languages: {
                    en: '🇺🇸',
                    bg: '🇧🇬',
                },
                currentLangcode: null,
                showLanguages: false
            }
        },
        mounted() {
            this.currentLangcode = this.$i18n.locale
        },
        methods: {
            openLanguageSwitcherModal() {
                this.showLanguages = !this.showLanguages
            },
            setLangcode(language) {
                this.$i18n.locale = language
                this.currentLangcode = language
                localStorage.setItem('language', language)
                this.showLanguages = !this.showLanguages
            }
        }
    }
</script>
