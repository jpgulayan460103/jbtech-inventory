import Vue from 'vue'

import ItemIndex from './Pages/Items'
import RequestIndex from './Pages/Requests'
import CreateRequest from './Pages/CreateRequest'
import CreatedRequest from './Pages/CreatedRequest'
import ProcessRequest from './Pages/ProcessRequest'
import Users from './Pages/Users'
import Reports from './Pages/Reports'

Vue.component('item-index', ItemIndex);
Vue.component('requests-index', RequestIndex);
Vue.component('requests-create', CreateRequest);
Vue.component('requests-created', CreatedRequest);
Vue.component('process-request', ProcessRequest);
Vue.component('users', Users);
Vue.component('reports-index', Reports);
