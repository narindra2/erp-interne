import { createApp } from 'vue';
import recapSuiviDossier from './components/recapSuiviDossier.vue'
import folderList from './components/folderList.vue'
import productivitie from './components/productivitie.vue'
import userParams from './components/userParams.vue'
import dayoffGantt from './components/dayoffGantt.vue'
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import ganttastic from '@infectoone/vue-ganttastic'
const app = createApp({});
app
    // .use(require('vue-moment'))
    .use(ganttastic)
    .component('recap-suivi-dossier', recapSuiviDossier)
    .component('folder-list', folderList)
    .component('productivitie', productivitie)
    .component('user-params', userParams)
    .component('dayoff-gantt-vue', dayoffGantt)
    .component('EasyDataTable', Vue3EasyDataTable)
    .mount('#app-vue-partial');