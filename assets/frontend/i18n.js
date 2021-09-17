import I18n from 'vue-i18n';
import Vue from 'vue';

import en from './lang/en';
import de from './lang/de';

Vue.use(I18n);

const messages = {
    en: en,
    de: de
};

/*const messages = {
    en: {
        home: {
            navbar: {
                links: {
                    products: 'Our Products',
                    documents: 'About Cuba',
                    contact: 'Contact Us'
                },
                user: {
                    options: 'Options',
                    logout: 'Logout'
                },
                lang: {
                    en: 'English',
                    de: "German"
                }
            },
            hero: {
                welcome: {
                    text1: 'Cuba is...',
                    text2: 'Our',
                    text3: 'Passion'
                },
                lead: 'Welcome to our Client Intranet'
            },
            products: {
                text1: "Our",
                text2: "Products"
            },
            documents: {
                text1: "About",
                text2: "Cuba"
            },
            contact: {
                text1: "Contact",
                text2: "Us",
                address: "Address",
                phone: "Phone",
                e_phone: "Emergency Phone",
                g_contact: "General Contact",
                sales: "Sales",
                p_management: "Product Management",
                o_times: "Opening Times"
            }
        },
        login: {

        },
        register: {

        },
        general: {
            download: "Download",
            open: "Open"
        }
    },
    de: {}
}*/

const i18n = new I18n({
    locale: 'en',
    fallbackLocale: 'en',
    messages: messages
});

function loadLocale() {
	return localStorage.getItem('locale') || 'en';
}

function saveLocale(locale) {
	localStorage.setItem('locale', locale);
}

export function setLocale(locale) {
	i18n.locale = locale;
	document.querySelector('html').setAttribute('lang', locale);
    saveLocale(locale);
}

export default function initI18n() {
    const savedLocale = loadLocale();
    setLocale(savedLocale);
    return i18n;
}