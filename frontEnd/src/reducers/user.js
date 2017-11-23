import User from 'entity/User';

import Utils from 'services/Utils';

let initialState = Object.assign(
    new User(),
    Utils.createLoadingStore(),
    {
        isAuth: false
    }
);

export function user(state = initialState, action = {}) {
    switch (action.type) {
        case 'AUTH_REQUEST_SEND':
            return Object.assign({},
                state,
                { isLoading: true }
            );

        case 'AUTH_REQUEST_SUCCESS':
            return Object.assign({},
                state,
                new User(action.data),
                {
                    isLoading: false,
                    loaded: true,
                    isAuth: true
                }
            );


        case 'LOGOUT_REQUEST_SEND':
            return Object.assign({},
                state,
                { isLoading: true }
            );

        case 'LOGOUT_REQUEST_SUCCESS':
            return Object.assign({},
                new User(),
                {
                    isLoading: false,
                    isAuth: false
                }
            );


        case 'GET_USER_SETTINGS_SEND':
            return Object.assign({},
                new User(),
                state,
                { isLoading: true }
            );

        case 'GET_USER_SETTINGS_SUCCESS':
            return Object.assign({},
                state,
                new User(action.data),
                {
                    isLoading: false,
                    loaded: !!action.data,
                    isAuth: !!action.data
                }
            );


        default:
            return state;
    }
}