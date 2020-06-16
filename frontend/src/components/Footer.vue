<template>
    <footer id="contact" class="d-flex flex-column align-items-center w-100 bg-dark text-light text-medium">
        <h3 class="title mt-4">{{ $t('home.contact.title.text1') }} <br> <em class="text-primary">{{ $t('home.contact.title.text2') }}</em></h3>
        <div class="container">
            <div class="row p-4">
                <div class="col mt-md-0 mt-2">
                    <div class="nav flex-column text-center text-md-right">
                        <div class="nav-item">
                            <strong>{{ $t('home.contact.address') }}</strong>
                            <br>
                            <span>Edificio Bacardi, Oficina 404, Calle Monserrate</span>
                        </div>
                        <div class="nav-item">
                            <strong>{{ $t('home.contact.phone') }}</strong>
                            <br>
                            <span>+53 7866 4734</span>
                        </div>
                        <div class="nav-item">
                            <strong>{{ $t('home.contact.emergency_phone') }}</strong>
                            <br>
                            <span>+53 5339 3060</span>
                        </div>
                    </div>
                </div>
                <div class="col mt-2 mt-md-0">
                    <div class="nav flex-column text-center text-md-left">
                        <div class="nav-item">
                            <strong>{{ $t('home.contact.general_contact') }}</strong>
                            <br>
                            <span>info@sensesofcuba.com</span>
                        </div>
                        <div class="nav-item">
                            <strong>{{ $t('home.contact.sales') }}</strong>
                            <br>
                            <span>sales@sensesofcuba.com</span>
                        </div>
                        <div class="nav-item">
                            <strong>{{ $t('home.contact.product_management') }}</strong>
                            <br>
                            <span>product@sensesofcuba.com</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2 mb-4">
                <div class="col">
                    <div class="nav flex-column text-center">
                        <div class="nav-item">
                            <strong>{{ $t('home.contact.opening_times') }}</strong>
                            <br>
                            <span>Monday - Sunday: 9:00-17:00</span>
                            <br>
                            <span>Saturday - Sunday (and public holidays): 10:00-16:00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="img-wrapper">
            <img v-lazy="imgObj">
        </div>
        <div class="container">
            <div class="row p-4">
                <div class="col d-flex align-items-center flex-column">
                    <div class="nav flex-column flex-md-row align-items-center justify-content-center">
                        <div class="nav-item">
                            <a href="#" class="nav-link">{{ $t('home.footer.links.imprint') }}</a>
                        </div>
                        <div class="nav-item">
                            <a href="#" class="nav-link">{{ $t('home.footer.links.privacy_policy') }}</a>
                        </div>
                        <div class="nav-item">
                            <a href="#" class="nav-link">{{ $t('home.footer.links.cooperations') }}</a>
                        </div>
                        <div class="nav-item">
                            <a href="#" class="nav-link">{{ $t('home.footer.links.general_and_conditions') }}</a>
                        </div>
                    </div>
                    <small class="copyright mt-4">
                        {{ $t('home.footer.copyright') }} <i class="fa fa-copyright"></i> {{ year }}. Senses Of Cuba (By Tourcom).
                    </small>
                </div>
            </div>
        </div>
    </footer>
</template>

<script>
import imgSrc from '~/assets/images/havana.jpg';
import imgLoading from '~/assets/images/soc_team-loading.jpg';

export default {
    data() {
        return {
            imgObj: {
                src: imgSrc,
                loading: imgLoading
            }
        }
    },
    computed: {
        year() {
            return new Date().getFullYear();
        }
    }
}
</script>

<style scoped>
.img-wrapper {
    width: 100%;
    height: 200px;
    position: relative;
}
.img-wrapper img {
    height: 100%;
    width: 100%;
    object-fit: cover;
    object-position: center;
}
</style>