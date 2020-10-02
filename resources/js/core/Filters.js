const Filters = {
    capitalize: (value = "") =>
        value
            ? value
                  .toString()
                  .charAt(0)
                  .toUpperCase() + value.toString().slice(1)
            : "",

    sc2txt: (value = "", sp = "_") => {
        let v = value.split(sp).join(" ");
        return (
            v
                .toString()
                .charAt(0)
                .toUpperCase() + v.toString().slice(1)
        );
    },

    formatDate: (value = "", format = "DD/MM/YYYY hh:mm A") => (value ? moment(String(value), "YYYY-MM-DD HH:mm").format(format) : ""),

    formatNumber: (value = 0, d = 2, ts = ",", ds = ".") => accounting.formatNumber(value, d, ts, ds),

    formatJournalBalance: (value = 0, d = 2, ts = ",", ds = ".") => accounting.formatNumber(value / 100, d, ts, ds),

    formatDecimal: (value = 0, d = 4) => accounting.formatNumber(value, d, "", "."),

    unformatNumber: (value = 0, ds = ".") => accounting.unformat(value, ds)
};

export default Filters;
