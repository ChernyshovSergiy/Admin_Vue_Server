import Vue from 'vue';
import Vuetify, { VSnackbar, VBtn, VIcon } from 'vuetify/lib';
import VuetifyToast from 'vuetify-toast-snackbar';
import FlagIcon from 'vue-flag-icon';
// import VueI18n from 'vue-i18n';

Vue.use(FlagIcon);
// Vue.use(VueI18n);

// const messages = {
//     en: {
//         $vuetify: {
//             dataIterator: {
//                 rowsPerPageText: 'Items per page:',
//                 pageText: '{0}-{1} of {2}'
//             }
//         }
//     },
//     ru: {
//         $vuetify: {
//             dataIterator: {
//                 rowsPerPageText: 'Элементов на страницу:',
//                 pageText: '{0}-{1} из {2}'
//             }
//         }
//     }
// };
Vue.use(Vuetify, {
    components: {
        VSnackbar,
        VBtn,
        VIcon
    }
});
Vue.use(VuetifyToast);

export default new Vuetify(
    // {
    //     lang: {
    //         t: (key, ...params) => VueI18n.t(key, params)
    //     }
    // },
    // messages
);
