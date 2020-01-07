<template>
    <div id="reset-password-page" class="d-flex flex-column align-items-center justify-content-center bg-primary h-100">
        <div class="container d-flex flex-column align-items-center">
            <div class="card p-4 shadow w-30">
                <form @submit.prevent="handleSubmit" class="d-flex flex-column">
                    <div v-if="!done" class="d-flex flex-column align-items-center flex-grow-1 text-center">
                        <img src="~/assets/images/logo.png" class="img-fluid mb-2" alt="" style="max-width: 150px">
                        <h3 class="mb-3">{{ $t('password_reset.title') }}</h3>
                        <p>{{ $t('password_reset.subtitle') }}</p>
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
                        <br>
                        <button class="btn btn-primary mt-3" type="submit">{{ $t('password_reset.reset') }}</button>
                        <br>
                    </div>
                    <div class="d-flex flex-column align-items-center flex-grow-1 text-center" v-else>
                        <img src="~/assets/images/logo.png" class="img-fluid mb-2" alt="" style="max-width: 150px">
                        <h3 class="mb-3">{{ $t('password_reset.reset_completed') }}</h3>
                        <p>{{ $t('password_reset.login_with_credentials') }}</p>
                        <br>
                        <router-link to="/login">{{ $t('general.login') }}</router-link>
                        <br>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Loader from 'vue-spinner/src/PulseLoader.vue';
import { mapActions } from 'vuex';

export default {
    data() {
        return {
            email: '',
            inProgress: false,
            error: null,
            done: false,
            csfrToken: ''
        }
    },
    components: {
        Loader
    },
    methods: {
        ...mapActions(['resetUserPassword', 'getCSFRToken']),
        handleSubmit() {
            this.inProgress = true;
            this.error = null;
            this.resetUserPassword({email: this.email, token: this.csfrToken}).then(() => {
                this.inProgress = false;
                this.done = true;
            }).catch(e => {
                this.inProgress = false;
                this.error = e.message;
            });
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.getCSFRToken('reset-password').then(token => {
                this.csfrToken = token;
            }).catch(e => {
                console.error('Error getting the CSFR token for this form.')
            });
        });
    }
}
</script>

<style lang="scss" scoped>
#reset-password-page {
    background-image: url('~/assets/images/hero-loading.jpg');
}
</style>