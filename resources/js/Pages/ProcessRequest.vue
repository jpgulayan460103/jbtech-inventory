<template>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <p>Request number: <b>{{clonedCreatedRequest.request_number}}</b></p>
                <p>Customer Name: <b>{{clonedCreatedRequest.customer_name}}</b></p>
                <p>Requested Warehouse: <b>{{clonedCreatedRequest.warehouse ? clonedCreatedRequest.warehouse.name : ""}}</b></p>
                <p>Remarks: <b>{{clonedCreatedRequest.remarks}}</b></p>
                <p>Requested On: <b>{{clonedCreatedRequest.created_at}}</b></p>
                <p>Scan Item: <input type="text" v-model="scanned" placeholder="Scan here" class="form-control" style="width:250px" @keypress.enter="scanItem"></p>
            </div>
            <div class="col-md-8">
                <h3>REQUESTED ITEMS</h3>
                <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Item Name</th>
                        <th scope="col">Category</th>
                        <th scope="col">Per Piece</th>
                        <th scope="col">No. of Box Requested</th>
                        <th scope="col">Requested Qty</th>
                        <th scope="col">Fulfilled Qty</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in clonedCreatedRequest.items" :key="index">
                        <td>{{ item.item ? item.item.name : "" }}</td>
                        <td>{{ item.item ? item.item.category : "" }}</td>
                        <td>{{ item.item ? item.per_piece : "" }}</td>
                        <td>{{ item.item ? item.quantity : "" }}</td>
                        <td>{{ item.requested_quantity }}</td>
                        <td>{{ getScannedItemQuantity(item.item_id, item.per_piece) }}</td>
                    </tr>
                </tbody>
            </table>
            <button class="btn btn-primary" @click="processRequest()">Save</button>
            <button class="btn btn-danger">Cancel</button>
            </div>
        </div>
        <h3>SCANNED ITEMS</h3>
        <hr>
        <div class="row">
            <div v-for="(item, index) in clonedCreatedRequest.items" :key="index" class="col-md-4">
                <!-- <h4 class="text-success">{{ item.item.category }} - {{ item.item.name }}</h4> -->
                <h4>{{ item.item.category }} - {{ item.item.name }}</h4>
                <p>
                    Per Piece: <b>{{ item.per_piece }}</b><br>
                    Requested Qty: <b>{{ item.requested_quantity }}</b><br>
                    Fulfilled Qty: <b>{{ getScannedItemQuantity(item.item_id, item.per_piece) }}</b>
                </p>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Serial Number</th>
                            <th scope="col">Quantity</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(scannedItem, scannedItemindex) in getScannedItem(item.item_id, item.per_piece)" :key="scannedItemindex">
                            <td>
                                <span>{{ scannedItem.serial_number }}</span><br>
                                <span>remarks:{{ scannedItem.remarks }}</span><br>
                            </td>
                            <td>{{ scannedItem.quantity }}</td>
                            <td>
                                <a href="javascript:void(0)" @click="removeItem(scannedItem.id)">Remove</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <br>
            </div>
        </div>
    </div>
</template>

<style lang="scss" scoped>

</style>

<script>
    import _cloneDeep from 'lodash/cloneDeep'
    import _isEmpty from 'lodash/isEmpty'
    export default {
        mounted() {
            this.clonedCreatedRequest = _cloneDeep(this.createdRequest);
        },
        props: ['createdRequest','user'],
        data() {
            return {
                clonedCreatedRequest: {
                    items: []
                },
                scanned: "",
                scannedItems: [],
            }
        },
        methods: {
            scanItem(){
                axios.get('/api/requests/items/scan', {
                    params: {
                        scanned: this.scanned,
                        warehouse_id: this.createdRequest.warehouse_id,
                    }
                })
                .then(res => {
                    let item_detail = res.data;
                    let existed = this.scannedItems.filter(i => i.serial_number == item_detail.serial_number);
                    if(_isEmpty(existed)){
                        let on_items = this.clonedCreatedRequest.items.filter(i => i.item_id == item_detail.item_id && item_detail.quantity == i.per_piece);
                        if(!_isEmpty(on_items)){
                            let scanQuantity = this.getScannedItemQuantity(item_detail.item_id, on_items[0].per_piece);
                            let limitQuantity = on_items[0].requested_quantity;
                            let newQuantity = scanQuantity + item_detail.quantity;
                            console.log(item_detail.quantity, on_items[0].per_piece);
                            if(newQuantity <= limitQuantity){
                                if(item_detail.quantity == on_items[0].per_piece){
                                    this.scannedItems.push(item_detail);
                                }else{
                                    console.log('lahi ug per piece');
                                }
                            }else{    
                            }
                        }else{
                            console.log('wala sa item list');
                        }
                    }else{
                        console.log('naa na ang serial');
                    }
                    this.scanned = "";
                })
                .catch(err => {})
                .then(res => {})
                ;
            },
            getScannedItem(item_id, per_piece){
                return this.scannedItems.filter(i => i.item_id == item_id && per_piece == i.quantity);
            },
            getScannedItemQuantity(item_id, per_piece){
                let items = this.scannedItems.filter(i => i.item_id == item_id && per_piece == i.quantity);
                return items.reduce((sum, t) => {
                    return sum += t.quantity;
                }, 0);
            },
            removeItem(item_detail_id){
                this.scannedItems = this.scannedItems.filter(i => i.id != item_detail_id);
            },
            processRequest(){
                let has_unfulfilled = false;
                var unfulfilled = {};
                for (let index = 0; index < this.clonedCreatedRequest.items.length; index++) {
                    let item = this.clonedCreatedRequest.items[index];
                    let scannedQuantity = this.getScannedItemQuantity(item.item_id, item.per_piece);
                    if(scannedQuantity != item.requested_quantity){
                        has_unfulfilled = true;
                        unfulfilled = item;
                        break;
                    }
                }
                if(has_unfulfilled){
                    if(confirm(`The quantity of ${unfulfilled.item.name} is not enough to fulfill the requested quantity. Are you sure you want to continue?`)){
                        let formData = {
                            user_id: this.user.id,
                            warehouse_id: this.clonedCreatedRequest.warehouse_id,
                            items: this.scannedItems
                        };
                        axios.post(`/api/request/${this.clonedCreatedRequest.id}/process`,formData)
                        .then(res => {
                            window.location = `/requests/${this.clonedCreatedRequest.id}`;
                        })
                        .catch(res => {})
                        .then(res => {})
                    };
                }else{
                    let formData = {
                            user_id: this.user.id,
                            warehouse_id: this.clonedCreatedRequest.warehouse_id,
                            items: this.scannedItems
                        };
                        axios.post(`/api/request/${this.clonedCreatedRequest.id}/process`,formData)
                        .then(res => {
                            window.location = `/requests/${this.clonedCreatedRequest.id}`;
                        })
                        .catch(res => {})
                        .then(res => {})
                }
            }
        },
        computed: {
            
        },
    }
</script>