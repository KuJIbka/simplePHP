import PropTypes from 'prop-types';
import React from 'react';

import { controlKeyCodes, controlIntKeyCodes, controlFloatKeyCodes } from 'constant/KeyCodes';

const availableKeyCode = [].concat(controlKeyCodes, controlIntKeyCodes, controlFloatKeyCodes);

class InputFloat extends React.Component {
    render() {
        return (
            <input ref="mainInput"
                type="tel"
                className={ this.props.className + ' ' + this.props.qaClassName }
                name={ this.props.name }
                value={ this.props.value }
                required={ this.props.required }
                placeholder={ this.props.placeholder }
                autoComplete={ this.props.autoComplete }
                autoFocus={ this.props.autoFocus }
                onPaste={ this._onPaste.bind(this) }
                onInput={ this._onChange.bind(this) }
                onKeyDown={ this._onKeyDown }
            />
        );
    }

    _onKeyDown(e) {
        let isPaste = e.keyCode === 86 && e.ctrlKey;
        if (!isPaste) {
            if (availableKeyCode.indexOf(e.keyCode) === -1) {
                e.preventDefault();
            }
        }
    }

    _onChange(e) {
        let parsedValue = e.currentTarget.value.replace(/[^0-9.,-]/g, '');
        let m = parsedValue.match(/^-?\d*((\.|,)?\d*)?/);
        e.currentTarget.value = m[0] ? m[0] : '';
        if (typeof this.props.onChange === 'function') {
            this.props.onChange(e);
        }
    }

    _onPaste(e) {
        const el = this.refs.mainInput;
        setTimeout(() => {
            let event = document.createEvent('Event');
            event.initEvent('input', true, true);
            el.dispatchEvent(event);
        }, 40);
    }

    static propTypes = {
        name: PropTypes.string,
        className: PropTypes.string,
        qaClassName: PropTypes.string,
        placeholder: PropTypes.string,
        autoComplete: PropTypes.string,
        autoFocus: PropTypes.bool,
        required: PropTypes.bool,
        onChange: PropTypes.func,
    }
}

export default InputFloat;
