<template>
    <div class="container">
        <form action="">
            <notifications group="foo"
                    position="bottom left"
                    :max="3"
                    :width="400">
            </notifications>
        <div class="row">
            <div class="col-md-4">
                <h2>Requester Information</h2>
                <form id="createItemForm" @submit.prevent="submitForm">
                    <div class="form-group">
                            <label for="reorder_level">Items From</label>
                            <select class="form-control" v-model="requestFormData.warehouse_id" @change="changeWarehouse">
                                <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                            </select>
                        </div>
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" class="form-control" v-model="requestFormData.customer_name" placeholder="Enter Customer Name" required>
                        <div class="invalid-feedback">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <input type="text" class="form-control" v-model="requestFormData.remarks" placeholder="Enter Remarks" required>
                        <div class="invalid-feedback">

                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary mt-4">Submit</button>
                    <button type="button" class="btn btn-danger mt-4" @click.prevent="resetForms()">Reset Form</button>
                </form>
            </div>
            <div class="col-md-8">
                <h2>Request Items</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Item Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Pieces in Box</th>
                            <th scope="col">Qty</th>
                            <th scope="col">Total Qty</th>
                            <th scope="col">Remaining Stock</th>
                            <th scope="col" class="text-center "><button type="button" class="btn btn-primary" @click="addItem">Add</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in requestFormData.items" :key="index">
                            <td>
                                <span v-if="!item.userSelected">
                                    <vue-typeahead-bootstrap
                                        :data="items"
                                        v-model="item.query"
                                        :screen-reader-text-serializer="item => ` `"
                                        :serializer="item => item.name"
                                        @hit="selectItem($event, index)"
                                        placeholder="Search for Item Name"
                                    >
                                        <template slot="suggestion" slot-scope="{ data }">
                                            <div>
                                                <span>{{ data.category }}</span> - <span>{{ data.name }}</span><br>
                                            </div>
                                        </template>
                                    </vue-typeahead-bootstrap>
                                </span>
                                <span v-else>{{ item.name }}</span>
                            </td>
                            <td>
                                <span v-if="item.userSelected">
                                    {{ item.category }}
                                </span>
                            </td>
                            <td>
                                <span v-if="item.userSelected">
                                    <!-- <input type="number" v-model="item.quantity" placeholder="Enter Quantity"> -->
                                    <select v-model="item.per_piece">
                                        <option value="">Select</option>
                                        <option :value="per_pieces.per_pieces" v-for="(per_pieces, remainingIndex) in item.remaining" :key="remainingIndex">{{ per_pieces.per_pieces }}</option>
                                    </select>
                                </span>
                            </td>
                            <td>
                                <span v-if="item.userSelected">
                                    <span v-if="item.per_piece !== ''">
                                        <input type="number" name="test" v-model="item.quantity" placeholder="Qty" min="1" :max="perPieceData(item, item.per_piece).max_quantity">
                                    </span>
                                </span>
                            </td>
                            <td>
                                <span v-if="item.userSelected">
                                    <span :class="{'text-danger': quantityDangerClass(item, item.per_piece, item.quantity) }">
                                        {{ item.per_piece !== "" ? perPieceData(item, item.per_piece).per_pieces * item.quantity  : '0'}}
                                    </span>
                                </span>
                            </td>
                            <td>
                                <span v-if="item.userSelected">
                                    {{ item.per_piece !== "" ? perPieceData(item, item.per_piece).total_quantity : '0'}}
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger" @click="removeItem(item, index)">Remove</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
            </form>
    </div>
</template>

<script>
    import VueTypeaheadBootstrap from 'vue-typeahead-bootstrap';
    import isEmpty from 'lodash/isEmpty'
    import debounce from 'lodash/debounce'
    export default {
        mounted() {
            this.getItem();
        },
        props: ['warehouses','user'],
        data() {
            return {
                requestFormData: {
                    warehouse_id: 1,
                    items: []
                },
                items: [],
                selecteduser: null
            }
        },
        components: {
            VueTypeaheadBootstrap
        },
        methods: {
            async getItem(){
                let res = await axios.get('/api/all/items',{
                    params: this.itemFilterData
                });
                this.items = res.data;
            },
            alertError: debounce((notif) => {
                    notif({
                            group: 'foo',
                            title: `Form Error`,
                            text: `The quantity of items exceeds to its remaining quantity.`,
                            type: "error"
                        });
            },200),
            async createRequest(){
                var has_error = false;
                this.requestFormData.items.forEach(item => {
                    let index = item.remaining.findIndex(i => i.per_pieces == item.per_piece);
                    if(item.remaining[index].max_quantity < item.quantity){
                        this.alertError(this.$notify);
                        has_error = true;
                    }
                });
                if(has_error){
                    return false;
                }
                this.requestFormData.requester_id = this.user.id
                axios.post('/api/requests', this.requestFormData)
                .then(res => {
                    window.location = `/requests/${res.data.id}`;
                })
                .catch(err => {})
                .then(res => {})
                ;
            },
            resetForms(){
                this.requestFormData = {
                    warehouse_id: 1,
                    items: []
                }
            },

            changeWarehouse(){
                this.requestFormData = {
                    ...this.requestFormData,
                    items: []
                }
            },
            addItem(){
                this.requestFormData.items.push({
                    query: "",
                    userSelected: false,
                })
            },
            selectItem(item, index){
                axios.get(`api/items/${item.id}/request`, {
                    params: {
                        warehouse_id: this.requestFormData.warehouse_id
                    }
                })
                .then(res => {
                    let remaining = res.data.remaining;
                    this.$set(this.requestFormData.items, index, {
                        userSelected: true,
                        quantity: 1,
                        item_id: item.id,
                        name: item.name,
                        category: item.category,
                        remaining: remaining,
                        per_piece: "",
                        total_quantity_1: item.total_quantity_1,
                        total_quantity_2: item.total_quantity_2,
                    });
                })
                .catch(err => {})
                .then(res => {})
            },
            removeItem(item, index){
                this.requestFormData.items.splice(index, 1);
                // this.requestFormData.items = this.requestFormData.items.filter(i => i.id != item.id);
            },
            submitForm(){
                this.createRequest();
            },
            perPieceData(item, per_pieces){
                let index = item.remaining.findIndex(i => i.per_pieces == per_pieces);
                return item.remaining[index];
            },
            quantityDangerClass(item, per_pieces, quantity){
                let filtereditem = this.perPieceData(item, per_pieces);
                if(isEmpty(filtereditem)){
                    return false;
                }
                return ((per_pieces * quantity) > filtereditem.total_quantity);
            }
        }
    }
</script>