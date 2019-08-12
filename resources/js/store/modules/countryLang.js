import axios from 'axios';
import { api } from '~/config';
import * as types from '../mutation-types';
// import { error } from 'vue-i18n/src/util';

/**
 * Initial state
 */
export const state = {
    languages: [],
    token: window.localStorage.getItem('token')
};

/**
 * Mutations
 */
export const mutations = {
    [types.SET_LANG](state, languages) {
        state.languages = state.languages.push(languages);
        console.log('mutations: ', state.languages[1]);
    },

    [types.FETCH_LANG_FAILURE](state) {
        state.languages = [];
        // window.localStorage.removeItem('token');
    }
};

/**
 * Actions
 */
export const actions = {
    async fetchLang({ commit }) {
        try {
            await axios.get(api.path('language'))
                .then(req => {
                    console.log('fetch: ', req.data.data);
                    console.log('fetch: ', typeof req.data.data);
                    commit(types.SET_LANG, req.data.data);
                })
                .catch(e => {
                    console.log('fetchError: ', e);
                });
            // console.log({ data });

        } catch (e) {
            commit(types.FETCH_LANG_FAILURE);
        }
    },

    async addLang({ commit }, payload) {
        try {
            await axios.post(api.path('language'),{payload})
                .then(req => {
                    const result = req.data;
                    console.log('add: ',result);
                    this.fetchUser();
                })
                .catch(e => {
                    console.log(e);
                });

        } catch (e) {
            commit(types.FETCH_LANG_FAILURE);
        }
    },

    async editLang({ commit }, payload) {
        try {
            await axios.put(api.path('language')+'/'+ payload.id ,{payload})
                .then(req => {
                    const result = req.data;
                    console.log('edit: ',result);
                    this.fetchUser();
                })
                .catch(e => {
                    console.log(e);
                });

        } catch (e) {
            commit(types.FETCH_LANG_FAILURE);
        }
    },

    async destroyLang({ commit }, payload) {
        try {
            await axios.delete(api.path('language')+'/'+ payload.id)
                .then(req => {
                    const result = req.data;
                    console.log('delete: ',result);
                    this.fetchUser();
                })
                .catch(e => {
                    console.log(e);
                });

        } catch (e) {
            commit(types.FETCH_LANG_FAILURE);
        }
    }
};

/**
 * Getters
 */
export const getters = {
    languages: state => state.languages,
    check: state => state.languages !== null,
    token: state => state.token
};
