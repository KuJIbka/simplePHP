class User {
    constructor(data = null) {
        this.id = data ? data.id : 0;
        this.login = data ? data.login : '';
    }
}

export default User;
