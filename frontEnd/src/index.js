import React from 'react';
import ReactDOM from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import { Provider } from 'react-redux';
import { Router, browserHistory } from 'react-router';
import thunkMiddleware from 'redux-thunk';
import { createLogger } from 'redux-logger';

import routes from 'routes/index';

import Http from 'services/Http';
import Translator from 'bazinga-translator';
import History from 'services/History';

const middlewares = [ thunkMiddleware ];

if (process.env.NODE_ENV !== `production`) {
    const logger = createLogger({
        //predicate: (getState, action) => action.type === "GET_MY_ORDERS_REQUEST"
    });
    //middlewares.push(logger);
}

import rootReducer from 'reducers/index';

const store = createStore(
    rootReducer,
    applyMiddleware(...middlewares)
);

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
