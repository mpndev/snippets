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
    }
}

export default Auth
