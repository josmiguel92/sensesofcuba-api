<template>
    <nav id="navbar" class="navbar navbar-expand-lg navbar-dark" :class="{'bg-dark': toggled}" v-sticky>
        <UserModal :show.sync="showUserModal" @close="showUserModal = false"></UserModal>
        <div class="container">
            <div class="navbar-left d-none d-lg-flex">
                <Dropdown @select="onUserOptions" class="text-primary" icon="fa fa-user-circle" :title="currentUser.username" :options="userOptions"></Dropdown>
                <a role="button" href="/admin" class="btn btn-sm btn-primary d-flex align-items-center m-1">Admin Panel</a>
            </div>
            <a href="#" class="navbar-brand">
                <img style="max-width: 200px" src="~/assets/images/logo2.png" class="">
            </a>
            <button class="navbar-toggler"
                type="button"
                :data-toggled="toggled"
                data-target="menu"
                aria-controls="menu"
                :aria-expanded="toggled"
                @click.stop="toggled = !toggled"
                aria-label="Toggle Navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="menu"
                :class="{show: toggled}">
                <div class="navbar-collapse-header d-flex">
                    <Dropdown @select="onUserOptions" class="text-primary d-lg-none" icon="fa fa-user-circle" :title="currentUser.username" :options="userOptions"></Dropdown>
                    <Dropdown @select="onLangOptions" icon="fa fa-language" class="text-primary" :title="currentLocale" :options="menuOptions"></Dropdown>
                </div>
                <div class="dropdown-divider"></div>
                <ul class="navbar-nav">
                    <li class="nav-item active">
                        <a class="nav-link text-primary text-nowrap" href="#products">{{ $t('home.navbar.links.products') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-primary text-nowrap" href="#documents">{{ $t('home.navbar.links.documents') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-primary text-nowrap" href="#contact">{{ $t('home.navbar.links.contact') }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
import Dropdown from '~/components/Dropdown.vue';
import UserModal from '~/components/UserModal.vue';
import { mapGetters, mapActions } from 'vuex';

import { setLocale } from '~/i18n.js';

export default {
    components: {
        Dropdown,
        UserModal
    },
    data() {
        return {
            toggled: false,
            showUserModal: false
        }
    },
    computed: {
        menuOptions() {
            return [
                {
                    title: this.$t('home.navbar.lang.en'),
                    value: 'en',
                    icon: 'fa fa-language'
                },
                {
                    title: this.$t('home.navbar.lang.de'),
                    value: 'de',
                    icon: 'fa fa-language'
                }
            ]
        },
        userOptions() {
            return [
                {
                    title: this.$t('home.navbar.user.logout'),
                    value: 'logout',
                    icon: 'fa fa-door-open'
                },
                //{
                //    title: 'Options',
                //    value: 'options',
                //    icon: 'fa fa-user-edit'
                //}
            ]
        },
        ...mapGetters(['currentUser']),
        currentLocale() {
            return this.$i18n.locale;
        }
    },
    methods: {
        ...mapActions(['logoutUser']),
        onUserOptions(option) {
            switch (option) {
                case "options":
                    this.showUserModal = true;
                    break;
                case "logout":
                    this.logoutUser();
                    this.$router.push('/login');
                    break;
                default:
                    break;
            }
        },
        onLangOptions(option) {
            setLocale(option);
            this.$router.go();
        }
    },
    directives: {
        sticky: {
            bind: function(el, binding, vnode) {
                el.stickToTop = function(e) {
                    const currentPos = window.pageYOffset || document.documentElement.scrollTop;
                    if (currentPos < 0) return;
                    const navBarHeight = el.clientHeight;
                    if (currentPos < navBarHeight) {
                        el.classList.remove('bg-dark');
                        //el.style.backgroundColor = '#00000080';
                        el.classList.remove('shadow');
                        el.classList.remove('shadow-3');
                    } else {
                        el.classList.add('bg-dark');
                        //el.style.backgroundColor = '#212121';
                        el.classList.add('shadow');
                        el.classList.add('shadow-3');
                    }
                }
                window.addEventListener('scroll', el.stickToTop);
            },
            unbind: function(el, binding, vnode) {
                window.removeEventListener('scroll', el.stickToTop);
            }
        }
    }
    
}
</script>

<style scoped>
#navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background-color: #00000080;
    transition: all 0.35s ease;
}
@media (min-width: 992px) {
    .navbar-left, .navbar-brand, .navbar-collapse {
        width: 33.3333333%;
        text-align: center;
    }
    .navbar-left {
		display: flex;
		justify-content: center;
	}
	.navbar-collapse {
		flex-grow: 0;
		justify-content: center;
	}
}
</style>