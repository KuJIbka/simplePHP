$(document).ready(() => {
    $.get('/lang/ru_RU.json', null).then((jsonString) => {
        Translator.fromJSON(JSON.stringify(jsonString));
    });

    let authForm = $('.jqAuthForm');
    authForm.submit((e) => {
         e.preventDefault();
         let el = $(e.currentTarget);
         $.post('/auth/login', el.serialize()).then((resp) => {
                if (resp.text) {
                    alert(resp.text);
                }
                if (resp.type === 'success') {
                    window.location.href = resp.moveTo;
                }
         });
    });

    let logoutEl = $('.jsLogout');
    logoutEl.click((e) => {
        e.preventDefault();
        $.get('/auth/logout', null).then((resp) => {
            if (resp.type === 'success') {
                window.location.href = resp.moveTo;
            }
        });
    });
});
