"use strict";

module.exports = function (h) {
  var _this = this;

  return function (perpageValues, classes, id) {

    return perpageValues.length > 1 ? h(
      "div",
      { "class": classes.selectWrapper },
      [h(
        "select",
        { "class": classes.select,
          attrs: { name: "limit",

            id: id
          },
          domProps: {
            "value": _this.limit
          },
          on: {
            "change": _this.setLimit.bind(_this)
          }
        },
        [perpageValues]
      )]
    ) : '';
  };
};