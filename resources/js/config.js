const siteUrl = Laravel.siteUrl,
    apiUrl = Laravel.apiUrl;

export const settings = {
    siteName: Laravel.siteName
};

class URL {
    constructor(base) {
        this.base = base;
    }

    path(path, args) {
        path = path.split('.');
        let obj = this,
            url = this.base;

        for (let i = 0; i < path.length && obj; i++) {
            if (obj.url) {
                url += '/' + obj.url;
            }

            obj = obj[path[i]];
        }
        if (obj) {
            url = url + '/' + (typeof obj === 'string' ? obj : obj.url);
        }

        if (args) {
            for (let key in args) {
                url = url.replace(':' + key, args[key]);
            }
        }

        return url;
    }
}

export const api = Object.assign(new URL(apiUrl), {
    url: 'v1',

    login: {
        url: 'login',
        refresh: 'refresh'
    },

    logout: 'logout',

    register: 'register',

    password: {
        url: 'password',
        forgot: 'email',
        reset: 'reset'
    },

    orderMask: {
        url: 'order/masks'
    },

    me: 'me',

    users: {
        url: 'users',

        activate: ':id/activate',
        single: ':id',
        restore: ':id/restore'
    },

    profile: {
        url: 'profile'
    },

    dashboard: {
        url: 'dashboard'
    },

    language: {
        url: 'language'
    },

    languages: {
        url: 'languages'
    },

    statuses: {
        url: 'statuses'
    },

    status: {
        url: 'status'
    },

    contents: {
        url: 'contents'
    },
    content: {
        url: 'content'
    },

    menus: {
        url: 'menus'
    },
    menu: {
        url: 'menu'
    },

    modelings: {
        url: 'modelings'
    },
    modeling: {
        url: 'modeling'
    },

    executors: 'executors',
    executor: 'executor',

    printings: {
        url: 'printings'
    },

    printing: {
        url: 'printing'
    },

    countries: {
        url: 'countries'
    },

    sizes: {
        url: 'sizes'
    },
});
