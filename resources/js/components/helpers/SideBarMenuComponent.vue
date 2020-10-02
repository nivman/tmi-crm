<template>
    <li>
        <a :aria-expanded="isExpanded()" @click="toggle()">
            <slot></slot>
            <span class="caret"></span>
        </a>
        <expanding-component>
            <ul v-show="isExpanded()" class="submenu">
                <slot name="submenu"></slot>
            </ul>
        </expanding-component>
    </li>
</template>

<script>
import ExpandingComponent from './ExpandingComponent.vue';

export default {
    components: { ExpandingComponent },
    props: {
        expand: { type: Boolean, required: true },
    },
    data() {
        return { expanded: this.expand };
    },
    methods: {
        isExpanded() {
            return this.expanded;
        },
        toggle() {
            this.expanded = !this.expanded;
        },
    },
};
</script>

<style lang="scss" scoped>
@import '../../../sass/variables';

.icon {
    vertical-align: baseline;
}
li {
    a {
        position: relative;
        padding: 0.65em 0.75em;
        li a {
            padding: 0.65em 0.75em;
        }
        .caret {
            content: ' ';
            border: 1px solid #4a4a4a;
            width: 0.5em;
            height: 0.5em;
            right: 1em;
            border-right: 0;
            border-top: 0;
            display: block;
            margin-top: -0.25em;
            pointer-events: none;
            position: absolute;
            top: 50%;
            transform: rotate(-45deg);
            transform-origin: center;
            transition: all 0.25s ease-in;
        }
        &[aria-expanded='true'] {
            background-color: $blue;
            color: $white;
            .caret {
                margin-top: -0.075em;
                transform: rotate(135deg);
                border-left-color: #fff;
                border-bottom-color: #fff;
            }
        }
    }

    a + ul {
        padding: 0;
        margin: 0 0 0 18px;
        border-left: 1px dashed $blue;
    }
}
</style>
