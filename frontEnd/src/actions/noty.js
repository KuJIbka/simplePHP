export function notyOpen(type, title, text, onConfirm) {
    return {
        type: 'NOTY_SUCCESS',
        data: {
            type: type,
            title: title,
            text: text,
            onConfirm: onConfirm,
        }
    };
}

export function notyClose() {
    return {
        type: 'NOTY_CLOSE',
    };
}
