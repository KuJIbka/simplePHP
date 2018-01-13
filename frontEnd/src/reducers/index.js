import { combineReducers } from 'redux';

import { user } from 'reducers/user';
import { noty } from 'reducers/noty';
import { appConfig } from 'reducers/appConfig';

const rootReducer = combineReducers({
    user,
    noty,
    appConfig,
});

export default rootReducer;