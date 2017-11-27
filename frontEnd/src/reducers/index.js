import { combineReducers } from 'redux';

import { user } from 'reducers/user';
import { noty } from 'reducers/noty';

const rootReducer = combineReducers({
    user,
    noty
});

export default rootReducer;