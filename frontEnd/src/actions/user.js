import Http from 'services/Http';

export function authRequest(data) {
    return (dispatch) => {
        dispatch(authRequestSend());
        return Http.csrfPost(Http.getLocalizedUrl('/auth/login'), data).then((resp) => {
            if (resp.type === 'success') {
                dispatch(authRequestSuccess(resp.data.userData));
            }
            return resp;
        });
    }
}

function authRequestSend() {
    return {
        type: 'AUTH_REQUEST_SEND'
    };
}

function authRequestSuccess(data) {
    return {
        type: 'AUTH_REQUEST_SUCCESS',
        data: data
    };
}


export function logoutRequest() {
    return (dispatch) => {
        dispatch(logoutRequestSend());
        return Http.post(Http.getLocalizedUrl('/auth/logout')).then((resp) => {
            if (resp.type === 'success') {
                dispatch(logoutRequestSuccess());
                return resp;
            }
        });
    }
}

function logoutRequestSend() {
    return {
        type: 'LOGOUT_REQUEST_SEND'
    };
}

function logoutRequestSuccess() {
    return {
        type: 'LOGOUT_REQUEST_SUCCESS'
    };
}


export function getUserSettingsRequest() {
    return (dispatch) => {
        dispatch(getUserSettingsSend());
        return Http.get(Http.getLocalizedUrl('/auth/getUserSettings')).then((resp) => {
            if (resp.type === 'success') {
                dispatch(getUserSettingsSuccess(resp.data.userData));
                return resp;
            }
        })
    }
}

function getUserSettingsSend() {
    return {
        type: 'GET_USER_SETTINGS_SEND',
    };
}

function getUserSettingsSuccess(data) {
    return {
        type: 'GET_USER_SETTINGS_SUCCESS',
        data: data,
    };
}
