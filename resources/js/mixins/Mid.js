const mId = {
    created: function () {
        if (this.$store.state.settings.ac.idColumn != 1) {
            this.columns.unshift('id');
            this.filters = { id: '', ...this.filters };
        }
    }
};

export default mId;
