'use strict';

module.exports = function () {
    return {
        framework: 'bulma',
        table: 'table is-bordered is-striped is-hoverable is-fullwidth is-responsive',
        row: '',
        column: '',
        labelWrapper: 'field-label is-normal',
        label: 'label is-pulled-left',
        inputWrapper: 'control',
        input: 'input',
        selectWrapper: 'select',
        select: 'select',
        field: 'field',
        inline: 'is-horizontal',
        right: 'is-pulled-right',
        left: 'is-pulled-left',
        center: 'has-text-centered',
        contentCenter: '',
        icon: 'icon',
        small: '',
        nomargin: 'marginless',
        button: 'button',
        groupTr: 'is-selected',
        dropdown: {
            container: 'dropdown',
            trigger: 'dropdown-trigger',
            menu: 'dropdown-menu',
            content: 'dropdown-content',
            item: 'dropdown-item',
            caret: 'fa fa-angle-down'
        },
        pagination: {
            nav: 'pagination is-gapless',
            count: '',
            wrapper: 'pagination is-gapless',
            list: 'pagination-list',
            item: '',
            link: 'pagination-link',
            next: '',
            prev: '',
            active: 'is-current'
        }
    };
};