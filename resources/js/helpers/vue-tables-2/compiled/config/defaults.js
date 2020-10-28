"use strict";

module.exports = function () {
  return {
    dateColumns: [],
    listColumns: {},
    datepickerOptions: {
      locale: {
        cancelLabel: 'ניקוי'
      }
    },
    datepickerPerColumnOptions: {},
    initialPage: 1,
    perPage: 10,
    perPageValues: [10, 25, 50, 100],
    groupBy: false,
    params: {},
    sortable: true,
    filterable: true,
    initFilters: {},
    customFilters: [],
    templates: {},
    debounce: 250,
    dateFormat: "DD/MM/YYYY",
    toMomentFormat: true,
    skin: false,
    columnsDisplay: {},
    columnsDropdown: false,
    texts: {
      count: "מציג {from} מ {to} מתוך {count} רשומות|{count} רשומות|רשומה אחת",
      first: 'ראשון',
      last: 'אחרון',
      filter: "סינון:",
      filterPlaceholder: "חיפוש חופשי",
      limit: "רשומות:",
      page: "עמוד:",
      noResults: "לא נמצאו רשומות",
      filterBy: "סינון {column}",
      loading: 'טעינה',
      defaultOption: 'בחירה {column}',
      columns: 'עמודות'
    },
    sortIcon: {
      is: 'glyphicon-sort',
      base: 'glyphicon',
      up: 'glyphicon-chevron-up',
      down: 'glyphicon-chevron-down'
    },
    sortingAlgorithm: function sortingAlgorithm(data, column) {
      return data.sort(this.getSortFn(column));
    },

    customSorting: {},
    multiSorting: {},
    clientMultiSorting: true,
    serverMultiSorting: false,
    filterByColumn: false,
    highlightMatches: false,
    orderBy: false,
    footerHeadings: false,
    headings: {},
    headingsTooltips: {},
    pagination: {
      dropdown: false,
      chunk: 10,
      edge: false,
      align: 'center',
      nav: 'fixed'
    },
    childRow: false,
    childRowTogglerFirst: true,
    uniqueKey: 'id',
    requestFunction: false,
    requestAdapter: function requestAdapter(data) {
      return data;
    },
    responseAdapter: function responseAdapter(resp) {

      var data = this.getResponseData(resp);

      return {
        data: data.data,
        count: data.count
      };
    },
    requestKeys: {
      query: 'query',
      limit: 'limit',
      orderBy: 'orderBy',
      ascending: 'ascending',
      page: 'page',
      byColumn: 'byColumn'
    },
    rowClassCallback: false,
    preserveState: false,
    saveState: false,
    storage: 'local',
    columnsClasses: {}
  };
};