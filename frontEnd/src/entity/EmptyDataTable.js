export default class EmptyDataTable {
    constructor() {
        this.recordsTotal = 0;
        this.recordsFiltered = 0;
        this.data = [];
        this.loaded = false;
        this.isLoading = true;
        this.needUpdate = false;
    }
}