import SnippetsIndex from './pages/snippets/index'
import SnippetsCreate from './pages/snippets/create'
import SnippetsEdit from './pages/snippets/edit'
import SnippetsShow from './pages/snippets/show'
import SnippetsForksCreate from './pages/snippets/forks/create'
import LoginGithubCallback from './pages/login/github/callback'
import LoginGoogleCallback from './pages/login/google/callback'
import LoginFacebookCallback from './pages/login/facebook/callback'
import FacebookPrivacyPolicy from './pages/login/facebook/privacy-policy'
import FacebookDeleteUserData from './pages/login/facebook/delete-user-data'
import UsersShow from './pages/users/show'
import LoginCreate from './pages/login/create'
import PasswordReset from './pages/password/reset'
import PasswordForget from './pages/password/forget'
import RegisterCreate from './pages/register/create'
import Tags from './pages/tags/index'
import NotFound from './pages/NotFound'

export default {
    mode: 'history',
    routes: [
        {
            path: '/',
            component: SnippetsIndex,
            name: 'snippets.index'
        },
        {
            path: '/users/:user',
            component: UsersShow,
            name: 'users.show',
            beforeEnter: (to, from, next) => {
                if (Auth.guest()) {
                    next({ name: 'login.create' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/snippets/create',
            component: SnippetsCreate,
            name: 'snippets.create',
            beforeEnter: (to, from, next) => {
                if (Auth.guest()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/snippets/:snippet_id_or_slug/edit',
            component: SnippetsEdit,
            name: 'snippets.edit',
            beforeEnter: (to, from, next) => {
                if (Auth.guest()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/snippets/:snippet_id_or_slug',
            component: SnippetsShow,
            name: 'snippets.show',
            beforeEnter: (to, from, next) => {
                next()
            }
        },
        {
            path: '/snippets/:snippet_id_or_slug/forks/create',
            component: SnippetsForksCreate,
            name: 'snippets.forks.create',
            beforeEnter: (to, from, next) => {
                if (Auth.guest()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/login/github/callback',
            component: LoginGithubCallback,
            name: 'login.github.callback',
            beforeEnter: (to, from, next) => {
                if (Auth.check()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/login/google/callback',
            component: LoginGoogleCallback,
            name: 'login.google.callback',
            beforeEnter: (to, from, next) => {
                if (Auth.check()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/login/facebook/callback',
            component: LoginFacebookCallback,
            name: 'login.facebook.callback',
            beforeEnter: (to, from, next) => {
                if (Auth.check()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/login/facebook/privacy-policy',
            component: FacebookPrivacyPolicy,
            name: 'login.facebook.privacy-policy'
        },
        {
            path: '/login/facebook/delete-user-data',
            component: FacebookDeleteUserData,
            name: 'login.facebook.delete-user-data'
        },
        {
            path: '/login',
            component: LoginCreate,
            name: 'login.create',
            beforeEnter: (to, from, next) => {
                if (Auth.check()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/register',
            component: RegisterCreate,
            name: 'register.create',
            beforeEnter: (to, from, next) => {
                if (Auth.check()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/password-reset',
            component: PasswordReset,
            name: 'password.reset',
            beforeEnter: (to, from, next) => {
                if (Auth.check()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/password-forget',
            component: PasswordForget,
            name: 'password.forget',
            beforeEnter: (to, from, next) => {
                if (Auth.check()) {
                    next({ name: 'snippets.index' })
                }
                else {
                    next()
                }
            }
        },
        {
            path: '/tags',
            component: Tags,
            name: 'tags.index',
            beforeEnter: (to, from, next) => {
                next()
            }
        },
        {
            path: '*',
            name: 'not.found',
            component: NotFound,
            beforeEnter: (to, from, next) => {
                next()
            }
        }
    ]
}
