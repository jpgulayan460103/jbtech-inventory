<template>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <form id="formData" @submit.prevent="submitForm">
                    <div class="form-group">
                        <label for="email">Username</label>
                        <input type="text" class="form-control" aria-describedby="emailHelp" v-model="formData.email" placeholder="Enter Item Name" required :class="{'is-invalid': (typeof formErrors.email != 'undefined' && formErrors.email.length != 0)}">
                        <div class="invalid-feedback">
                            {{ formErrors.email ? formErrors.email[0] : "" }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" aria-describedby="emailHelp" v-model="formData.password" placeholder="Enter Item Name" :class="{'is-invalid': (typeof formErrors.password != 'undefined' && formErrors.password.length != 0)}">
                        <div class="invalid-feedback">
                            {{ formErrors.password ? formErrors.password[0] : "" }}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Fullname</label>
                        <input type="text" class="form-control" aria-describedby="emailHelp" v-model="formData.name" placeholder="Enter Item Name" required :class="{'is-invalid': (typeof formErrors.name != 'undefined' && formErrors.name.length != 0)}">
                        <div class="invalid-feedback">
                            {{ formErrors.name ? formErrors.name[0] : "" }}
                        </div>
                    </div>

                    <div class="form-group">
                            <label for="reorder_level">User Type</label>
                            <select class="form-control" v-model="formData.account_type" disabled="user.account_type == 'warehouse_admin'">
                                <option value="">Select User Type</option>
                                <!-- <option value="admin">Admin</option> -->
                                <option value="warehouse_admin">Warehouse Admin</option>
                                <option value="user">User</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>

                    <div class="form-group" v-if="formData.account_type =='warehouse_admin'">
                        <label for="reorder_level">To</label>
                        <select class="form-control" v-model="formData.warehouse_id">
                            <option value="">Select Warehouse</option>
                            <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Submit</button>
                </form>
            </div>
        
        <div class="col-md-8">
            <!-- <h2>Items List</h2> -->
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Username</th>
                        <th scope="col">Name</th>
                        <th scope="col">Account Type</th>
                        <th scope="col">Warehouse</th>
                        <th scope="col" class="text-center ">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(userTable, index) in users" :key="userTable.id" :class="{'table-secondary': (selectedUser.id == userTable.id)}">
                        <td>{{ userTable.email }}</td>
                        <td>{{ userTable.name }}</td>
                        <td>{{ userTable.account_type }}</td>
                        <td>{{ userTable.warehouse ? userTable.warehouse.name : "" }}</td>
                        <td class="text-center ">
                            <div class="flex space-x-4">
                                <span class="custom-pointer" @click="editSelectUser(userTable, index)" v-if="user.account_type == 'admin' || user.id == userTable.id">Edit</span>
                                <span class="custom-pointer" @click="deleteUser(userTable)" v-if="user.account_type == 'admin'">| Delete</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getUsers();
        },
        props: ['warehouses','user'],
        data() {
            return {
                formData: {
                    account_type: this.user.account_type == 'warehouse_admin' ? 'user' : "",
                    warehouse_id: ""
                },
                formErrors: [],
                users: [],
                selectedUser: {
                    id: null
                },
                formType: "create",
            }
        },
        methods: {
            async submitForm(){
                if(this.formData.account_type == 'admin'){
                    this.formData.warehouse_id = null;
                }
                if(this.formData.password && this.formData.password.trim() == ''){
                    delete this.formData.password;
                }
                if(this.formType == 'create'){
                    this.createUsers();
                }else{
                    this.updateUsers();
                }
            },
            async getUsers(){
                let res = await axios.get('/api/users');
                this.users = res.data;
            },
            async createUsers(){
                this.formErrors = {};
                axios.post('/api/users', this.formData)
                .then(res => {
                    this.getUsers();
                    this.formData = {
                        account_type: "",
                        warehouse_id: ""
                    };
                    this.formType = "create";
                })
                .catch(err => {
                    this.formErrors = err.response.data.errors;
                })
                .then(res => {})
                ;
            },

            async updateUsers(){
                this.formErrors = {};
                axios.put(`/api/users/${this.formData.id}`, this.formData)
                .then(res => {
                    this.getUsers();
                    this.formData = {
                        account_type: "",
                        warehouse_id: ""
                    };
                    this.formType = "create";
                })
                .catch(err => {
                    this.formErrors = err.response.data.errors;
                })
                .then(res => {})
                ;
            },
            editSelectUser(user, index){
                this.formType = "update";
                this.selectedUser = user;
                this.formData = {
                    ...this.formData,
                    ...user
                };
            },
            
            deleteUser(user){
                axios.delete(`/api/users/${user.id}`)
                .then(res => {
                    this.getUsers();
                })
            }
        }
    }
</script>