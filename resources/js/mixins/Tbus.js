import _debounce from "lodash/debounce";
const inflection = require("inflection");
import bus from "./../helpers/vue-tables-2/compiled/bus.js";
import TableFiltersComponent from "../components/helpers/TableFiltersComponent";
import PanelFiltersComponent from "../components/helpers/PanelFiltersComponent";

function tBus(url) {
    var s = url.split("/");
    var u = s[s.length - 1];
    var table = inflection.camelize(u) + "Table";
    var tbl = inflection.camelize(u, true) + "Table";
    var name = inflection.humanize(inflection.singularize(u), true);

    return {
        components: { TableFiltersComponent, PanelFiltersComponent },
        data() {
            return {
                filters: 0,
                loading: true,
                reference: null,
                filtering: false,
                options: {
                    perPage: this.$store.state.settings.ac.noRows
                }
            };
        },
        created: function() {
            this.$event.listen("refresh" + table, () => {
                this.refreshTable();
            });

            this.$watch(
                "filters",
                _debounce(function() {
                    this.search();
                }, 100),
                { deep: true }
            );

            bus.$on("vue-tables." + tbl + ".loading", () => (this.loading = true));
            bus.$on("vue-tables." + tbl + ".loaded", () => (this.loading = false));
        },
        computed: {
            url() {
                return "/" + url + "?filters=" + JSON.stringify(this.filters);
            },
            dateformat() {
                return this.$store.state.settings.ac.dateformat;
            }
        },
        // watch: {
        //     filters: {
        //         handler: function(v) {
        //             this.search();
        //         },
        //         deep: true
        //     }
        // },
        methods: {
            refreshTable(resetPage) {
                if (this.$refs[tbl]) {
                    if (resetPage) {
                        this.$refs[tbl].setPage(1);
                    } else {
                        this.$refs[tbl].getData();
                    }
                }
            },
            search: _debounce(function() {
                if (!this.filters.date_range || this.filters.date_range.length > 21) {
                    this.refreshTable(true);
                }
            }, 500),
            deleteRecord(id) {
                this.$modal.show("dialog", {
                    title: "למחוק " + name + "!",
                    text: "הפעולה תמחק את הרשומה ללא אפשרות שיחזור.",
                    buttons: [
                        {
                            title: "כן למחוק",
                            class: "button is-danger is-radiusless is-radius-bottom-left",
                            handler: () => {
                                this.$http
                                    .delete(`${url}/delete/${id}`)
                                    .then(res => {

                                        this.notify("success", name + " has been successfully deleted.");
                                        this.refreshTable();
                                        this.$modal.hide("dialog");
                                    })
                                    .catch(err => {
                                        this.$event.fire("appError", err.response);
                                    });
                            }
                        },
                        { title: "ביטול", class: "button is-warning is-radiusless is-radius-bottom-right" }
                    ]
                });
            }
        }
    };
}

export default tBus;
