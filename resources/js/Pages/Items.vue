<template>
    <div class="container">
        <div class="row">
            <notifications group="foo"
                    position="bottom left"
                    :max="3"
                    :width="400">
            </notifications>
            <div class="col-md-4" v-if="user.account_type != 'user'">
                <div v-if="formType == 'create' || formType == 'edit_items'">
                    <h2 v-if="formType == 'create'">Create Item</h2>
                    <h2 v-else>Update Item</h2>
                    <form id="createItemForm" @submit.prevent="submitForm">
                        <div class="form-group">
                            <label for="category">Category</label>

                            <vue-typeahead-bootstrap
                                :data="categories"
                                v-model="createItemFormData.category"
                                :screen-reader-text-serializer="item => ` `"
                                :serializer="item => item.category"
                                placeholder="Enter Category"
                                :inputClass="(typeof createItemFormError.category != 'undefined' && createItemFormError.category.length != 0) ? 'form-control is-invalid' :''"
                            >
                                <template slot="suggestion" slot-scope="{ data }">
                                    <div>
                                        <span>{{ data.category }}</span>
                                    </div>
                                </template>
                            </vue-typeahead-bootstrap>
                            <!-- <input type="text" class="form-control" v-model="createItemFormData.category" placeholder="Enter Category" required :class="{'is-invalid': (typeof createItemFormError.category != 'undefined' && createItemFormError.category.length != 0)}"> -->
                            <div style="color:red">
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
                            <label for="reorder_level">Receive Type</label>
                            <select class="form-control" v-model="addItemFormData.receive_type" required>
                                <option value="">Select Receive Type</option>
                                <option value="in">NEW ITEM</option>
                                <option value="rewarehouse">REWAREHOUSE</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="reorder_level">To</label>
                            <select class="form-control" v-model="addItemFormData.warehouse_id" :disabled="user.account_type != 'admin'" required>
                                <option value="">Select Warehouse</option>
                                <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                            </select>
                        </div>

                        <div class="form-group" v-if="addItemFormData.receive_type == 'in'">
                            <label for="serial_number">Quantity {{ selectedItem.item_type_string }}</label>
                            <input type="number" min="1" class="form-control" v-model="addItemFormData.quantity" placeholder="Enter Quantity" required :disabled="selectedItem.item_type == 'per_piece'">
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <input type="text" class="form-control" v-model="addItemFormData.remarks" placeholder="Enter Remarks">
                            <div class="invalid-feedback">
                                Please choose a username.
                            </div>
                        </div>
                        <div class="form-group" v-if="addItemFormData.receive_type == 'in'">
                            <label for="stock_month">Stock Month</label>
                            <input type="month" class="form-control" v-model="addItemFormData.stock_month" placeholder="Enter Stock Month" required>
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
                <h2  class="mb-6">Items List</h2>
                    <div class="row">
                        <div class="col-md-4">
                            Search:
                            <input class="form-control form-control-sm" v-model="itemFilterData.search" type="text" placeholder="Type here...">
                        </div>
                        <div class="col-md-2">
                            View Items:
                            <select class="form-control form-control-sm" v-model="itemFilterData.view_status">
                                <option value="default">All</option>
                                <option value="archived">Archived</option>
                                <option value="reorder">Reorder Level</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            &nbsp;<br>
                            <button class="btn btn-primary  btn-sm" @click="getItem">Search</button>
                        </div>
                    </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Type</th>
                            <th scope="col">Category</th>
                            <th scope="col">Item Name</th>
                            <th scope="col">Reorder Level</th>
                            <th scope="col">Main Warehouse Qty</th>
                            <th scope="col">JBtech Warehouse Qty</th>
                            <th scope="col">Total Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in items" :key="item.id" :class="{'table-secondary': (selectedItem.id == item.id)}">
                            <td>{{ item.id }}</td>
                            <td>{{ item.item_type_string }}</td>
                            <td>{{ item.category }}</td>
                            <td>{{ item.name }}</td>
                            <td>{{ item.reorder_level }}</td>
                            <td class="text-center">{{ item.total_quantity_1 }}</td>
                            <td class="text-center">{{ item.total_quantity_2 }}</td>
                            <td class="text-center">{{ item.total_quantity }}</td>
                            <td class="text-center" v-if="item.is_archived === 0">
                                <span class="custom-pointer" @click="selectItem(item, index)" title="Add/View Stock">
                                    <i class="bi bi-plus-square" v-if="user.account_type != 'user'"></i>
                                    <i class="bi bi-eye" v-else></i>
                                </span>
                            </td>
                            <td class="text-center" v-if="user.account_type != 'user' && item.is_archived === 0">
                                <span class="custom-pointer" @click="editSelectItem(item, index)" title="Edit Item Details"><i class="bi bi-pencil-square"></i></span>
                            </td>
                            <td class="text-center" v-if="user.account_type != 'user' && item.is_archived === 0">
                                <span class="custom-pointer" v-if="item.allow_delete == 1" @click="deleteSelectedItem(item)" title="Delete Item"><i class="bi bi-trash"></i></span>
                            </td>
                            <td class="text-center" v-if="user.account_type != 'user' && item.is_archived === 0">
                                <span class="custom-pointer" title="Generate Barcode" @click="generateBarcode(item)" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="bi bi-upc"></i></span>
                            </td>
                            <td class="text-center" v-if="user.account_type != 'user'">
                                <span class="custom-pointer" v-if="item.total_quantity_1 == 0 && item.total_quantity_2 == 0 && item.allow_delete == 0 && item.is_archived === 0" title="Archive" @click="archiveItem(item, 1)"><i class="bi bi-file-earmark-zip"></i></span>
                                <span class="custom-pointer" v-if="item.is_archived === 1" title="Unarchive" @click="archiveItem(item, 0)"><i class="bi bi-arrow-counterclockwise"></i></span>
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
                <div class="row">
                    <div class="col-md-2">
                        Search:
                        <input class="form-control form-control-sm" v-model="itemDetailFilterData.search" type="text" placeholder="Type here...">
                    </div>
                    <div class="col-md-2">
                        Warehouse:
                        <select class="form-control form-control-sm" v-model="itemDetailFilterData.warehouse_id">
                            <option value="">All Warehouse</option>
                            <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        Stock Month:
                        <select class="form-control form-control-sm" v-model="itemDetailFilterData.stock_month">
                            <option value="">All Months</option>
                            <option :value="stock_month.stock_month" v-for="(stock_month, index) in stockMonths" :key="index">{{ stock_month.stock_month }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        &nbsp;<br>
                        <button class="btn btn-primary  btn-sm" @click="getItemDetails">Search</button>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Item Name</th>
                            <th scope="col">Serial Number</th>
                            <th scope="col">Stock Month</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Warehouse</th>
                            <th scope="col">Added</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in itemDetails" :key="item.id" :class="{'table-secondary': (selectedItemDetail.id == item.id)}">
                            <td>
                                <span :title="item.item.category">{{ item.item.name }}</span>
                            </td>
                            <td>{{ item.serial_number }}</td>
                            <td>{{ item.stock_month }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>{{ item.remarks }}</td>
                            <td>{{ item.warehouse.name }}</td>
                            <td>{{ item.created_at }}</td>
                            <td sty>
                                <span class="custom-pointer" v-if="user.account_type == 'admin'" @click="deleteSerialNumber(item)" title="Delete Item" data-bs-toggle="modal" data-bs-target="#deleteModal"><i class="bi bi-trash"></i></span>
                            </td>
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
        <div class="row" v-if="formType !='edit_items' && user.account_type !='user'">
            <div class="col-md-12">
                <h2>{{ selectedItem.category }} {{ selectedItem.name }} Item History</h2>

                <div class="row">
                    <div class="col-md-2">
                        Search:
                        <input class="form-control form-control-sm" v-model="itemHistoryFilterData.search" type="text" placeholder="Type here...">
                    </div>
                    <div class="col-md-2">
                        Warehouse:
                        <select class="form-control form-control-sm" v-model="itemHistoryFilterData.warehouse_id">
                            <option value="">All Warehouse</option>
                            <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        History Type:
                        <select class="form-control form-control-sm" v-model="itemHistoryFilterData.history_type">
                            <option value="">All History Type</option>
                            <option value="in">IN</option>
                            <option value="out">OUT</option>
                            <option value="rewarehouse">REWAREHOUSE</option>
                            <option value="deleted">DELETED</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        &nbsp;<br>
                        <button class="btn btn-primary  btn-sm" @click="getItemHistory">Search</button>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">In/Out</th>
                            <th scope="col">Item Name</th>
                            <th scope="col">Serial Number</th>
                            <th scope="col">Stock Month</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Warehouse</th>
                            <th scope="col">Added</th>
                            <th scope="col">Previous Qty</th>
                            <th scope="col">Remaining Qty</th>
                            <th scope="col">Request Number</th>
                            <th scope="col">User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in itemHistories" :key="index" :class="{'table-secondary': (selectedItemHistory.id == item.id)}">
                            <td>{{ item.history_type }}</td>
                            <td>
                                <span :title="item.item.category">{{ item.item.name }}</span>
                            </td>
                            <td>
                                 <span :title="item.item_detail.serial_number">{{ item.item_detail.serial_number }}</span>
                            </td>
                            <td>
                                 <span :title="item.item_detail.stock_month">{{ item.item_detail.stock_month }}</span>
                            </td>
                            <td>{{ item.quantity }}</td>
                            <td>
                                <span v-if="item.history_type =='out'">{{ item.request_item.remarks }}</span>
                                <span v-else-if="item.history_type =='deleted'">{{ item.deleted_remarks }}</span>
                                <span v-else>{{ item.item_detail.remarks }}</span>
                            </td>
                            <td>{{ item.warehouse.name }}</td>
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

        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete {{ selectedSerial.serial_number }} Serial Number?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="deleteSerialForm" @submit.prevent="deleteSerialPhp">
                        <div class="form-group">
                            <label for="to">Reason</label>
                            <textarea class="form-control" v-model="selectedSerial.deleted_remarks" placeholder="Add Reason" required>
                            </textarea>
                        </div>
                    </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="closeDeleteModal" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="deleteSerialForm" class="btn btn-danger">Confirm Delete</button>
            </div>
            </div>
        </div>
        </div>

        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Barcode Generator</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="generateBarcode" @submit.prevent="generateBarcodePhp">
                        <span><b>Barcode Definition:</b></span><br>
                        <span>Item ID: <b>{{ barcodeForm.item_id  }} ({{ barcodeForm.item_id }})</b></span><br>
                        <span>Category: <b>{{ barcodeForm.category }} ({{ barcodeForm.ca }})</b></span><br>
                        <span>Current Year: <b>{{ barcodeForm.year }} ({{ barcodeForm.ye }})</b></span><br>
                        <div class="form-group">
                            <label for="prefix">Barcode Prefix</label>
                            <input readonly type="text" class="form-control" aria-describedby="emailHelp" v-model="barcodeForm.prefix" placeholder="Enter Barcode Prefix" required>
                        </div>
                        <!-- 
                        <div class="form-group">
                            <label for="padding">Leading Zeros</label>
                            <input type="number" min="1" class="form-control" aria-describedby="emailHelp" v-model="barcodeForm.padding" placeholder="Enter Leading Zeros" required>
                        </div> -->
                        <div class="form-group">
                            <label for="from">Starting Number</label>
                            <input type="number" min="1" class="form-control" aria-describedby="emailHelp" v-model="barcodeForm.from" placeholder="Enter Starting Number" required>
                        </div>

                        <div class="form-group">
                            <label for="to">Ending Number</label>
                            <input type="number" min="1" class="form-control" aria-describedby="emailHelp" v-model="barcodeForm.to" placeholder="Enter Ending Number" required>
                        </div>

                        <div class="form-group">
                            <label for="to">Sample Barcode</label>
                            <input type="text" readonly class="form-control" aria-describedby="emailHelp" v-model="sampleBarcode" placeholder="Enter Ending Number" required>
                        </div>
                    </form>

                    <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Prefix</th>
                            <th scope="col">Starting</th>
                            <th scope="col">Ending</th>
                            <th scope="col">Generated On</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(barcode, index) in barcodes" :key="barcode.id">
                            <td>{{ barcode.barcode_prefix }}</td>
                            <td>{{ barcode.start_from }}</td>
                            <td>{{ barcode.end_to }}</td>
                            <td>{{ barcode.created_at }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm" @click="showGeneratedBarcodes({
                                    prefix: barcode.barcode_prefix,
                                    from: barcode.start_from,
                                    to: barcode.end_to,
                                    padding: barcode.padding,
                                })">
                                    Print
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" form="generateBarcode" class="btn btn-primary">Generate</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import VueTypeaheadBootstrap from 'vue-typeahead-bootstrap';
    import isEmpty from 'lodash/isEmpty';
    import cloneDeep from 'lodash/cloneDeep';
    export default {
        async mounted() {
            // console.log('Component mounted.')
            await this.getItem();
            await this.getCategories();
            await this.getItemDetails();
            await this.getItemHistory();
            await this.getStockMonths();
        },
        components: {
            VueTypeaheadBootstrap
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
                    quantity: 0,
                    item_id: null,
                    remarks: "",
                    user_id: this.user.id,
                    receive_type: "",
                },
                addItemDetailFormErrors:{},
                selectedSerial:{},
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
                categories: [],
                stockMonths: [],
                itemFilterData: {
                    page: 1,
                    warehouse_id: this.user.account_type == 'warehouse_admin' ? this.user.warehouse_id : "",
                    search: "",
                    view_status: "default",
                },
                itemDetailFilterData: {
                    page: 1,
                    warehouse_id: this.user.account_type == 'warehouse_admin' ? this.user.warehouse_id : "",
                    search: "",
                    stock_month: ""
                },
                itemHistoryFilterData: {
                    page: 1,
                    warehouse_id: this.user.account_type == 'warehouse_admin' ? this.user.warehouse_id : "",
                    search: "",
                    history_type: "",
                },
                barcodeForm: {
                    prefix: "",
                    padding: 3,
                    from: 1,
                    to: 100,
                },
                barcodes: []
            }
        },
        methods: {
            async getCategories(){
                let res = await axios.get('/api/categories');
                this.categories = res.data;
            },
            async getItem(){
                let res = await axios.get('/api/items',{
                    params: this.itemFilterData
                });
                this.items = res.data.data;
                this.itemPaginations = res.data.links;
            },
            async getItemDetails(){
                let id = isEmpty(this.selectedItem) ? "all" : this.selectedItem.id;
                let res = await axios.get(`/api/items/${id}/details`,{
                    params: this.itemDetailFilterData
                });
                this.itemDetails = res.data.data;
                this.itemDetailPaginations = res.data.links;
            },
            async getItemHistory(){
                let id = isEmpty(this.selectedItem) ? "all" : this.selectedItem.id;
                let res = await axios.get(`/api/items/${id}/history`,{
                    params: this.itemHistoryFilterData
                });
                this.itemHistories = res.data.data;
                this.itemHistoryPaginations = res.data.links;
            },
            async getStockMonths(){
                let id = isEmpty(this.selectedItem) ? "all" : this.selectedItem.id;
                let res = await axios.get(`/api/items/${id}/stock-months`);
                this.stockMonths = res.data;
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
                        text: `${this.createItemFormData.name} as been added`,
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
                        text: `${this.createItemFormData.name} as been updated`,
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
                    this.$notify({
                        group: 'foo',
                        title: `${this.addItemFormData.name}`,
                        text: `${this.addItemFormData.serial_number} as been added`,
                        type: "success"
                    });
                    this.addItemFormData.serial_number = "";
                    await this.getItem();
                    await this.getItemDetails();
                    await this.getItemHistory();
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
                    quantity: 0,
                    warehouse_id: this.user.account_type == 'admin' ? "" : this.user.warehouse_id,
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
            async deleteSelectedItem(item, index){
                axios.delete(`/api/items/${item.id}`)
                .then(res => {
                    this.getItem();
                    this.getCategories();
                    this.resetForms();
                    this.selectedItem = {};
                })
                .catch(err => {})
            },
            selectItemDetail(item, index){
                this.selectedItemDetail = item;
            },
            selectItemHistory(item, index){
                this.selectedItemHistory = item;
            },
            async closeAddItemForm(){
                this.formType = "create";
                this.resetForms();
                this.selectedItem = {};
                this.itemDetails = [];
                this.itemDetailPaginations = [];
                await this.getItemDetails();
                await this.getItemHistory();
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
                    quantity: 0,
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
            navigateItemHistoryPages(label){
                if(label == "Next &raquo;"){
                    label = this.itemHistoryFilterData.page + 1;
                }else if(label == "&laquo; Previous"){
                    label = this.itemHistoryFilterData.page - 1;
                }
                this.itemHistoryFilterData.page = label;
                this.getItemHistory();
            },

            getGeneratedBarcodes(){
                axios.get(`/api/items/${this.barcodeForm.item_id}/barcodes`)
                .then(async res => {
                    this.barcodes = res.data;
                    if(!isEmpty(this.barcodes)){
                        this.barcodeForm = {
                            ...this.barcodeForm,
                            from: parseInt(this.barcodes[0].end_to) + 1,
                            to: parseInt(this.barcodes[0].end_to) + 100,
                        }
                    }
                })
                .catch(err => { })
                .then(res => {})
                ;
            },
            generateBarcode(item){
                const event = new Date();
                let year = event.getFullYear();
                let ye = event.getFullYear().toString().substr(2);
                let ca = item.category
                    .split(' ')
                    .map(word => word[0].toUpperCase())
                    .join('');
                this.barcodeForm = {
                    ...item,
                    item_id: item.id,
                    prefix: `${item.id}${ca}${ye}`,
                    padding: 4,
                    from: 1,
                    to: 100,
                    year,
                    ye,
                    ca
                }
                this.getGeneratedBarcodes();
            },
            generateBarcodePhp(){
                axios.post(`/api/items/${this.barcodeForm.item_id}/barcodes`, this.barcodeForm)
                .then(async res => {
                    this.getGeneratedBarcodes();
                    this.showGeneratedBarcodes(this.barcodeForm);
                })
                .catch(err => { })
                .then(res => {})
                ;
            },
            showGeneratedBarcodes(barcodeForm){
                window.open(`/generate/barcode?prefix=${barcodeForm.prefix}&from=${barcodeForm.from}&to=${barcodeForm.to}&padding=${barcodeForm.padding}`,
                        'newwindow',
                        'width=960,height=1080');
                    return false;
            },
            async archiveItem(item, value){
                item = cloneDeep(item);
                item.is_archived = value;
                axios.put(`/api/items/${item.id}`, item)
                .then(async res => {
                    this.$notify({
                        group: 'foo',
                        title: `Success`,
                        text: `${item.name} as been archived`,
                        type: "success"
                    });
                    await this.getItem();
                    await this.getCategories();
                    await this.getItemDetails();
                    await this.getItemHistory();
                })
                .catch(err => { })
                .then(res => {})
                ;
            },
            async deleteSerialNumber(item){
                this.selectedSerial = cloneDeep(item);
            },
            async deleteSerialPhp(){
                document.getElementById('closeDeleteModal').click();
                this.selectedSerial.user_id = this.user.id;
                axios.put(`/api/items/${this.selectedSerial.item_id}/details/${this.selectedSerial.id}`, this.selectedSerial)
                .then(async res => {
                    this.$notify({
                        group: 'foo',
                        title: `Success`,
                        text: `${item.serial_number} as been deleted`,
                        type: "success"
                    });
                })
                .catch(err => { })
                .then(res => {})
                ;
                await this.getItem();
                await this.getCategories();
                await this.getItemDetails();
                await this.getItemHistory();
            }
        },
        computed: {
            sampleBarcode(){
                if(this.barcodeForm.prefix == ""){
                    return "";
                }else{
                    return `${this.barcodeForm.prefix}`+ `${this.barcodeForm.from}`.padStart(this.barcodeForm.padding, "0");
                }
            }
        }
    }
</script>