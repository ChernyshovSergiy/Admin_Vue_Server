export default [
    ...applyRules(
        ['guest'],
        [
            {
                path: '',
                component: require('$comp/auth/AuthWrapper').default,
                redirect: { name: 'login' },
                children: [
                    {
                        path: '/login',
                        name: 'login',
                        component: require('$comp/auth/login/Login').default
                    },
                    {
                        path: '/register',
                        name: 'register',
                        component: require('$comp/auth/register/Register')
                            .default
                    },
                    {
                        path: '/password',
                        component: require('$comp/auth/password/PasswordWrapper')
                            .default,
                        children: [
                            {
                                path: '',
                                name: 'forgot',
                                component: require('$comp/auth/password/password-forgot/PasswordForgot')
                                    .default
                            },
                            {
                                path: 'reset/:token',
                                name: 'reset',
                                component: require('$comp/auth/password/password-reset/PasswordReset')
                                    .default
                            }
                        ]
                    }
                ]
            }
        ]
    ),
    ...applyRules(
        ['auth'],
        [
            {
                path: '',
                component: require('$comp/admin/AdminWrapper').default,
                children: [
                    {
                        path: '',
                        name: 'index',
                        redirect: { name: 'dashboard' }
                    },
                    {
                        path: '/dashboard',
                        name: 'dashboard',
                        component: require('$comp/admin/dashboard/Dashboard')
                            .default
                    },
                    {
                        path: '/orders',
                        component: require('$comp/admin/orders/OrdersWrapper')
                            .default,
                        children: [
                            {
                                path: 'modeling_orders',
                                name: 'modeling_orders',
                                component: require('$comp/admin/orders/modeling/ModelingOrders')
                                    .default
                            },
                            {
                                path: 'printing_orders',
                                name: 'printing_orders',
                                component: require('$comp/admin/orders/printing/PrintingOrders')
                                    .default
                            },
                            {
                                path: 'painting_orders',
                                name: 'painting_orders',
                                component: require('$comp/admin/orders/painting/PaintingOrders')
                                    .default
                            }
                        ]
                    },
                    {
                        path: '/menus',
                        name: 'menus',
                        component: require('$comp/admin/menu/Menu')
                            .default
                    },
                    {
                        path: '/contents',
                        name: 'contents',
                        component: require('$comp/admin/content/Contents')
                            .default
                    },
                    {
                        path: '/components',
                        component: require('$comp/admin/components/ComponentsWrapper')
                            .default,
                        children: [
                            {
                                path: 'languages',
                                name: 'languages',
                                component: require('$comp/admin/components/languages/Languages')
                                    .default
                            },
                            {
                                path: 'order_status',
                                name: 'order_status',
                                component: require('$comp/admin/components/order_status/OrderStatuses')
                                    .default
                            }
                        ]
                    },
                    {
                        path: 'profile',
                        component: require('$comp/admin/profile/ProfileWrapper')
                            .default,
                        children: [
                            {
                                path: '',
                                name: 'profile',
                                component: require('$comp/admin/profile/Profile')
                                    .default
                            },
                            {
                                path: 'edit',
                                name: 'profile-edit',
                                component: require('$comp/admin/profile/edit/ProfileEdit')
                                    .default
                            }
                        ]
                    }
                ]
            }
        ]
    ),
    { path: '*', redirect: { name: 'index' } }
];

function applyRules(rules, routes) {
    for (let i in routes) {
        routes[i].meta = routes[i].meta || {};

        if (!routes[i].meta.rules) {
            routes[i].meta.rules = [];
        }
        routes[i].meta.rules.unshift(...rules);

        if (routes[i].children) {
            routes[i].children = applyRules(rules, routes[i].children);
        }
    }

    return routes;
}
