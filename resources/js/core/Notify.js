const Notify = {
    methods: {
        notify: function(type, text, title, duration) {
            this.$notify({
                type: type,
                text: text,
                title: title,
                group: 'app',
                duration: duration ? duration : (type == 'error' ? 15000 : 5000),
            })
        }
    }
};

export default Notify;
