import React from 'react';
import { connect } from 'react-redux';

import Login from 'component/Login';
import Loading from 'component/Loading';
import Noty from 'component/Noty';

import { getUserSettingsRequest } from 'actions/user';


class Interface extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            firstUserLoaded: false,
        };
        props.dispatch(getUserSettingsRequest()).then(() => {
            this.setState({
                firstUserLoaded: true
            });
        });
    }

    shouldComponentUpdate(nextProps, nextState) {
        if (this.props.user.isAuth !== nextProps.user.isAuth
            || this.state.firstUserLoaded !== nextState.firstUserLoaded
        ) {
            return true;
        }
        return false;
    }

    render() {
        return (
            <div className="container">
                { (() => {
                    if (!this.state.firstUserLoaded) {
                        return <Loading className="block-ui-overlay__hidden-overlay" />;
                    } else {
                        if (this.props.user.isAuth) {
                            return this.props.children;
                        } else {
                            return <Login />;
                        }
                    }
                })() }

                <Noty />
            </div>
        );
    }
}

export default connect((store) => {
    return {
        user: store.user,
    };
})(Interface);
