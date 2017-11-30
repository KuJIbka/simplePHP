import History from 'services/History';
import Notify from 'services/Notify';

class Http {
    request(method, url, data, headers = {}) {
        let options = {
            method: method,
            credentials: 'include',
            headers: Object.assign({
                "X-Requested-With": 'XMLHttpRequest',
            }, headers),
        };
        if (data) {
            options.body = data;
        }
        return fetch(url, options).then((resp) => {
            return resp.json();
        }).then((resp) => {
            if (resp.text) {
                Notify.app_noty(resp.type, null, resp.text);
            }
            if (resp.moveTo) {
                if (resp.text) {
                    setTimeout(() => {
                        if (resp.moveTo.indexOf('#') !== -1) {
                            History.push(resp.moveTo.split('#')[1]);
                        } else {
                            window.location.href = resp.moveTo;
                        }
                    }, 7000);
                }
            }
            return resp;
        });
    }

    get(url, data, headers = {}) {
        url += '?' + this.convertToUri(data);
        return this.request('get', url, null, headers);
    }

    post(url, data, headers = {}) {
        let dataToSend = this.convertToFormData(data);
        return this.request('post', url, dataToSend, headers);
    }

    convertToFormData(anyFormatData) {
        if (anyFormatData instanceof FormData) {
            return anyFormatData;
        }
        let fData = new FormData();
        if (anyFormatData instanceof Array) {
            for (let k in anyFormatData) {
                fData.append(anyFormatData[k].name, anyFormatData[k].value);
            }
        } else if (typeof anyFormatData === 'object') {
            for (let k in anyFormatData) {
                if (anyFormatData.hasOwnProperty(k)) {
                    fData.append(k, anyFormatData[k]);
                }
            }
        }
        return fData;
    }

    convertToUri(anyFormatData) {
        if (typeof anyFormatData === 'string') {
            return anyFormatData;
        }
        let fData = [];
        if (anyFormatData instanceof FormData || anyFormatData instanceof Map) {
            anyFormatData.forEach((el, key) => {
                 fData.push(encodeURIComponent(key) + '=' + encodeURIComponent(el));
            });
        } else if (anyFormatData instanceof Array) {
            for (let k in anyFormatData) {
                fData.push(encodeURIComponent(anyFormatData[k].name) + '=' + encodeURIComponent(anyFormatData[k].value));
            }
        } else if (typeof anyFormatData === 'object') {
            for (let k in anyFormatData) {
                if (anyFormatData.hasOwnProperty(k)) {
                    fData.push(encodeURIComponent(k) + '=' + encodeURIComponent(anyFormatData[k]));
                }
            }
        }
        return fData.join("&").replace(/%20/g, "+");
    }
}

export default new Http();