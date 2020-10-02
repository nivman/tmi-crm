class Form {
    constructor(data) {
        this.uploads = [];
        this.formdata = new FormData();
        for (let field in data) {
            this[field] = data[field];
            this.formdata.append(field, data[field]);
        }
    }

    appendData(field, data) {
        this.uploads.push(field);
        this.formdata.append(field, data);
    }

    data() {
        let data = {};
        delete this.uploads;
        for (const key of Object.keys(this)) {
            if (key != "formdata") {
                data[key] = this[key];
            }
        }
        return data;
    }

    getformdata() {
        const uploads = this.uploads;
        delete this.uploads;
        for (const key of Object.keys(this)) {
            if (key != "formdata" && !uploads.includes(key)) {
                if (Array.isArray(this[key])) {
                    for (var i = 0; i < this[key].length; i++) {
                        this.formdata.set(key+'[]', this[key][i]);
                    }
                } else {
                    this.formdata.set(key, this[key]);
                }
            }
        }
        return this.formdata;
    }

    reset() {
        for (const key of Object.keys(this)) {
            this[key] = "";
        }
    }

    put(url, mp = false) {
        this._method = "PUT";
        this.formdata.append("_method", "PUT");
        return this.submit("post", url, mp);
    }

    patch(url, mp = false) {
        this._method = "PATCH";
        this.formdata.append("_method", "PATCH");
        return this.submit("post", url, mp);
    }

    post(url, mp = false) {
        return this.submit("post", url, mp);
    }

    delete(url) {
        return this.submit("delete", url);
    }

    submit(requestType, url, mp = false) {
        return new Promise((resolve, reject) => {
            // const config = data ? { headers: { "content-type": "multipart/form-data" } } : {};
            window.axios[requestType](url, mp ? this.getformdata() : this.data())
                .then(response => {
                    setTimeout(() => {
                        this.reset();
                    }, 100);
                    resolve(response);
                })
                .catch(error => reject(error));
        });
    }
}

export default Form;
