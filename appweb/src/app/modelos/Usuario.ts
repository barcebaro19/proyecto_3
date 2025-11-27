export class Usuario {
    company = '';
    department = '';
    displayname = '';
    mail = '';
    name = '';
    tiempo = 0;
    token = '';
    apptoken = '';
    usuario = '';
    isLogin = false;
    roles = [];
    reset() {
        this.company = '';
        this.department = '';
        this.displayname = '';
        this.mail = '';
        this.name = '';
        this.tiempo = 0;
        this.token = '';
        this.apptoken = '';
        this.isLogin = false;
        this.roles = [];
    }
}