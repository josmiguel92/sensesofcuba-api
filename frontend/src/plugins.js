import LazyLoad from 'vue-lazyload';
import loadingImg from '~/assets/images/loading.gif';
import clickOutside from './directives/click-outside';

export default {
    install(Vue) {
        Vue.use(LazyLoad, {
            preLoad: 1.3,
            loading: loadingImg
        });
        Vue.directive('click-outside', clickOutside);
    }
}