import Vue from 'vue';
import Vuex from 'vuex';
import API from './api';

Vue.use(Vuex);

/**
 * Finds all the children for a parent node
 * @param {Object} root
 * @param {Array} data
 */
function buildTree(root, data)
{
    let children = data.filter(e => e.child_of === root.id);
    if (children.length) {
        Object.assign(root, { children: children });
        root.children.map(e => {
            buildTree(e, data);
            return e;
        });
    }
}

const store = new Vuex.Store({
    state: {
        user: JSON.parse(localStorage.getItem('user')),
        products: [],
        documents: [],
        news: [],
    },
    getters: {
        currentUser: state => state.user,
        products: state => state.products,
        documents: state => state.documents,
        news: state => state.news
    },
    mutations: {
        setAuth(state, user) {
            state.user = user;
            localStorage.setItem('user', JSON.stringify(user));
        },
        removeAuth(state) {
            state.user = null,
            localStorage.removeItem('user');
        },
        setProducts(state, products) {
            console.log('Building Products tree...');
            const pTree = products.filter(p => !p.child_of).map(p => {
                buildTree(p, products);
                return p;
            });
            console.log('Done!');
            state.products = pTree;
        },
        setDocuments(state, docs) {
            state.documents = docs;
        },
        setNews(state, news) {
            state.news = news;
        }
    },
    actions: {
        loginUser({commit}, {email, password}) {
            return API.login(email, password).then(res => {
                const user = res.data;
                commit('setAuth', user);
                return Promise.resolve();
            }).catch(e => {
                return Promise.reject(e);
            })
        },
        registerUser({commit}, credentials) {
            return API.register(credentials).then(res => {
                return Promise.resolve();
            }).catch(e => {
                return Promise.reject(e);
            })
        },
        resetUserPassword({commit}, data) {
            return API.resetPassword(data.email, data.token).then(() => {
                return Promise.resolve();
            }).catch(e => {
                return Promise.reject(e);
            });
        },
        logoutUser({commit}) {
            commit('removeAuth');
            return Promise.resolve();
        },
        fetchProducts({ commit }) {
            return API.getProducts()
                .then(res => {
                    const productList = res.data;
                    commit('setProducts', productList);
                    return Promise.resolve();
                }).catch(e => {
                    return Promise.reject(e);
                });
        },
        fetchDocuments({ commit }) {
            return API.getDocuments()
                .then(res => {
                    const docs = res.data;
                    commit('setDocuments', docs);
                    return Promise.resolve();
                }).catch(e => {
                    return Promise.reject(e);
                })
        },
        fetchNews({ commit }) {
            return API.getNews()
                .then(res => {
                    const news = res.data;
                    commit('setNews', news);
                    return Promise.resolve();
                }).catch(e => {
                    return Promise.reject(e);
                })
        },
        getCSFRToken({commit}, route) {
            return API.getCsfrToken(route).then(res => {
                return Promise.resolve(res.data._token);
            }).catch(e => {
                return Promise.reject(e);
            });
        },
        subscribeToProduct({commit}, productId) {
            return API.subscribeToProduct(productId).then(res => {
                return Promise.resolve();
            }).catch(e => {
                return Promise.reject(e);
            });
        },
        unsubscribeFromProduct({commit}, productId) {
            return API.unsubscribeToProduct(productId).then(res => {
                return Promise.resolve();
            }).catch(e => {
                return Promise.reject(e);
            });
        }
    }
});

export default function initStore()
{
    return store;
}