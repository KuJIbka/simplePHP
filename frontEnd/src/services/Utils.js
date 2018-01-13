export default class Utils {
    static blockMouseEvents(obj) {
        obj.attr('onclick', 'return false;');
        obj.css('pointer-events', 'none');
    }

    static unblockMouseEvents(obj) {
        obj.removeAttr('onclick');
        obj.css('pointer-events', 'auto');
    }

    static number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '')
            .replace(/[^0-9+\-Ee.]/g, '');
        let n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            toFixedFix = function(n, prec) {
                let k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec);
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        let s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
            .split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '')
                .length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1)
                .join('0');
        }
        return s.join(dec);
    }

    static getMoneyFormat(amount, decimals) {
        amount = +amount;
        if (decimals) {
            decimals = +decimals;
        } else {
            decimals = this.decimalCount(amount);
        }
        let moneyVal = this.number_format(amount, decimals, ',', ' ');
        if (moneyVal.indexOf(',') !== -1) {
            moneyVal = moneyVal.replace(/0+$/, '').replace(/,$/, '');
        }
        return moneyVal;
    }

    static decimalCount(num) {
        let match = (''+num).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        if (!match) { return 0; }
        return Math.max(
            0,
            // Number of digits right of decimal point.
            (match[1] ? match[1].length : 0)
            // Adjust for scientific notation.
            - (match[2] ? +match[2] : 0)
        );
    }

    static numToRealStr(number) {
        number = +number;
        let str = number.toString();
        let eIndex = str.indexOf('e');
        if (eIndex !== -1) {
            let eVal = +str.substr(eIndex + 2);
            let dotIndex = str.indexOf('.');
            if (dotIndex !== -1) {
                eVal += eIndex - dotIndex - 1;
            }
            str = number.toFixed(eVal);
        }
        return str.replace('.', ',');
    }

    static parseAmount(amount) {
        amount += "";
        amount = amount.replace(',', '.');
        let m = amount.match(/^\d*(\.?\d*)?/);
        return m[0] ? parseFloat(m[0]) : 0;
    }

    static floor(amount, prec = 0) {
        const acc = Math.pow(10, prec);
        return Math.floor(amount * acc) / acc;
    }

    static ceil(amount, prec = 0) {
        const acc = Math.pow(10, prec);
        return Math.ceil(amount * acc) / acc;
    }

    static getDateFromStr(ddmmyyString) {
        if (/^\d\d[-.\/]\d\d[-.\/]\d\d\d\d$/.exec(ddmmyyString)) {
            let parseDate = ddmmyyString.split('/');
            if (!parseDate[1]) {
                parseDate = ddmmyyString.split('.');
            }
            if (!parseDate[1]) {
                parseDate = ddmmyyString.split('-');
            }
            let inputDate = new Date();
            inputDate.setYear(+parseDate[2]);
            inputDate.setMonth(+parseDate[1] - 1);
            inputDate.setDate(+parseDate[0]);
            inputDate.setHours(0);
            inputDate.setMinutes(0);
            inputDate.setSeconds(0);
            inputDate.setMilliseconds(0);
            return inputDate;
        } else {
            return null;
        }
    }

    static nameValueArrayToObj(arr) {
        let result = {};
        arr.map((el) => {
            result[el.name] = el.value;
        });
        return result;
    }

    static createIdRawStore(array = []) {
        let cloneArray = array.slice(0);
        let store = new Map();
        cloneArray.map((el) => {
            store.set(el.id, el);
        });
        return { data: store };
    }

    static objToMap(obj) {
        let m = new Map();
        for (let key in obj) {
            if (obj.hasOwnProperty(key)) {
                m.set(key, obj[key]);
            }
        }
        return m;
    }

    static createLoadingStore() {
        return {
            loaded: false,
            isLoading: true,
        }
    }

    static mapToArray(mapObject) {
        const arr = [];
        for (let el of mapObject.values()) {
            arr.push(el);
        }
        return arr;
    }

    static nl2br(str) {
        return str.replace(/([^>])\n/g, '$1<br/>');
    }

    static setCrossDomainCookie(name, value, expire = null) {
        let domain = '.' + window.location.hostname.replace('m.', '');
        $.cookie(name, value, { domain: domain, path: '/' , expire: expire });
    }

    static serializeArray(form) {
        let field, length, output = [];

        if (typeof form === "object" && form.nodeName === "FORM") {
            let length = form.elements.length;
            for (i = 0; i < length; i++) {
                field = form.elements[i];
                if (field.name && !field.disabled && field.type !== "file" && field.type !== "reset" && field.type !== "submit" && field.type !== "button") {
                    if (field.type === "select-multiple") {
                        length = form.elements[i].options.length;
                        for (j = 0; j < length; j++) {
                            if(field.options[j].selected)
                                output[output.length] = { name: field.name, value: field.options[j].value };
                        }
                    } else if ((field.type !== "checkbox" && field.type !== "radio") || field.checked) {
                        output[output.length] = { name: field.name, value: field.value };
                    }
                }
            }
        }

        return output;
    }
}
