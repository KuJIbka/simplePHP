import React from 'react';
import { connect } from 'react-redux'
import BlockUI from 'react-block-ui';

import Translator from 'bazinga-translator';

import { authRequest } from 'actions/user';

class Login extends React.Component {
    constructor(props) {
        super(props);
        this.handlerOnAuth = this._onAuth.bind(this);
        this.state = {
            blocking: false,
        };
    }

    render() {
        return (
            <div className="jumbotron">
                <BlockUI blocking={ this.state.blocking }>
                    <h2>{ Translator.trans('L_AUTHORISATIONS') }</h2>
                    <form onSubmit={ this.handlerOnAuth }>
                        <div className="form-group">
                            <label>{ Translator.trans('L_LOGIN') }</label>
                            <input name="login" className="form-control" placeholder={ Translator.trans('L_LOGIN_PLACEHOLDER') } />
                        </div>
                        <div className="form-group">
                            <label>{ Translator.trans('L_PASSWORD') }</label>
                            <input name="password" type="password" className="form-control"  placeholder={ Translator.trans('L_PASSWORD_PLACEHOLDER') } />
                        </div>
                        <button type="submit" className="btn btn-primary">{ Translator.trans('L_SIGN_IN') }</button>
                    </form>
                </BlockUI>
            </div>
        );
    }

    _onAuth(e) {
        e.preventDefault();
        let fd = new FormData(e.currentTarget);
        this.setState({
            blocking: true
        });
        this.props.dispatch(authRequest(fd)).then((resp) => {
            if (resp.type === 'error') {
                this.setState({ blocking: false });
            }
        });
    }
}

export default connect()(Login);
