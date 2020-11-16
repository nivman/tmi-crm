<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <form autocomplete="off" action="#" @submit.prevent="validateForm()">
            <div class="modal-card animated fastest zoomIn">
                <header class="modal-card-head is-radius-top">
                    <p class="modal-card-title">
                        {{
                            form.id
                                ? "עריכת שדה דינמי"
                                : "הוספת שדה דינמי"
                        }}
                    </p>
                    <button
                        type="button"
                        class="delete"
                        @click="$router.go(-1)"
                    ></button>
                </header>
                <section class="modal-card-body is-radius-bottom">
                    <div class="columns">
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="name">שם</label>
                                <input
                                    id="name"
                                    type="text"
                                    name="name"
                                    class="input"
                                    v-model="form.name"
                                    v-validate="'required'"
                                    :class="{ 'is-danger': errors.has('name') }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("name") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="slug">מזהה</label>
                                <input
                                    id="slug"
                                    name="slug"
                                    type="text"
                                    class="input"
                                    v-model="form.slug"
                                    v-validate="'required'"
                                    :class="{ 'is-danger': errors.has('slug') }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("slug") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="type">סוג</label>
                                <div class="select is-fullwidth">
                                    <select
                                        v-model="form.type"
                                        id="type"
                                        :class="{
                                            'is-danger': errors.has('type')
                                        }"
                                    >
                                        <option value="varchar">טקסט מקוצר</option>
                                        <option value="text">טקסט</option>
                                        <option value="boolean">כן\לא</option>
                                        <option value="datetime">תאריך ושעה</option>
                                        <option value="integer">מספר</option>
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("type") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="is_required">שדה חובה</label>
                                <div class="select is-fullwidth">
                                    <select
                                        v-model="form.is_required"
                                        id="is_required"
                                        :class="{
                                            'is-danger': errors.has(
                                                'is_required'
                                            )
                                        }"
                                    >
                                        <option value="0">לא</option>
                                        <option value="1">כן</option>
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("is_required") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="sort_order"
                                    >סדר מיון</label
                                >
                                <input
                                    type="number"
                                    class="input"
                                    id="sort_order"
                                    name="sort_order"
                                    v-model="form.sort_order"
                                    v-validate="'required|integer'"
                                    :class="{
                                        'is-danger': errors.has('sort_order')
                                    }"
                                />
                                <div class="help is-danger">
                                    {{ errors.first("sort_order") }}
                                </div>
                            </div>
                        </div>
                        <div class="column is-half">
                            <div class="field">
                                <label class="label" for="entities">סוג טבלה</label>
                                <div class="select is-fullwidth is-multiple">
                                    <select
                                        size="5"
                                        multiple
                                        id="entities"
                                        v-model="form.entities"
                                        v-validate="'required'">
                                        <option value="App\Account">חשבון</option>
                                        <option value="App\Customer">לקוח</option>
                                        <option value="App\Expense">הוצאות</option>
                                        <option value="App\Income" >הכנסות</option>
                                        <option value="App\Invoice">חשבוניות</option>
                                        <option value="App\Product" >מוצרים</option>
                                        <option value="App\Purchase">רכישות</option>
                                        <option value="App\Vendor">ספקים</option>
                                        <option value="App\Project">פרוייקטים</option>
                                    </select>
                                </div>
                                <div class="help is-danger">
                                    {{ errors.first("entities") }}
                                </div>
                            </div>
                            <div class="field">
                                <label class="label" for="description">תיאור</label>
                                <textarea
                                    rows="4"
                                    type="text"
                                    id="description"
                                    class="textarea"
                                    name="description"
                                    v-model="form.description">
                                </textarea>
                            </div>
                          <div class="field">
                            <div class="modal-card-head" style="border: none">
                            <label class="label" for="hideInList" style="margin-bottom:0px; margin-left: 5px">הצגה בטבלאות: </label>
                            <VueToggles
                                id="hideInList"
                                style="direction: ltr; margin-left: 9px;"
                                @click="toggleHideInList"
                                :value="hideInList"
                                height="20"
                                width="60"
                                fontSize="12"
                                uncheckedColor="#000000"
                                checkedColor="#000000"
                                fontWeight="bold"
                                uncheckedText="הצג"
                                checkedText="הסתר"
                                checkedBg="lightgrey"
                                uncheckedBg="#b4d455"/>
                          </div>
                        </div>
                        </div>
                    </div>
                    <div class="field">
                        <button
                            type="submit"
                            class="button is-link is-fullwidth"
                            :disabled="errors.any()">
                            הכנסה
                        </button>
                    </div>
                </section>
            </div>
        </form>
        <button
            class="modal-close is-large is-hidden-mobile"
            aria-label="close"
            @click="$router.go(-1)"
        ></button>
    </div>
</template>

<script>
import VueToggles from 'vue-toggles';
export default {
    data() {
        return {
          hideInList: false,
            form: new this.$form({
                id: "",
                name: "",
                slug: "",
                type: "varchar",
                sort_order: "",
                description: "",
                is_required: "0",
                entities: [],
                hide_in_list: 0
            })
        };
    },
    computed: {
        is_edit() {
            return this.form.id && this.form.id !== "";
        }
    },
    created() {
        if (this.$route.params.id) {
            this.fetchExpense(this.$route.params.id);
        }
    },
    methods: {
        submit() {
            if (this.form.id && this.form.id !== "") {
                this.form
                    .put(`app/custom_fields/${this.form.id}`)
                    .then(() => {
                        this.$event.fire("refreshCustomFieldsTable");
                        this.notify(
                            "success",
                            "שדה דינמי עודכן"
                        );
                        this.$router.push("/settings/fields");
                    })
                    .catch(err => {
                        this.$event.fire("appError", err.response);
                    });
            } else {
                this.form
                    .post("app/custom_fields")
                    .then(() => {
                        this.$event.fire("refreshCustomFieldsTable");
                        this.notify(
                            "success",
                            "שדה דינמי נוסף"
                        );
                        this.$router.push("/settings/fields");
                    })
                    .catch(err => {
                        this.$event.fire("appError", err.response);
                    });
            }
        },
        fetchExpense(id) {
            this.$http
                .get(`app/custom_fields/${id}`)
                .then(res => {
                    let entities = res.data.entities.map(e => e.entity_type);
                    let is_required = res.data.is_required ? "1" : "0";
                    this.form = new this.$form(res.data);
                    this.form.is_required = is_required;
                    this.form.hide_in_list = res.data.hide_in_list;
                    this.hideInList = res.data.hide_in_list === 0 ? false : true;
                    this.form.entities = entities;
                })
                .catch(err => {
                    this.$event.fire("appError", err.response);
                });
        },
      toggleHideInList() {

        this.hideInList = !this.hideInList;
        this.form.hide_in_list = !this.hideInList ? 0 : 1 ;

      },
        validateForm() {
            this.$validator
                .validateAll()
                .then(result => {
                    if (result) {
                        this.submit();
                    }
                })
                .catch(err => this.$event.fire("appError", err));
        },
        hasValue(v) {
            return (
                this.form.entities.filter(e => e.entity_type == v).length > 0
            );
        }
    },
  components: {VueToggles}
};
</script>
