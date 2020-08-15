<template>
    <div id="login-page" class="d-flex flex-column align-items-center justify-content-center h-100">
        
        <div class="container d-flex flex-column align-items-center">
            <div class="card p-4 shadow w-30">
                <Dropdown @select="onLangOptions" icon="fa fa-language" class="text-primary" :title="currentLocale" :options="menuOptions" style="text-align: center"></Dropdown>

                <form @submit.prevent="handleSubmit" class="d-flex flex-column">
                    <div class="d-flex flex-column align-items-center flex-grow-1 text-center">
                        <img src="~/assets/images/logo.png" class="img-fluid mb-2" alt="" style="max-width: 150px">
                        <h3 class="mb-3">{{ $t('login.title') }}</h3>
                        <div class="feedback mb-2">
                            <Loader color="#f9ce1c" :loading="inProgress"></Loader>
                            <small class="text-danger" v-if="error"><i class="fa fa-info-circle"></i> {{ error }}</small>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control" placeholder="Email"
                                v-model="email" required autofocus>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" class="form-control" placeholder="Password"
                                v-model="password" required>
                        </div>
                        <router-link class="mt-2" to="/reset-password">{{ $t('login.forgot_password') }}</router-link>
                        <br>
                        <button class="btn btn-primary mt-3" type="submit">{{ $t('general.login') }}</button>
                        <br>
                    </div>
                    <div class="d-flex align-items-center mt-4 flex-md-column">
                        <span class="mr-2">{{ $t('login.no_account') }}</span>
                        <router-link to="/register" tag="button" class="btn btn-secondary">{{ $t('general.register') }}</router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
import Loader from 'vue-spinner/src/PulseLoader.vue';
import Dropdown from '~/components/Dropdown.vue';

import { setLocale } from '~/i18n.js';

export default {
    data() {
        return {
            email: '',
            password: '',
            inProgress: false,
            error: undefined
        }
    },
    components: {
        Loader,
        Dropdown
    },
    methods: {
        ...mapActions(['loginUser']),
        handleSubmit() {
            this.inProgress = true;
            this.error = undefined;
            this.loginUser({email: this.email, password: this.password}).then(() => {
                this.inProgress = false;
                this.$router.push('/');
            }).catch(e => {
                console.error(e);
                this.inProgress = false;
                if (e.response) {
                    switch (e.response.status) {
                        case 401:
                            this.error = 'Invalid Email or Password'
                            break;
                        case 403:
                            this.error = 'User not approved yet. Wait for confirmation email.'
                            break;
                        default:
                            this.error = 'Unknown error. Please try again.'
                            break;
                    }
                } else {
                    this.error = 'Network error. Check your internet connection.';
                }
            });
        },
        onLangOptions(option) {
            setLocale(option);
           // this.$router.go();
        }
    },
    computed: {
        currentLocale() {
            return this.$i18n.locale;
        },
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
    }
}
</script>

<style lang="scss" scoped>
#login-page {
    background-image: url('~/assets/images/hero-loading.jpg');
}
</style>