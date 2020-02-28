import Vue from "vue";
import Router from "vue-router";

import Anonymous from "../containers/Anonymous";
import Authenticated from "../containers/Authenticated";
import Login from "../views/Login";

const ForgotPassword = () => import("../views/ForgotPassword");
const ResetPassword = () => import("../views/ResetPassword");
const Dashboard = () => import("../views/Dashboard");
const TargetConfiguration = () => import("../views/TargetConfiguration");
const Permissions = () => import("../views/Permissions");
const UserList = () => import("../views/UserList");
const UserForm = () => import("../views/UserForm");
const PageNotFound = () => import("../views/PageNotFound");

Vue.use(Router);

export const router = new Router({
    mode: "history",
    routes: [
        {
            path: "/",
            component: Anonymous,
            meta: {
                requiresGuest: true,
                title: 'EasternEnterprise(EE)'
            },
            children: [
                {
                    path: "/",
                    redirect: "/login"
                },
                {
                    path: "/login",
                    name: "Login",
                    component: Login,
                    meta: {
                        title: 'Login'
                    }
                },
                {
                    name: "forgot-Password",
                    path: "/forgot-password",
                    component: ForgotPassword,
                    meta: {
                        title: 'Forgot password'
                    }
                },
                {
                    name: "reset-Password",
                    path: "/reset-password",
                    component: ResetPassword,
                    meta: {
                        title: 'Reset password'
                    }
                }
            ]
        },
        {
            path: "/app",
            component: Authenticated,
            meta: {
                requiresAuth: true
            },
            children: [
                {
                    name: "Dashboard",
                    path: "/dashboard",
                    component: Dashboard,
                    meta: {
                        title: 'Dashboard',
                        moduleName: 'dashboard'
                    }
                },
                {
                    name: "UserList",
                    path: "/users",
                    component: UserList,
                    meta: {
                        requiresAdmin: true,
                        title: 'User list',
                        moduleName: 'user'
                    }
                },
                {
                    name: "AddUser",
                    path: "/user",
                    component: UserForm,
                    meta: {
                        requiresAdmin: true,
                        title: 'Add user',
                        moduleName: 'user'
                    }
                },
                {
                    name: "EditUser",
                    path: "/user/:id",
                    component: UserForm,
                    meta: {
                        requiresAdmin: true,
                        title: 'Update user',
                        moduleName: 'user'
                    }
                },
                {
                    name: "TargetConfiguration",
                    path: "/target-configuration",
                    component: TargetConfiguration,
                    meta: {
                        title: 'TargetConfiguration',
                        moduleName: 'targetConfiguration'
                    }
                },
                {
                    name: "Permissions",
                    path: "/permissions",
                    component: Permissions,
                    meta: {
                        title: 'Permissions',
                        moduleName: 'permissions'
                    }
                }
            ]
        },
        {
            name: "pagenotfound",
            path: "*",
            component: PageNotFound
        }
    ]
});

router.beforeEach((to, from, next) => {
    // redirect to login page if not logged in and trying to access a restricted page
    if (to.matched.some(record => record.meta.requiresAuth)) {
        const loggedIn = localStorage.getItem("token");
        if (!loggedIn) {
            return next("/login");
        }
    }
    if (to.matched.some(record => record.meta.requiresGuest)) {
        const loggedIn = localStorage.getItem("token");
        if (loggedIn) {
            return next("/dashboard");
        }
    }
    if (to.matched.some(record => record.meta.requiresAdmin)) {
        const roleId = localStorage.getItem("role");
        if (roleId !== '1' && roleId !== '3') {
            return next("/dashboard");
        }
    }
    next();
});

// Title
router.beforeEach((to, from, next) => {
    // This goes through the matched routes from last to first, finding the closest route with a title.
    // eg. if we have /some/deep/nested/route and /some, /deep, and /nested have titles, nested's will be chosen.
    const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title);

    // Find the nearest route element with meta tags.
    const nearestWithMeta = to.matched.slice().reverse().find(r => r.meta && r.meta.metaTags);

    // If a route with a title was found, set the document (page) title to that value.
    if (nearestWithTitle) document.title = nearestWithTitle.meta.title;

    // Remove any stale meta tags from the document using the key attribute we set below.
    Array.from(document.querySelectorAll('[data-vue-router-controlled]')).map(el => el.parentNode.removeChild(el));

    // Skip rendering meta tags if there are none.
    if (!nearestWithMeta) return next();

    // Turn the meta tag definitions into actual elements in the head.
    nearestWithMeta.meta.metaTags.map(tagDef => {
        const tag = document.createElement('meta');

        Object.keys(tagDef).forEach(key => {
            tag.setAttribute(key, tagDef[key]);
        });

        // We use this to track which meta tags we create, so we don't interfere with other ones.
        tag.setAttribute('data-vue-router-controlled', '');

        return tag;
    })
    // Add the meta tags to the document head.
        .forEach(tag => document.head.appendChild(tag));

    next();
});
