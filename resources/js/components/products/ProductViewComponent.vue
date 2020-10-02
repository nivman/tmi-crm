<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card is-medium animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">Product Details</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-else>
                    <figure v-if="product.image" class="image m-b-md">
                        <img :src="product.image" :alt="product.name" />
                    </figure>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>Created at</td>
                                <td>{{ product.created_at | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}</td>
                            </tr>
                            <tr>
                                <td>Code</td>
                                <td>{{ product.code }}</td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>{{ product.name }}</td>
                            </tr>
                            <tr>
                                <td>Category</td>
                                <td>{{ product.categories[0].name }}</td>
                            </tr>
                            <tr>
                                <td>Taxes</td>
                                <td>
                                    <span v-html="showTaxes(product.taxes)"></span>
                                </td>
                            </tr>
                            <tr>
                                <td>Cost</td>
                                <td>{{ product.cost | formatNumber }}</td>
                            </tr>
                            <tr>
                                <td>Price</td>
                                <td>{{ product.price | formatNumber }}</td>
                            </tr>
                            <template v-if="product.attributes">
                                <tr v-for="attr in product.attributes" :key="attr.slug">
                                    <td>{{ attr.name }}</td>
                                    <td v-if="attr.type == 'datetime' && product[attr.slug]">
                                        {{ product[attr.slug]['date'] | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}
                                    </td>
                                    <td v-else>{{ product[attr.slug] }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <button class="modal-close is-large is-hidden-mobile" aria-label="close" @click="$router.go(-1)"></button>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading: true,
            product: null,
        };
    },
    created() {
        this.$http
            .get(`app/products/${this.$route.params.id}`)
            .then(res => {
                this.product = res.data;
                this.loading = false;
            })
            .catch(err => {
                this.$event.fire('appError', err.response);
            });
    },
    methods: {
        showTaxes(taxes) {
            return taxes.map(tax => '<span class="tag">' + tax.name + '</span>').join(' ');
        },
    },
};
</script>
