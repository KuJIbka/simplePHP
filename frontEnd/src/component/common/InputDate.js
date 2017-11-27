import React from 'react';
import PropTypes from 'prop-types';

import FormsHelper from 'services/FormsHelper';
import DeepEqual from 'deep-equal';
import moment from 'moment';

import DatePicker from 'react-datepicker';

class InputDate extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            value: this.props.value
        };

        this.maxDate = moment();
        this.onChangeInputHandler = this._onChangeDate.bind(this);
    }

    componentWillReceiveProps(nextProps) {
        this.setState({
            value: nextProps.value,
        });
    }

    shouldComponentUpdate(nextProps, nextState) {
        if (this.props.value !== nextProps.value
            || this.state.value !== nextState.value
            || !DeepEqual(this.props.errors, nextProps.errors)
        ) {
            return true;
        }
        return false;
    }

    render() {
        return (
            <DatePicker customInput={ this._getCustomInput() }
                        selected={ this.state.value }
                        dateFormat="DD-MM-YYYY"
                        showYearDropdown
                        withPortal
                        onChange={ this.onChangeInputHandler }
                        maxDate={ this.maxDate }
            />
        );
    }

    _getCustomInput() {
        let additionalInputClassName = FormsHelper.getErrorClass(this.props.name, this.props.errors);
        if (this.props.className) {
            additionalInputClassName += ' ' + this.props.className;
        }
        if (this.props.qaClassName) {
            additionalInputClassName += ' ' + this.props.qaClassName;
        }
        return (
            <div>
                <input name={ this.props.name }
                       className={ "form-control " + additionalInputClassName }
                       placeholder={ this.props.placeholder }
                       value={ this.state.value ? this.state.value.format("DD-MM-YYYY") : '' }
                       autoComplete={ this.props.autoComplete }
                       autoFocus={ this.props.autoFocus }
                       required={ this.props.required }
                       onChange={ () => {} }
                />
                { FormsHelper.hasError(this.props.name, this.props.errors) &&
                <div className="invalid-feedback">
                    { FormsHelper.getError(this.props.name, this.props.errors) }
                </div>
                }
            </div>
        );
    }

    _onChangeDate(date) {
        if (typeof this.props.onChange === 'function') {
            this.props.onChange(date);
        } else {
            this.setState({
                value: date
            });
        }
    }

    static propTypes = {
        name: PropTypes.string,
        className: PropTypes.string,
        qaClassName: PropTypes.string,
        placeholder: PropTypes.string,
        autoComplete: PropTypes.string,
        autoFocus: PropTypes.bool,
        required: PropTypes.bool,
        value: PropTypes.oneOfType([
            PropTypes.string,
            PropTypes.object,
        ]),
        onChange: PropTypes.func,
        errors: PropTypes.object,
    };
}

export default InputDate;
