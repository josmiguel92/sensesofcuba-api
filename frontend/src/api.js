import Axios from "axios";

//const API_URL = 'http://s498302874.online.de/api';
const API_URL = `${process.env.VUE_API}`;

class API {
    constructor() {
        this.client = Axios.create({
            baseURL: API_URL.trim('%20'),
        });
        this.client.interceptors.request.use(config => {
            const locale = localStorage.getItem('locale');
            const user = JSON.parse(localStorage.getItem('user'));
            if (user && user.token) {
                config.headers['Authorization'] = `Bearer ${user.token}`;
            }
            config.headers['Accept-Language'] = locale;
            return config;
        });
    }

    getCsfrToken(route) {
        return this.client.get(route);
    }

    getProducts() {
        return this.client.get('products');
    }

    getDocuments() {
        return this.client.get('documents');
    }

    login(email, password) {
        return this.client.post('login', {
            email,
            password
        });
    }

    register(credentials) {
        return this.client.post('register', {
            name: credentials.name,
            enterprise: credentials.enterprise,
            travelAgency: credentials.travelAgency,
            web: credentials.web,
            country: credentials.country,
            email: credentials.email,
            password: credentials.password,
            _token: credentials.csfrToken
        });
    }

    resetPassword(email, token) {
        return this.client.post('reset-password', {
            email: email,
            _token: token
        });
    }

    subscribeToProduct(productId) {
        return this.client.get(`product_subscribe/${productId}/subscribe`);
    }

    unsubscribeToProduct(productId) {
        return this.client.get(`product_subscribe/${productId}/unsubscribe`);
    }
}

export default new API();