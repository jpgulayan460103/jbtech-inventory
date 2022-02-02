<template>
    <div class="container">
        <div class="row">
            <notifications group="foo"
                    position="bottom left"
                    :max="3"
                    :width="400">
            </notifications>
            <div class="col-md-4">
                <div v-if="formType == 'create' || formType == 'edit_items'">
                    <h2 v-if="formType == 'create'">Create Item</h2>
                    <h2 v-else>Update Item</h2>
                    <form id="createItemForm" @submit.prevent="submitForm">
                        <div class="form-group">
                            <label for="category">Category</label>
                            <input type="text" class="form-control" v-model="createItemFormData.category" placeholder="Enter Category" required :class="{'is-invalid': (typeof createItemFormError.category != 'undefined' && createItemFormError.category.length != 0)}">
                            <div class="invalid-feedback">
                                {{ createItemFormError.category ? createItemFormError.category[0] : "" }}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Item Name</label>
                            <input type="text" class="form-control" aria-describedby="emailHelp" v-model="createItemFormData.name" placeholder="Enter Item Name" required :class="{'is-invalid': (typeof createItemFormError.name != 'undefined' && createItemFormError.name.length != 0)}">
                            <div class="invalid-feedback">
                                {{ createItemFormError.name ? createItemFormError.name[0] : "" }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reorder_level">Type</label>
                            <select class="form-control" v-model="createItemFormData.item_type">
                                <option value="per_box">Per Box</option>
                                <option value="per_piece">Per Piece</option>
                            </select>
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="reorder_level">Reorder Level</label>
                            <input type="number" min="0" class="form-control" v-model="createItemFormData.reorder_level" placeholder="Enter Reorder Level" required>
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>

                        
                        <button type="submit" class="btn btn-primary mt-4">Submit</button>
                        <button type="button" class="btn btn-danger mt-4" @click.prevent="resetForms()">Reset Form</button>
                    </form>
                </div>
                <div v-if="formType == 'add_items'">

                    <h2>Add Item</h2>
                    <form id="addItemForm" @submit.prevent="addItem">

                        <div class="form-group is-invalid">
                            <label for="name">Item Name</label>
                            <input type="text" class="form-control is-valid" aria-describedby="emailHelp" v-model="addItemFormData.name" placeholder="Enter Item Name" readonly>
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="reorder_level">To</label>
                            <select class="form-control" v-model="addItemFormData.warehouse_id" :disabled="user.account_type != 'admin'">
                                <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="serial_number">Quantity {{ selectedItem.item_type_string }}</label>
                            <input type="number" min="1" class="form-control" v-model="addItemFormData.quantity" placeholder="Enter Quantity" required :disabled="selectedItem.item_type == 'per_piece'">
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" class="form-control" v-model="addItemFormData.remarks" placeholder="Enter Remarks" required>
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="serial_number">Serial/Barcode Number</label>
                            <input type="text" class="form-control" v-model="addItemFormData.serial_number" placeholder="Enter Serial/Barcode Number" required :class="{'is-invalid': (typeof addItemDetailFormErrors.serial_number != 'undefined' && addItemDetailFormErrors.serial_number.length != 0)}">
                            <div class="invalid-feedback">
                                {{ addItemDetailFormErrors.serial_number ? addItemDetailFormErrors.serial_number[0] : "" }}
                            </div>
                        </div>
                        
                        
                        <button type="submit" class="btn btn-primary mt-4">Submit</button>
                        <button type="button" class="btn btn-danger mt-4" @click.prevent="closeAddItemForm()">Close</button>
                    </form>
                </div>
            </div>
            <div class="col-md-8">
                <h2>Items List</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Type</th>
                            <th scope="col">Category</th>
                            <th scope="col">Item Name</th>
                            <th scope="col">Reorder Level</th>
                            <th scope="col">Main Warehouse Qty</th>
                            <th scope="col">JBtech Warehouse Qty</th>
                            <th scope="col" class="text-center ">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in items" :key="item.id" :class="{'table-secondary': (selectedItem.id == item.id)}">
                            <td>{{ item.item_type_string }}</td>
                            <td>{{ item.category }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.reorder_level }}</td>
                            <td class="text-center">{{ item.total_quantity_1 }}</td>
                            <td class="text-center">{{ item.total_quantity_2 }}</td>
                            <td class="text-center ">
                                <div class="flex space-x-4">
                                    <span class="custom-pointer" @click="selectItem(item, index)">Add</span> |
                                    <span class="custom-pointer" @click="editSelectItem(item, index)">Edit</span> |
                                    <span class="custom-pointer">Delete</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item" :class="{ active: pagination.active }" v-for="(pagination, index) in itemPaginations" :key="index" @click="navigateItemPages(pagination.label)">
                            <a class="page-link" href="javascript:void(0);" v-if="pagination.url != null">
                                <span v-html="pagination.label"></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <hr v-if="formType !='edit_items'">
        <br>

        <!-- Serial Numbers -->
        <div class="row" v-if="formType !='edit_items'">
            <div class="col-md-12">
                <h2>{{ selectedItem.category }} {{ selectedItem.name }} Serial Numbers</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Serial Number</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Warehouse</th>
                            <th scope="col">Added</th>
                            <!-- <th scope="col" class="text-center ">Actions</th> -->
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in itemDetails" :key="item.id" :class="{'table-secondary': (selectedItemDetail.id == item.id)}">
                            <td>{{ item.serial_number }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.remarks }}</td>
                            <td>{{ item.warehouse.name }}</td>
                            <td>{{ item.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item" :class="{ active: pagination.active }" v-for="(pagination, index) in itemDetailPaginations" :key="index" @click="navigateItemDetailPages(pagination.label)">
                            <a class="page-link" href="javascript:void(0);" v-if="pagination.url != null">
                                <span v-html="pagination.label"></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
        <hr v-if="formType !='edit_items'">
        <br>


        <!-- History -->
        <div class="row" v-if="formType !='edit_items'">
            <div class="col-md-12">
                <h2>{{ selectedItem.category }} {{ selectedItem.name }} In/Out History</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">In/Out</th>
                            <th scope="col">Serial Number</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Warehouse</th>
                            <th scope="col">Added</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Remaining</th>
                            <th scope="col">Request Number</th>
                            <th scope="col">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in itemHistories" :key="index" :class="{'table-secondary': (selectedItemHistory.id == item.id)}">
                            <td>{{ item.history_type }}</td>
                            <td>{{ item.item_detail.serial_number }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.item_detail.remarks }}</td>
                            <td>{{ item.item_detail.warehouse.name }}</td>
                            <td>{{ item.item_detail.created_at }}</td>
                            <td>{{ item.stock }}</td>
                            <td>{{ item.remain }}</td>
                            <td>
                                <a :href="`/requests/${item.request_item.id}`" v-if="item.request_item">
                                    {{ item.request_item ? item.request_item.request_number : "" }}
                                </a>
                            </td>
                            <td>{{ item.user ? item.user.name : "" }}</td>
                        </tr>
                    </tbody>
                </table>
                <nav aria-label="Page navigation example">
                    <ul class="pagination">
                        <li class="page-item" :class="{ active: pagination.active }" v-for="(pagination, index) in itemHistoryPaginations" :key="index" @click="navigateItemHistoryPages(pagination.label)">
                            <a class="page-link" href="javascript:void(0);" v-if="pagination.url != null">
                                <span v-html="pagination.label"></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
            this.getItem();
        },
        props: ['warehouses','user'],
        data() {
            return {
                createItemFormData: {
                    name: "",
                    category: "",
                    reorder: 0,
                    item_type: "per_box",
                    user_id: this.user.id,
                },
                createItemFormError:{

                },
                addItemFormData: {
                    name: "",
                    serial_number: "",
                    quantity: 1,
                    item_id: null,
                    remarks: "",
                    user_id: this.user.id,
                },
                addItemDetailFormErrors:{
                },
                items: [],
                itemDetails: [],
                itemHistories: [],
                formType: "create",
                selectedItem: {},
                selectedItemDetail: {},
                selectedItemHistory: {},
                itemPaginations: [],
                itemDetailPaginations: [],
                itemHistoryPaginations: [],
                itemFilterData: {
                    page: 1
                },
                itemDetailFilterData: {
                    page: 1,
                    warehouse_id: this.user.account_type != 'admin' ? this.user.warehouse_id : ""
                },
                itemHistoryFilterData: {
                    page: 1,
                    warehouse_id: this.user.account_type != 'admin' ? this.user.warehouse_id : ""
                }
            }
        },
        methods: {
            async getItem(){
                let res = await axios.get('/api/items',{
                    params: this.itemFilterData
                });
                this.items = res.data.data;
                this.itemPaginations = res.data.links;
            },
            async getItemDetails(){
                let id = this.selectedItem.id;
                let res = await axios.get(`/api/items/${id}/details`,{
                    params: this.itemDetailFilterData
                });
                this.itemDetails = res.data.data;
                this.itemDetailPaginations = res.data.links;
            },
            async getItemHistory(){
                let id = this.selectedItem.id;
                let res = await axios.get(`/api/items/${id}/history`,{
                    params: this.itemHistoryFilterData
                });
                this.itemHistories = res.data.data;
                this.itemHistoryPaginations = res.data.links;
            },
            async submitForm(){
                if(this.formType == "create"){
                    this.createItem();
                }else{
                    this.updateItem();
                }
            },

            async createItem(){
                this.createItemFormError = {};
                axios.post('/api/items', this.createItemFormData)
                .then(res => {
                    this.$notify({
                        group: 'foo',
                        title: `Success`,
                        text: `${this.createItemFormError.name} as been added`,
                        type: "success"
                    });
                    this.createItemFormData.name = "";
                    this.createItemFormData.item_type = "per_box";
                    this.createItemFormData.reorder_level = "";
                    this.getItem();
                })
                .catch(err => {
                    this.createItemFormError = err.response.data.errors;
                })
                .then(res => {})
                ;
            },

            async updateItem(){
                this.createItemFormError = {};
                axios.put(`/api/items/${this.createItemFormData.id}`, this.createItemFormData)
                .then(res => {
                    this.$notify({
                        group: 'foo',
                        title: `Success`,
                        text: `${this.createItemFormError.name} as been added`,
                        type: "success"
                    });
                    this.createItemFormData = {
                        name: "",
                        category: "",
                        reorder: 0,
                        item_type: "per_box",
                        user_id: this.user.id,
                    }
                    this.formType = "create";
                    this.getItem();
                })
                .catch(err => {
                    this.createItemFormError = err.response.data.errors;
                })
                .then(res => {})
                ;
            },
            async addItem(){
                this.addItemDetailFormErrors = {};
                axios.post(`/api/items/${this.addItemFormData.item_id}/serial`, this.addItemFormData)
                .then(async res => {
                    await this.getItem();
                    await this.getItemDetails();
                    await this.getItemHistory();
                    this.$notify({
                        group: 'foo',
                        title: `${this.addItemFormData.name}`,
                        text: `${this.addItemFormData.serial_number} as been added`,
                        type: "success"
                    });
                    this.addItemFormData.serial_number = "";
                })
                .catch(err => {
                    this.addItemDetailFormErrors = err.response.data.errors;
                })
                .then(res => {});
            },
            async selectItem(item, index){
                this.formType = "add_items";
                this.selectedItem = item;
                this.addItemFormData = {
                    ...this.addItemFormData,
                    name: item.name,
                    item_id: item.id,
                    per_piece: item.per_piece,
                    warehouse_id: this.user.account_type == 'admin' ? this.warehouses[0].id : this.user.warehouse_id,
                };
                if(item.item_type == 'per_piece'){
                    this.addItemFormData.quantity = 1;
                }
                this.itemDetails = [];
                this.itemDetailPaginations = [];
                this.itemDetailFilterData.page = 1;
                await this.getItemDetails();
                await this.getItemHistory();
            },
            async editSelectItem(item, index){
                this.formType = "edit_items";
                this.selectedItem = item;
                this.createItemFormData = {
                    ...item
                }
            },
            selectItemDetail(item, index){
                this.selectedItemDetail = item;
            },
            selectItemHistory(item, index){
                this.selectedItemHistory = item;
            },
            closeAddItemForm(){
                this.formType = "create";
                this.resetForms();
                this.selectedItem = {};
                this.itemDetails = [];
                this.itemDetailPaginations = [];
            },
            resetForms(){
                this.formType = "create";
                this.createItemFormData = {
                    name: "",
                    category: "",
                    reorder: 0,
                    item_type: "per_box",
                    user_id: this.user.id,
                };
                this.addItemFormData = {
                    name: "",
                    serial_number: "",
                    quantity: 1,
                    item_id: null,
                    remarks: "",
                    user_id: this.user.id,
                };
            },
            navigateItemPages(label){
                if(label == "Next &raquo;"){
                    label = this.itemFilterData.page + 1;
                }else if(label == "&laquo; Previous"){
                    label = this.itemFilterData.page - 1;
                }
                this.itemFilterData.page = label;
                this.getItem();
            },
            navigateItemDetailPages(label){
                if(label == "Next &raquo;"){
                    label = this.itemDetailFilterData.page + 1;
                }else if(label == "&laquo; Previous"){
                    label = this.itemDetailFilterData.page - 1;
                }
                this.itemDetailFilterData.page = label;
                this.getItemDetails();
            },
        }
    }
</script>