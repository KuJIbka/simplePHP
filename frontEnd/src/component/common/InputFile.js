import PropTypes from 'prop-types';
import React from 'react';

import AppTranslator from 'services/AppTranslator';

class InputFile extends React.PureComponent {
    constructor(props) {
        super(props);
        this.state = {
            file: props.file ? props.file : null,
        };
    }

    componentWillReceiveProps(nextProps) {
        if (nextProps.file) {
            this.setState({ file: nextProps.file });
        }
    }

    render() {
        return (
            <div className="custom-file">
                <input type="file"
                       className={ "custom-file-input " + this.props.isValidClassName }
                       id="inputGroupFile"
                       name={ this.props.name }
                       value={ this.state.value }
                       accept={ this.props.accept }
                       required={ this.props.required }
                       onChange={ this._onFileSelect.bind(this) }
                />
                <label className="custom-file-label "
                       htmlFor="inputGroupFile02"
                >{ this.state.file !== null ? this.state.file.name : AppTranslator.trans('L_CHOOSE_FILE') }</label>

                <div className="invalid-feedback">
                    { this.props.isValidText }
                </div>
            </div>
        );
    }

    _onFileSelect(e) {
        if (typeof this.props.onFileSelect === 'function') {
            this.props.onFileSelect(e);
        } else {
            if (e.target.files[0]) {
                this.setState({
                    file: e.target.files[0]
                });
            }
        }
    }

    _onCancelFile(e) {
        e.preventDefault();
        this.refs.mainInput.value = null;
        if (this.props.onCancelFile === 'function') {
            this.props.onCancelFile(e);
        } else {
            this.setState({
                file: null,
            });
        }
    }

    static propTypes = {
        isValidClassName: PropTypes.string,
        isValidText: PropTypes.string,
        label: PropTypes.string,
        buttonText: PropTypes.string,
        name: PropTypes.string,
        accept: PropTypes.string,
        required: PropTypes.bool,
        file: PropTypes.object,
        onFileSelect: PropTypes.func,
        onCancelFile: PropTypes.func
    }
}

export default InputFile;
