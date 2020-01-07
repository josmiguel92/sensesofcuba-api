import BaseService from './base.service';

class UserService extends BaseService {
	constructor() {
		super();
	}

	getCSRFCode() {
		return this.get('register');
	}

	signIn(email, password) {
		return this.post('login', {
			email: email,
			password: password
		});
	}

	signUp(credentials) {
		return this.post('register', credentials);
	}
}

export default new UserService();