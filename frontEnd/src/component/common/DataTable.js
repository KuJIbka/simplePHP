import PropTypes from 'prop-types';
import React from 'react';

import AppTranslator from 'services/AppTranslator';

import Loading from 'component/Loading';

class DataTable extends React.PureComponent {
    constructor(props, componentState = {}) {
        super(props);
        const pageCurrent = Math.floor(props.start / props.length) + 1;
        const pageCount = Math.ceil(props.recordsTotal / props.length) || 1;

        this.state = Object.assign({
            length: props.lengthMenu[0],
            start: props.start,
            orderBy: props.orderBy,
            isLastPage: pageCurrent === pageCount,
            pageCurrent: pageCurrent,
            pageCount: pageCount,
            tableClassName: '',
            withAction: typeof props.loadingAction === 'function',
            blockUI: true,
        }, componentState);

        this._onChangeLength = this._onChangeLength.bind(this);
    }

    componentWillMount() {
        this.onPageChange();
    }

    componentWillReceiveProps(nextProps) {
        let rt = nextProps.recordsTotal || 0;
        let pageCount = Math.ceil(rt / this.state.length) || 1;

        let oldOrderBy = this.props.orderBy;
        let newState = {
            orderBy: nextProps.orderBy,
            recordsTotal: rt,
            pageCount: pageCount,
            isLastPage: this.state.pageCurrent === pageCount,
            data: nextProps.data || []
        };

        if (this.props.isLoading && !nextProps.isLoading) {
            newState.loaded = true;
            newState.blockUI = false;
        }
        this.setState(newState, () => {
            if (oldOrderBy !== this.props.orderBy) {
                this.onPageChange();
            }
        });
    }

    render() {
        let content = [];
        if (this.props.children) {
            content = content.concat(this.props.children);
        } else {
            content.push(this.renderHeader());
            content.push(this.renderBody());
        }
        return (
            <div ref="dataTableWrapper">
                <div className="d-flex justify-content-between align-items-center">
                    { this.props.isLoading && <div className="mr-auto col-1"><Loading className="block-ui-overlay__hidden-overlay" /></div> }
                    { !this.props.data.length && !this.props.isLoading &&
                        <div className="mr-auto">{AppTranslator.trans('L_DATA_TABLE_TEXT_NO_RESULTS')}</div>
                    }
                    <div className="ml-auto">
                        <form className="form-inline">
                            <label className="mr-3">{AppTranslator.trans('L_DATA_TABLE_TEXT_PER_PAGE')}</label>
                            <select className="custom-select custom-select-sm custom-control-inline" onChange={this._onChangeLength}>
                                {this.props.lengthMenu.map((val) => {
                                    return (<option key={val} value={val}>{val}</option>);
                                })}
                            </select>
                        </form>
                    </div>
                </div>
                <hr />
                <table className={ this.props.className }>
                {content}
                </table>
                <div className="d-flex justify-content-between align-items-center">
                    <span>{AppTranslator.trans('L_DATA_TABLE_RECORDS_TOTAL', {
                        start: this.props.data.length === 0 ? 0 : this.state.start + 1,
                        toCount: this.state.start + (this.state.isLastPage ? this.props.data.length : this.state.length),
                        total: this.props.recordsTotal
                    })}</span>
                    {this._renderPagination()}
                </div>
            </div>
        );
    }

    _onPaginationClick(start, e) {
        e.preventDefault();
        this._onPageChange(start, this.state.length);
    }

    _onChangeLength(el) {
        this._onPageChange(this.state.start, +el.target.value);
    }

    _onPageChange(start, length) {
        if (start !== this.state.start || length !== this.state.length) {
            const pageCurrent = Math.floor(start / length) + 1;
            const pageCount = Math.ceil(this.props.recordsTotal / length) || 1;

            this.setState({
                pageCurrent: pageCurrent,
                pageCount:  pageCount,
                start: Math.floor(start / length) * length,
                length: length,
                isLastPage: pageCurrent === pageCount
            }, () => {
                this.onPageChange();
            });
        }
    }

    _renderPagination() {
        const isPenult = this.state.pageCurrent + 1 === this.state.pageCount;
        const isSecond = this.state.pageCurrent === 2;
        const withPrev = this.state.pageCurrent !== 1;
        const withNext = this.state.pageCurrent !== this.state.pageCount;
        const prevStart = (this.state.pageCurrent - 2) * this.state.length;
        const nextStart = this.state.pageCurrent * this.state.length;

        let pageElems = [];
        let isActive = false;

        const addPageElems = (from, to, pageElems) => {
            for (let i = from; i <= to; i++) {
                isActive = i === this.state.pageCurrent ? 'active' : '';
                let pageStart = (i - 1) * this.state.length;
                pageElems.push(
                    <li key={i} className={ "page-item " + isActive }>
                        <a onClick={this._onPaginationClick.bind(this, pageStart)} className="page-link" href="#">{i}</a>
                    </li>
                );
            }
        };

        if (this.props.withNumPagination) {
            if (this.state.pageCount <= 5) {
                addPageElems(1, this.state.pageCount, pageElems);
            } else {
                if (this.state.pageCurrent === 1 || this.state.pageCurrent === this.state.pageCount) {
                    addPageElems(1, 3, pageElems);
                    pageElems.push(
                        <li key={'0.0'} className="page-item">
                            <span>...</span>
                        </li>
                    );
                    addPageElems(this.state.pageCount, this.state.pageCount, pageElems);
                } else if (isSecond) {
                    addPageElems(1, 3, pageElems);
                    pageElems.push(
                        <li key={'0.0'} className="page-item">
                            <span>...</span>
                        </li>
                    );
                    addPageElems(this.state.pageCount, this.state.pageCount, pageElems);
                } else if (isPenult) {
                    addPageElems(1, 2, pageElems);
                    pageElems.push(
                        <li key={'0.0'} className="page-item">
                            <span>...</span>
                        </li>
                    );
                    addPageElems(this.state.pageCount - 1, this.state.pageCount, pageElems);
                } else {
                    addPageElems(1, 1, pageElems);
                    pageElems.push(
                        <li key={'0.0'} className="page-item">
                            <span>...</span>
                        </li>
                    );
                    addPageElems(this.state.pageCurrent, this.state.pageCurrent, pageElems);
                    pageElems.push(
                        <li key={'0.1'} className="page-item">
                            <span>...</span>
                        </li>
                    );
                    addPageElems(this.state.pageCount, this.state.pageCount, pageElems);
                }
            }
        }

        return (
            <ul className="pagination">
                <li className={ "page-item " + (!withPrev ? 'disabled' : '') }>
                    <a href="#" className="page-link" onClick={ this._onPaginationClick.bind(this, prevStart) }>{ AppTranslator.trans('L_DATA_TABLE_PAGE_PREV') }</a>
                </li>
                {pageElems}
                <li className={ "page-item " + (!withNext ? 'disabled' : '') }>
                    <a href="#" className="page-link" onClick={ this._onPaginationClick.bind(this, nextStart) }>{ AppTranslator.trans('L_DATA_TABLE_PAGE_NEXT') }</a>
                </li>
            </ul>
        );
    }

    renderHeader() {}

    renderBody() {}

    onPageChange() {
        if (this.state.withAction) {
            let sendData = {
                start: this.state.start,
                length: this.state.length,
            };
            if (this.state.orderBy) {
                sendData.orderBy = this.state.orderBy;
            }
            this.props.loadingAction(sendData);
        }
    }

    static propTypes = {
        lengthMenu: PropTypes.array,
        recordsTotal: PropTypes.number,
        length: PropTypes.number,
        start: PropTypes.number,
        orderBy: PropTypes.string,
        headerData: PropTypes.array,
        data: PropTypes.array,
        tableClassName: PropTypes.string,
        loadingAction: PropTypes.func,
        isLoading: PropTypes.bool,
        needUpdate: PropTypes.bool,
        withNumPagination: PropTypes.bool
    };

    static defaultProps = {
        className: 'table table-dark table-bordered',
        lengthMenu: [25, 50, 75, 100],
        recordsTotal: 0,
        length: 25,
        start: 0,
        orderBy: null,
        headerData: [],
        data: [],
        tableClassName: '',
        loadingAction: null,
        isLoading: false,
        needUpdate: false,
        withNumPagination: true
    };
}

// DataTable.propTypes = {
//     lengthMenu: PropTypes.array,
//     recordsTotal: PropTypes.number,
//     length: PropTypes.number,
//     start: PropTypes.number,
//     headerData: PropTypes.array,
//     data: PropTypes.array,
//     tableClassName: PropTypes.string,
//     loadingAction: PropTypes.func,
//     isLoading: PropTypes.bool,
//     needUpdate: PropTypes.bool,
//     withNumPagination: PropTypes.bool
// };
//
// DataTable.defaultProps = {
//     className: 'table table-dark table-bordered',
//     lengthMenu: [25, 50, 75, 100],
//     recordsTotal: 0,
//     length: 25,
//     start: 0,
//     headerData: [],
//     data: [],
//     tableClassName: '',
//     loadingAction: null,
//     isLoading: false,
//     needUpdate: false,
//     withNumPagination: true
// };
export default DataTable;
