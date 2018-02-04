import React from 'react';
import { connect } from 'react-redux';

import AppTranslator from 'services/AppTranslator';

import { logoutRequest } from 'actions/user';

class Private extends React.Component {
    constructor(props) {
        super(props);

        this.logoutHandler = this._logoutHandler.bind(this);
    }

    render() {
        return (
            <div>
                <h1>{ AppTranslator.trans('L_HELLO_NAME', { name: this.props.user.login }) }</h1>
                <button className="btn btn-primary" onClick={ this.logoutHandler }>{ AppTranslator.trans('L_LOGOUT') }</button>
            </div>
        );
    }

    _logoutHandler(e) {
        e.preventDefault();
        this.props.dispatch(logoutRequest());
    }
}

export default connect((store) => {
    return {
        user: store.user,
    };
})(Private);