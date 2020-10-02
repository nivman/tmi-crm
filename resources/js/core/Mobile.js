const Mobile = {
    install(Vue, options = {}) {
        const vm = new Vue({
            data: {
                mobile: window.navigator.onLine
            }
        });

        window.addEventListener("resize", function handleMobile(e) {
            vm.$data.mobile = window.innerWidth <= 768;
        });
        window.addEventListener("load", function handleMobile(e) {
            vm.$data.mobile = window.innerWidth <= 768;
        });

        Vue.mixin({
            computed: {
                isMobile() {
                    return vm.$data.mobile;
                }
            }
        });
    }
};

if (typeof window !== "undefined" && window.Vue) {
    window.Vue.use(Mobile);
}

export default Mobile;
