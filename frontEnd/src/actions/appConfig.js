import Http from 'services/Http';

export function getAppConfigRequest() {
    return (dispatch) => {
        dispatch(getAppConfigSend());
        return Http.get('/getAppConfig').then((resp) => {
            if (resp.type === 'success') {
                dispatch(getAppConfigSuccess(resp.data));
            }
            return resp;
        });
    }
}

function getAppConfigSend() {
    return {
        type: 'GET_APP_CONFIG_SEND',
    };
}

function getAppConfigSuccess(data) {
    return {
        type: 'GET_APP_CONFIG_SEND',
        data: data,
    };
}