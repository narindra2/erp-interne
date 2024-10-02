<template>
    <div class="card card-flush shadow-sm mb-2">
        <div class="card-header">
            <h5 class="card-title"></h5>
			<div class="card-toolbar">
           
            <div class="d-flex flex-stack flex-wrap gap-4">
				<div v-if="term && !loading " class="fs-7 me-2">Total : {{ folders.length }} resultat trouvés</div>
                <div class="position-relative my-1">
						 <input type="text" autocomplete="off" v-model="term" class="w-300px form-control form-control-sm" placeholder="Nom ou réference dossier"/>
                </div>
				<button type="button" title="Recharger" @click="reloadData"  class="btn btn-sm btn-light">
                    <i class="fas fa-sync-alt fs-6"></i> Réactualiser
                </button>
            </div>
        </div>
        </div>
    </div>
    <div class="card card-flush shadow-sm">
        <div class="mx-5">
		<div class="table-responsive">
			<table class="table table-row-bordered gy-5">
				<thead>
					<tr class="fw-semibold fs-6 text-gray-800 border-bottom border-gray-200">
						<th>Reference</th>
						<th>Dossier</th>
						<th>Date de création</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<template v-for="(folder, index) in folders" :key="index">
						<tr class="shadow p-3  bg-white rounded" :id="index">
							<td>
								<div class="position-relative ps-6 pe-3 py-2">
									<div class="position-absolute start-0 top-0 w-4px h-100 rounded-2 bg-info"></div>
									<p >{{ folder.ref }}</p>
								</div>
							</td>
							<td>{{ folder.folder_name }}</td>
							<td>{{ folder.addedAt }}</td>
							<td><i class=" to-link fas fa-trash text-danger" @click="deleteFolder(index , folder.id)"></i></td>
                    	</tr>
					</template>
				</tbody>
			</table>
		</div>
		<div class="text-center" v-if="hasAnotherData">
			<button :disabled ="loading"  type="button" @click="loadMoreData()" class="btn btn-sm btn-light-secondary" :data-kt-indicator="indicatorLoading">
				<span class="indicator-label">
					Afficher plus + 
				</span>
				<span class="indicator-progress">
					Chargement ... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
				</span>
			</button>
		</div>
    </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
	props : [],
    data() {
        return {
            app : app,
			loading : false,
			indicatorLoading : "off",
			folders : [],
			skip : 0,
			term :"",
			hasAnotherData : true,
        }
    },
    mounted() {
		this.getFolderList();
    },
    methods: {
		async getFolderList(postData =  {},add = false){
			this.loading = true;
			this.indicatorLoading = "on";
			await  axios.post(this.app.baseUrl + "/suivi/folder/list", postData).then(response => {
                if (response.data.success) {
					this.skip = response.data.currentPage;
					this.hasAnotherData = response.data.hasAnotherData;
					if (add) {
						this.folders = this.folders.concat(response.data.result)
					}else{
                    	this.folders = response.data.result;
					}
                }
				
            })
			this.loading = false;
			this.indicatorLoading = "off";
		},
		loadMoreData(){
			this.getFolderList({"skip" :this.skip},true);
		},
		reloadData(){
			this.folders = [];
			this.getFolderList();
		},
	 	async deleteFolder(index  , folder_id = 0){
			console.log(index);
			if (index > -1) {
				this.loading = true;
				this.indicatorLoading = "on";
			 await axios.post(this.app.baseUrl + "/suivi/folder/delete", {"folder_id" : folder_id}).then(response => {
					if (response.data.success) {
						this.folders.splice(index, 1);
						toastr.success(response.data.message)
					}
				})
				this.loading = false;
				this.indicatorLoading = "off";
			}
		}
    },
	watch : {
		term(newVal , oldVal){
			this.getFolderList({"term" :newVal});
		}
	}
}
</script>