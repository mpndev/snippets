const Auth = {
    user: localStorage.user ? JSON.parse(localStorage.user) : null,
    update: function(data) {
        this.user = data
        localStorage.user = JSON.stringify(this.user)
    },
    check: function() {
        return !!this.user
    },
    guest: function() {
        return !this.check()
    },
    logout: function() {
        this.update(null)
    },
    hasAbility: function(ability) {
      return this.user.abilities.includes(ability);
    },
    getParsedSettings: function() {
        return JSON.parse(this.user.settings)
    },
    getStringifiedSettings: function() {
        return this.user.settings
    },
    setStringifiedSettings: function(settings) {
        this.user.settings = JSON.stringify(settings)
        this.update(this.user)
    },
    addToFavoriteSnippets: function(snippet) {
        this.user.favorite_snippets.push(snippet)
        this.update(this.user)
    },
    removeFromFavoriteSnippets: function(snippet) {
        this.user.favorite_snippets = this.user.favorite_snippets.filter(s => s.id !== snippet.id)
        this.update(this.user)
    },
    getApiToken: function() {
        return this.user.api_token
    },
    isOwner: function(snippet) {
        return this.user.id === snippet.user.id
    },
    getName: function() {
        return this.user.name
    },
    isFavoriteSnippet: function(snippet) {
        return this.user.favorite_snippets.some(favorite_snippet => favorite_snippet.id === snippet.id)
    },
    isNotFavoriteSnippet: function(snippet) {
        return this.user.favorite_snippets.every(favorite_snippet => favorite_snippet.id !== snippet.id)
    },
    setDarkmod: function(isDarkmod) {
        this.user.darkmod = isDarkmod
        this.update(this.user)
    },
    isDarkMod: function() {
        if (!this.user) {
            return false
        }
        return !!this.user.darkmod
    }
}

export default Auth
