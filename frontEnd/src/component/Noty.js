import React from 'react';
import { connect } from 'react-redux';

import Notify from 'services/Notify';

import DeepEqual from 'deep-equal';
import AppTranslator from 'services/AppTranslator';

class Noty extends React.Component {
    constructor(props) {
        super(props);

        this.modalInit = false;
        this.onCloseHandler = this._onClose.bind(this);
        this.onConfirmHandler = this._onConfirm.bind(this);
    }

    shouldComponentUpdate(nextProps, nextState) {
        if (!DeepEqual(this.props.noty, nextProps.noty)) {
            return true;
        }
        return false;
    }

    componentDidUpdate(prevProps, prevState) {
        let modalEl = $(this.refs.commonNoty);
        if (!prevProps.noty.isOpen && this.props.noty.isOpen) {
            if (!this.modalInit) {
                modalEl.on('hide.bs.modal', (e) => {
                    Notify.app_noty_close();
                });
                this.modalInit = true;
            }
            modalEl.modal('show');
        }
        if (prevProps.noty.isOpen && !this.props.noty.isOpen) {
            modalEl.modal('hide');
        }
    }

    render() {
        let modalTitle = '';
        let textClass = '';
        let btnClass = '';
        switch (this.props.noty.type) {
            case 'success':
                modalTitle = AppTranslator.trans('L_SUCCESS');
                textClass = 'text-success';
                btnClass = 'btn-success';
                break;

            case 'error':
                modalTitle = AppTranslator.trans('L_ERROR');
                textClass = 'text-error';
                btnClass = 'btn-error';
                break;

            case 'alert':
                modalTitle = AppTranslator.trans('L_ALERT');
                textClass = 'text-error';
                btnClass = 'btn-error';
                break;

            case 'confirm':
                modalTitle = AppTranslator.trans('L_CONFIRMATION');
                textClass = '';
                btnClass = 'btn-primary';

        }
        if (this.props.noty.title) {
            modalTitle = this.props.noty.title;
        }

        let text = this.props.noty.text;
        return (
            <div ref="commonNoty" className="modal fade" tabIndex="-1" role="dialog">
                <div className="modal-dialog" role="document">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className={ "modal-title " + textClass }>{ modalTitle }</h5>
                            <button type="button" className="close" data-dismiss="modal" aria-label="Close" onClick={ this.onCloseHandler }>
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        { text &&
                        <div className="modal-body">
                            <p className={ textClass }>{ text }</p>
                        </div>
                        }
                        <div className="modal-footer justify-content-center">
                            { this.props.noty.type === 'confirm' &&
                                <button type="button" className="btn btn-success" onClick={ this.onConfirmHandler }>{ AppTranslator.trans('L_CONFIRM') }</button>
                            }
                            <button type="button" className={ "btn " + btnClass } data-dismiss="modal" onClick={ this.onCloseHandler }>{ AppTranslator.trans('L_CLOSE') }</button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    _onClose(e) {
        e.preventDefault();
        e.stopPropagation();
        Notify.app_noty_close();
    }

    _onConfirm(e) {
        e.preventDefault();
        e.stopPropagation();
        this.props.noty.onConfirm();
    }
}

export default connect((store) => {
    return {
        noty: store.noty,
    }
})(Noty);
