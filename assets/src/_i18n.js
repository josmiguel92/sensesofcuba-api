import Vue from 'vue';
import I18n from 'vue-i18n';
import Axios from 'axios';

Vue.use(I18n);

const i18n = new I18n({
	locale: 'en',
	fallbackLocale: 'en',
	messages: {
		en: {
			navbar: {
				products: 'Our Products',
				info: 'About Cuba',
				contact: 'Contact Us'
			},
			hero: {
				'1': 'Cuba is... Our Passion',
				'2': 'Welcome to out Client Intranet'
			}
		},
		es: {
			navbar: {
				products: 'Productos',
				info: 'Sobre Cuba',
				contact: 'Contactenos'
			},
			hero: {
				'1': 'Cuba es... Nuestra Pasion',
				'2': 'Bienvenido a nuestra Intranet de Clientes'
			}
		}
	}
});

function loadLocale() {
	return localStorage.getItem('locale') || 'en';
}

function saveLocale(locale) {
	localStorage.setItem('locale', locale);
}

export function setLocale(locale) {
	i18n.locale = locale;
	Axios.defaults.headers.common['Accept-Language'] = locale;
	document.querySelector('html').setAttribute('lang', locale);
    saveLocale(locale);
}

export default function initI18n() {
	const savedLocale = loadLocale();
	setLocale(savedLocale);
	return i18n;
}