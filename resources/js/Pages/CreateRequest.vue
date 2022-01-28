<template>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h2>Requester Information</h2>
                <form id="createItemForm" @submit.prevent="submitForm">
                    <div class="form-group">
                            <label for="reorder_level">Items From</label>
                            <select class="form-control" v-model="requestFormData.warehouse_id">
                                <option :value="warehouse.id" v-for="warehouse in warehouses" :key="warehouse.id">{{ warehouse.name }}</option>
                            </select>
                        </div>
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <input type="text" class="form-control" v-model="requestFormData.customer_name" placeholder="Enter Customer Name">
                        <div class="invalid-feedback">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <input type="text" class="form-control" v-model="requestFormData.remarks" placeholder="Enter Remarks">
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
                            <th scope="col">Category</th>
                            <th scope="col">Item Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Remaining Stock</th>
                            <th scope="col" class="text-center "><button type="button" class="btn btn-primary" @click="addItem">Add</button></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in requestFormData.items" :key="index">
                            <td>{{ item.category }}</td>
                            <td></td>
                            <td>{{ item.reorder_level }}</td>
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
            this.getItem();
        },
        props: ['warehouses'],
        data() {
            return {
                requestFormData: {
                    warehouse_id: 1,
                    items: []
                },
                items: [],
            }
        },
        methods: {
            async getItem(){
                let res = await axios.get('/api/all/items',{
                    params: this.itemFilterData
                });
                this.items = res.data.data;
            },
            resetForms(){

            },
            addItem(){
                this.requestFormData.items.push({
                    category: "",
                    quantity: "",
                    remaining: "",
                })
            }
        }
    }
</script>