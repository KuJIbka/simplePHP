export default class Notify {
    static app_alert(message) {
        let error = false;
        try {
            let ts = new Date();
            alert(message);
            let te = new Date();
            if (te - ts < 50) {
                error = true;
            }
        } catch (e) {
            error = true;
        }
        if (error) {
            this.app_noty({
                type: 'error',
                text: language.getLabel('L_JS_BROWSER_ALERT_DISABLE')
            });
        }
    }

    static app_confirm(message) {
        let error = false;
        let confirmResult = false;

        try {
            let ts = new Date();
            confirmResult = confirm(message);
            let te = new Date();
            if (te - ts < 50) {
                error = true;
            }
        } catch (e) {
            error = true;
        }
        if (error) {
            this.app_noty({
                type: 'error',
                text: language.getLabel('L_JS_BROWSER_ALERT_DISABLE')
            });
        }
        return confirmResult;
    }
}
