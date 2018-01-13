import Utils from 'services/Utils';

let initialState = Object.assign(
    { data: Utils.objToMap(window.appConfig) },
    Utils.createLoadingStore(),
);

export function appConfig(state = initialState, action = {}) {
    switch (action.type) {
        case 'GET_APP_CONFIG_SEND':
            return Object.assign({}, state, {
                isLoading: true,
            });

        case 'GET_APP_CONFIG_SUCCESS':
            return Object.assign({}, state, {
                data: Utils.objToMap(action.data),
                isLoading: false,
                loaded: true,
            });
    }
    return state;
}

