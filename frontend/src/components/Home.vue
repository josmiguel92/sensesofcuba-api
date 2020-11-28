<template>
    <div class="home-page">
        <section id="hero" class="jumbotron jumbotron-fluid" v-lazy:background-image="heroImg">
            <div class="container">
                <h1 class="display-3">{{ $t('home.hero.welcome.text1') }} <br>{{ $t('home.hero.welcome.text2') }} <br> <strong class="text-primary text-bold">{{ $t('home.hero.welcome.text3') }}</strong> </h1>
                <p class="lead">{{ $t('home.hero.lead') }}</p>
  		    </div>
            <svg class="section-divider" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
                <path fill="transparent" class="divider-bg" d="M 0 0 L 0 100 L 100 100 L 100 0 Z"></path>
                <path fill="#f9ce1c" stroke="#f9ce1c" stroke-width="2px" class="divider-fg" d="M 0 100 C 40 0 60 0 100 100 Z"></path>
            </svg>
        </section>

        <section id="documents" class="d-flex flex-column align-items-center w-100 bg-light">
            <h3 class="title p-4">{{ $t('home.documents.title.text1') }} <br> <em class="text-primary">{{ $t('home.documents.title.text2') }}</em></h3>
            <PulseLoader :loading="inProgress" color="#212121"></PulseLoader>
            <Documents :items="documents" v-show="!inProgress"></Documents>
        </section>
        <svg class="section-divider" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="transparent" class="divider-bg" d="M 0 0 L 0 100 L 100 100 L 100 0 Z"></path>
            <path fill="#f8f9fa" stroke="#f8f9fa" stroke-width="2px" class="divider-fg" d="M 0 100 C 40 0 60 0 100 100 Z"></path>
        </svg>
        
        <section id="products" class="d-flex flex-column align-items-center w-100 bg-primary p-2">
            <h3 class="title p-4 text-light">{{ $t('home.products.title.text1') }} <br> <em class="text-dark">{{ $t('home.products.title.text2') }}</em></h3>
            <PulseLoader :loading="inProgress" color="#212121"></PulseLoader>
            <ProductGrid :items="products" v-show="!inProgress"></ProductGrid>
        </section>
        <svg class="section-divider" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#f9ce1c" class="divider-bg" d="M 0 0 L 0 100 L 100 100 L 100 0 Z"></path>
            <path fill="#f8f9fa" stroke="#f8f9fa" stroke-width="2px" class="divider-fg" d="M 0 100 C 40 0 60 0 100 100 Z"></path>
        </svg>
        <section id="documents" class="d-flex flex-column align-items-center w-100 bg-light">
            <h3 class="title p-4">{{ $t('home.documents.title.text1') }} <br> <em class="text-primary">{{ $t('home.documents.title.text2') }}</em></h3>
            <PulseLoader :loading="inProgress" color="#212121"></PulseLoader>
            <Documents :items="documents" v-show="!inProgress"></Documents>
        </section>
        <svg class="section-divider" viewBox="0 0 100 100" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path fill="#f8f9fa" class="divider-bg" d="M 0 0 L 0 100 L 100 100 L 100 0 Z"></path>
            <path fill="#212121" stroke="#212121" stroke-width="3px" class="divider-fg" d="M 0 100 C 40 0 60 0 100 100 Z"></path>
        </svg>
    </div>
</template>

<script>
import ProductGrid from '~/components/ProductGrid.vue';
import Documents from '~/components/Documents.vue';
import PulseLoader from 'vue-spinner/src/PulseLoader.vue';
import { mapActions, mapGetters } from 'vuex';
import heroImgSrc from '~/assets/images/hero.jpg';
import heroImgLoading from '~/assets/images/hero-loading.jpg';

export default {
    data() {
        return {
            inProgress: false,
            heroImg: {
                src: heroImgSrc,
                loading: heroImgLoading
            }
        }
    },
    components: {
        ProductGrid,
        Documents,
        PulseLoader
    },
    computed: {
        ...mapGetters(['products', 'documents'])
    },
    methods: {
        ...mapActions(['fetchProducts', 'fetchDocuments', 'logoutUser'])
    },
    mounted() {
        console.log('Loading data...');
        this.inProgress = true;
        Promise.all([this.fetchProducts(), this.fetchDocuments()]).then(() => {
            console.log('Data loaded sucessfully!');
            this.inProgress = false;
        }).catch(e => {
            this.inProgress = false;
            console.error(e);
            if (e.response) {
                let status = e.response.status;
                switch (status) {
                    case 401:
                        this.logoutUser();
                        this.$router.push('/login');
                        break;
                    case 500:
                        //TODO: Notify the user and updated the ui properly
                        break;
                    default:
                        break;
                }
            }
        });
    }
}
</script>

<style scoped>
#hero {
	background-position: center;
	background-size: cover;
	background-repeat: no-repeat;
	color: #fafafa;
	padding-top: 14rem;
	padding-bottom: 10rem;
	margin-bottom: 0;
	position: relative;
}

#hero .section-divider {
    position: absolute;
    bottom: 0;
}

#products, #documents {
    min-height: 50vh;
}
</style>