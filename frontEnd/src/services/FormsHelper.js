export default class FormsHelper {
    static getErrorClass(inputName, errors) {
        if (errors) {
            if (errors[inputName]) {
                return 'is-invalid';
            } else {
                return 'is-valid';
            }
        }
        return '';
    }

    static hasError(inputName, errors) {
        return !!(errors && errors[inputName]);
    }

    static getError(inputName, errors) {
        if (errors && errors[inputName]) {
            return errors[inputName];
        }
        return null;
    }
}
