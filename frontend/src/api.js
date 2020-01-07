import Axios from "axios";

const API_URL = 'http://localhost:9000/api';

class API {
    constructor() {
        this.client = Axios.create({
            baseURL: API_URL,
        });
        this.client.interceptors.request.use(config => {
            const locale = localStorage.getItem('locale');
            config.headers['Accept-Language'] = locale;
            return config;
        });
    }

    getCsfrToken() {
        return this.client.get('register');
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

    resetPassword(email) {
        return this.client.post('reset-password', {
            email: email
        });
    }
}

export default new API();