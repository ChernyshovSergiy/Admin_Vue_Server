import 'babel-polyfill';
import Vue from 'vue';

import router from '~/router/index';
import store from '~/store/index';
import App from '$comp/App';
import '~/plugins/index';
import vuetify from '~/plugins/vuetify';
import { i18n } from '~/plugins/i18n';
import { Trans } from '~/plugins/Translation';
import VueSweetalert2 from '~/plugins/sweetalert2';

Vue.prototype.$i18nRoute = Trans.i18nRoute.bind(Trans);

Vue.config.productionTip = false;

export const app = new Vue({
    i18n,
    router,
    store,
    vuetify,
    VueSweetalert2,
    render: h => h(App)
}).$mount('#app');
