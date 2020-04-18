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
            name: 'snippets.create'
        },
        {
            path: '/snippets/:snippet/edit',
            component: SnippetsEdit,
            name: 'snippets.edit'
        },
        {
            path: '/snippets/:snippet',
            component: SnippetsShow,
            name: 'snippets.show'
        },
        {
            path: '/snippets/:snippet/forks/create',
            component: SnippetsForksCreate,
            name: 'snippets.forks.create'
        },
        {
            path: '/login',
            component: LoginCreate,
            name: 'login.create'
        },
        {
            path: '/register',
            component: RegisterCreate,
            name: 'register.create'
        },
        {
            path: '/tags',
            component: Tags,
            name: 'tags.index'
        },
        {
            path: '*',
            component: NotFound
        }
    ]
}
