<template>
    <div id="register-page" :class="{'h-100': done}" class="d-flex flex-column align-items-center justify-content-center bg-primary pt-4 pb-4">
        <div class="container d-flex flex-column align-items-center">
            <div class="card p-4 shadow w-30">
                <form v-if="!done" @submit.prevent="handleSubmit" class="d-flex flex-column">
                    <div  class="d-flex flex-column align-items-center flex-grow-1">
                        <img src="~/assets/images/logo.png" class="img-fluid mb-2" alt="" style="max-width: 150px">
                        <h3 class="mb-3">{{ $t('register.title') }}</h3>
                        <div class="feedback mb-2">
                            <Loader color="#f9ce1c" :loading="inProgress"></Loader>
                            <small class="text-danger" v-if="error"><i class="fa fa-info-circle"></i> {{ error }}</small>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user-circle"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Full name"
                                v-model="credentials.name" required autofocus>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user-circle"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Company"
                                v-model="credentials.enterprise" required>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-user-circle"></i>
                                </span>
                            </div>
                            <select class="select form-control" v-model="credentials.travelAgency">
                                <option value="tour_operator" selected>Tour Operator</option>
				                <option value="travel_agency">Travel Agency</option>
                                <option value="incentive_agency">Incentive Agency / MICE</option>
                                <option value="incoming_agency">Incoming Agency</option>
                            </select>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-globe"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Website"
                                v-model="credentials.web">
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-globe-europe"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" placeholder="Country"
                                v-model="credentials.country" required>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </div>
                            <input type="email" class="form-control" placeholder="Email"
                                v-model="credentials.email" required autofocus>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" class="form-control" placeholder="Password"
                                v-model="credentials.password" required>
                        </div>
                        <div class="form-group input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </div>
                            <input type="password" class="form-control" placeholder="Repeat Password"
                                v-model="passwordRepeat" required>
                        </div>
                        <router-link class="mt-2" to="/reset-password">{{ $t('register.no_confirm_email') }}</router-link>
                        <br>
                        <button class="btn btn-primary mt-3" type="submit">{{ $t('general.register') }}</button>
                        <br>
                    </div>
                    <div class="d-flex align-items-center mt-4 flex-column">
                        <span class="mr-2">{{ $t('register.have_account') }}</span>
                        <router-link to="/login" tag="button" class="btn btn-secondary">{{ $t('general.login') }}</router-link>
                    </div>
                </form>
                <div class="d-flex flex-column" v-else>
                    <div class="d-flex flex-column align-items-center flex-grow-1">
                        <img src="~/assets/images/logo.png" class="img-fluid mb-2" alt="" style="max-width: 150px">
                        <h3 class="mb-3">{{ $t('register.done_title') }}</h3>
                        <p>{{ $t('register.done_text') }}</p>
                    </div>
                    <div class="d-flex align-items-center mt-4 justify-content-center">
                        <router-link to="/login" tag="button" class="btn btn-secondary">{{ $t('general.login') }}</router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex';
import Loader from 'vue-spinner/src/PulseLoader.vue';

export default {
    data() {
        return {
            credentials: {
                name: '',
                enterprise: '',
                travelAgency: 'travel_agency',
                web: '',
                country: '',
                email: '',
                password: '',
                csfrToken: ''
            },
            passwordRepeat: '',
            inProgress: false,
            error: null,
            done: false
        }
    },
    components: {
        Loader
    },
    computed: {
        
    },
    methods: {
        ...mapActions(['registerUser', 'getCSFRToken']),
        handleSubmit() {
            this.inProgress = true;
            this.error = null;
            if (this.credentials.password !== this.passwordRepeat) {
                this.inProgress = false;
                this.error = "Passwords do not match!";
                return;
            }
            this.registerUser(this.credentials).then(() => {
                this.done = true;
                this.inProgress = false;
            }).catch(e => {
                console.error(e);
                this.inProgress = false;
                this.error = e.message;
            })
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.getCSFRToken('register').then(token => {
                this.credentials.csfrToken = token;
            }).catch(e => {
                console.error('Error getting the CSFR token for this form.')
            });
        });
    }
}
</script>

<style lang="scss" scoped>
#register-page {
    background-image: url('~/assets/images/hero-loading.jpg');
}
</style>