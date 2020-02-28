<template>
    <div class="inner-page">
        <header class="inner-page__header d-flex justify-content-between align-items-center">
            <h1 class="inner-page__title">User List</h1>
            <div class="action-bar">
                <router-link to="/user" class="btn btn-success">ADD USER</router-link>
            </div>
        </header>
        <section class="inner-page__content container-fluid">
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <b-table
                                        :items="users"
                                        :fields="fields"
                                        :busy="loading"
                                >
                                    <div slot="table-busy" class="text-center text-warning my-2">
                                        <b-spinner class="align-middle" variant="warning"></b-spinner>
                                        <strong>Loading...</strong>
                                    </div>
                                    <template slot="actions" slot-scope="row">
                                        <router-link :to="'/user/' + row.item.id"><i class="fa fa-pencil"></i>
                                        </router-link>
                                        <i class="ml-2 fa fa-trash-o cursor-pointer" v-if="userId !== row.item.id"
                                           @click="deleteUser(row.item.id)"></i>
                                    </template>
                                </b-table>
                                <template v-if="!loading">
                                    <b-pagination
                                            align="center"
                                            v-model="currentPage"
                                            :total-rows="total"
                                            :per-page="perPage"
                                            aria-controls="my-table"
                                    ></b-pagination>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
<script>
    import {mapState} from "vuex";

    export default {
        name: "UserList",
        data() {
            return {
                userId: parseInt(localStorage.getItem('userId')),
                fields: [
                    'firstName',
                    'lastName',
                    'email',
                    {key: 'roleName', label: 'Role'},
                    {key: 'actions', label: 'Actions'}
                ],
                currentPage: 1,
            };
        },
        methods: {
            deleteUser(id) {
                this.$store.dispatch('user/deleteUser', id);
                this.currentPage = 1;
            },
            requestUsers() {
                this.$store.dispatch('user/getUsers', this.currentPage);
            }
        },
        watch: {
            currentPage: {
                handler: 'requestUsers',
                immediate: true,
            }
        },
        computed: mapState({
            users: state => state.user.users,
            loading: state => state.user.loading,
            perPage: state => state.user.perPage,
            total: state => state.user.total,
        })
    };
</script>
<style lang="scss" scoped>
    .cursor-pointer {
        cursor: pointer;
    }
</style>
