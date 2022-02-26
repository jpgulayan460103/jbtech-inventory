<template>
    <div class="container">
        <h2>Request List</h2>
        <div class="row" v-if="user.account_type != 'user'">
            <div class="col-md-2" v-if="user.account_type == 'warehouse_admin'">
                &nbsp;<br>
                <select class="form-control form-control-sm" v-model="requestFilterData.show">
                    <option value="requested">User Requested</option>
                    <option value="created">Created Request</option>
                </select>
            </div>
            <div class="col-md-2">
                &nbsp;<br>
                <select class="form-control form-control-sm" v-model="requestFilterData.status">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="processed">Processed</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                Search:
                <input class="form-control form-control-sm" v-model="requestFilterData.search" type="text" placeholder="Type here...">
            </div>
            <div class="col-md-2">
                &nbsp;<br>
                <button class="btn btn-primary  btn-sm" @click="getRequest">Search</button>
            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Warehouse</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Remarks</th>
                    <th scope="col">Requested By</th>
                    <th scope="col">Requested On</th>
                    <th scope="col">Status</th>
                    <th scope="col">Processed By</th>
                    <th scope="col">Type</th>
                    <th scope="col" class="text-center"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(request, index) in requests" :key="request.id" :class="{'table-secondary': (selectedItem.id == request.id)}">
                    <td>{{ request.request_number }}</td>
                    <td>{{ request.warehouse.name }}</td>
                    <td>{{ request.customer_name }}</td>
                    <td>{{ request.remarks }}</td>
                    <td>{{ request.requester.name }}</td>
                    <td>{{ request.created_at }}</td>
                    <td>{{ request.status }}</td>
                    <td>{{ request.processor ? request.processor.name : "" }}</td>
                    <td>{{ request.request_type }}</td>
                    <td class="text-center"><a :href="`/requests/${request.id}`" type="button" class="btn btn-primary">View</a></td>
                </tr>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item" :class="{ active: pagination.active }" v-for="(pagination, index) in requestPaginations" :key="index" @click="navigatePages(pagination.label)">
                    <a class="page-link" href="javascript:void(0);" v-if="pagination.url != null">
                        <span v-html="pagination.label"></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.getRequest();
        },
        props: ['warehouses','user'],
        data() {
            return {
                requests: [],
                requestPaginations: {},
                requestFilterData: {
                    page: 1,
                    user_id: this.user.id,
                    show: "requested",
                    status: "",
                },
                selectedItem: {
                    id: null
                }
            }
        },
        methods: {
            async getRequest(){
                let res = await axios.get('/api/requests',{
                    params: this.requestFilterData
                });
                this.requests = res.data.data;
                this.requestPaginations = res.data.links;
            },

            navigatePages(label){
                if(label == "Next &raquo;"){
                    label = this.requestFilterData.page + 1;
                }else if(label == "&laquo; Previous"){
                    label = this.requestFilterData.page - 1;
                }
                this.requestFilterData.page = label;
                this.getRequest();
            },
        }
    }
</script>