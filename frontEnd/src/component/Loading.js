import React from 'react';
import PropTypes from 'prop-types';
import BlockUI from 'react-block-ui';

class Loading extends React.Component {
    render() {
        return (
            <BlockUI className={ this.props.className } blocking={ true }/>
        );
    }

    static propTypes = {
        className: PropTypes.string,
    }
}

export default Loading;