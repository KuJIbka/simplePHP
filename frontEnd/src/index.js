import 'bootstrap/dist/js/bootstrap.bundle.min';

import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { Router } from 'react-router';

import routes from 'routes/index';

import Http from 'services/Http';
import Translator from 'bazinga-translator';
import History from 'services/History';
import store from 'services/Store';

let locale = document.querySelector('html').lang;

Http.get('/lang/' + locale +'.json').then((jsonLang) => {
    Translator.fromJSON(jsonLang);
    ReactDOM.render(
        <Provider store={ store }>
            <Router history={ History }>
                { routes }
            </Router>
        </Provider>,
        document.querySelector('.jsReactApp')
    );
});
