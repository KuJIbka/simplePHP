import { createStore, applyMiddleware } from 'redux';
import thunkMiddleware from 'redux-thunk';
import { createLogger } from 'redux-logger';

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

export default store;