<template>
    <div class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card animated fastest zoomIn">
            <header class="modal-card-head is-radius-top">
                <p class="modal-card-title">Log Details</p>
                <button type="button" class="delete" @click="$router.go(-1)"></button>
            </header>
            <section class="modal-card-body is-radius-bottom">
                <loading v-if="loading"></loading>
                <div v-else>
                    <table class="table is-bordered is-rounded is-rounded-body is-striped is-narrow is-hoverable is-fullwidth m-b-none">
                        <tbody>
                            <tr>
                                <td>Created at</td>
                                <td>{{ log.created_at | formatDate($store.state.settings.ac.dateformat + ' HH:mm') }}</td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td>{{ log.log_name }}</td>
                            </tr>
                            <tr>
                                <td>Description</td>
                                <td>{{ log.description }}</td>
                            </tr>
                            <tr>
                                <td>User</td>
                                <td>{{ log.user.name }}</td>
                            </tr>
                            <tr>
                                <td>Subject</td>
                                <td>{{ log.subject_type.split('\\')[1] }}</td>
                            </tr>
                            <tr v-if="log.subject">
                                <td colspan="2">
                                    <strong>Subject Details</strong>:<br />
                                    <span v-if="log.subject.id">
                                        ID:
                                        <strong>{{ log.subject.id }}</strong
                                        ><br />
                                    </span>
                                    <span v-if="log.subject.date">
                                        Date:
                                        <strong>{{ log.subject.date | formatDate($store.state.settings.ac.dateformat) }}</strong
                                        ><br />
                                    </span>
                                    <span v-if="log.subject.reference">
                                        Reference:
                                        <strong>{{ log.subject.reference }}</strong
                                        ><br />
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="log.properties">
                                <td colspan="2">
                                    <strong>Properties</strong>:<br />
                                    {{ log.properties.attributes }}<br />
                                    <span v-if="log.properties.old">
                                        <strong>Old</strong>:<br />
                                        {{ log.properties.old }}
                                    </span>
                                </td>
                            </tr>
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
            log: null,
        };
    },
    created() {
        this.$http
            .get(`app/logs/${this.$route.params.id}`)
            .then(res => {
                this.log = res.data;
                this.loading = false;
            })
            .catch(err => this.$event.fire('appError', err.response));
    },
};
</script>
