export function noty(state = {}, action = {}) {
    switch (action.type) {
        case 'NOTY_SUCCESS':
            return {
                ...state,
                type: action.data.type,
                title: action.data.title,
                text: action.data.text,
                onConfirm: action.data.onConfirm,
                isOpen: true,
            };

        case 'NOTY_CLOSE':
            return {
                ...state,
                type: null,
                title: null,
                message: null,
                onConfirm: null,
                isOpen: false,
            };

        default:
            return state;
    }
}
