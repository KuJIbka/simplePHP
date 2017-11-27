import store from 'services/Store';
import { notyOpen, notyClose } from 'actions/noty';

export default class Notify {
    static app_noty(type, title, text, onConfirm) {
        store.dispatch(notyOpen(type, title, text, onConfirm));
    }

    static app_noty_close() {
        store.dispatch(notyClose());
    }

    static app_alert(text, title) {
        this.constructor.app_noty('alert', title, text);
    }

    static app_confirm(text, onConfirm, title) {
        this.constructor.app_noty('confirm', title, text, onConfirm);
    }
}
