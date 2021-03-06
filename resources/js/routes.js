import SnippetsIndex from './pages/snippets/index'
import SnippetsCreate from './pages/snippets/create'
import SnippetsEdit from './pages/snippets/edit'
import SnippetsShow from './pages/snippets/show'
import SnippetsForksCreate from './pages/snippets/forks/create'
import LoginCreate from './pages/login/create'
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
            path: '/snippets/:snippet/edit',
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
            path: '/snippets/:snippet',
            component: SnippetsShow,
            name: 'snippets.show',
            beforeEnter: (to, from, next) => {
                next()
            }
        },
        {
            path: '/snippets/:snippet/forks/create',
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
