<template>
    <app-layout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Tasks
            </h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4 mb-4">
                    Chart<br/>
                    <line-chart v-if="loaded" :chart-data="chartdata" :options="options"></line-chart>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-4 py-4">
                    <!-- <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert" v-if="$page.flash.message">
                      <div class="flex">
                        <div>
                          <p class="text-sm">{{ $page.flash.message }}</p>
                        </div>
                      </div>
                    </div> -->
                    <button @click="openModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded my-3"><span class="icon-plus"></span> New task</button>
                    <table class="table-fixed w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 w-20">No.</th>
                                <th class="px-4 py-2">Title</th>
                                <th class="px-4 py-2">Description</th>
                                <th class="px-4 py-2 w-40">Status</th>
                                <th class="px-4 py-2 ">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="row in data" :key="row.id">
                                <td class="border px-4 py-2">{{ row.id }}</td>
                                <td class="border px-4 py-2">{{ row.title }}</td>
                                <td class="border px-4 py-2">{{ row.description }}</td>
                                <td class="border px-4 py-2">
                                    <button @click="updateStatus(row)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" v-if="row.status === 1">
                                        <span class="icon-checkbox-checked" ></span>                                        
                                    </button>
                                    <button @click="updateStatus(row)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" v-else>
                                        <span class="icon-checkbox-unchecked"></span>
                                    </button>
                                </td>
                                <td class="border px-4 py-2"> 
                                    <button @click="edit(row)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"><span class="icon-pencil"></span></button>
                                    <button @click="deleteRow(row)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"><span class="icon-bin"></span></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="fixed z-10 inset-0 overflow-y-auto ease-out duration-400" v-if="isOpen">
                      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        
                        <div class="fixed inset-0 transition-opacity">
                          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                        </div>
                        <!-- This element is to trick the browser into centering the modal contents. -->
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>​
                        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                            <form>
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="">
                                        <div class="mb-4">
                                            <label for="task_title" class="block text-gray-700 text-sm font-bold mb-2">Title:</label>
                                            <input type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="task_title" placeholder="Task Title" v-model="form.title">
                                            <!-- <div v-if="$page.errors.title" class="text-red-500">{{ $page.errors.title[0] }}</div> -->
                                        </div>
                                        <div class="mb-4">
                                            <label for="task_description" class="block text-gray-700 text-sm font-bold mb-2">Description:</label>
                                            <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="task_description" v-model="form.description" placeholder="Task Description"></textarea>
                                            <!-- <div v-if="$page.errors.description" class="text-red-500">{{ $page.errors.description[0] }}</div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                    <button wire:click.prevent="store()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5" v-show="!editMode" @click="save(form)">
                                        Save
                                    </button>
                                    </span>
                                    <span class="flex w-full rounded-md shadow-sm sm:ml-3 sm:w-auto">
                                    <button wire:click.prevent="store()" type="button" class="inline-flex justify-center w-full rounded-md border border-transparent px-4 py-2 bg-green-600 text-base leading-6 font-medium text-white shadow-sm hover:bg-green-500 focus:outline-none focus:border-green-700 focus:shadow-outline-green transition ease-in-out duration-150 sm:text-sm sm:leading-5" v-show="editMode" @click="update(form)">
                                        Update
                                    </button>
                                    </span>
                                    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
                                    
                                    <button @click="closeModal()" type="button" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
                                        Cancel
                                    </button>
                                    </span>
                                </div>
                            </form>                          
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>
<script>
    import AppLayout from './../Layouts/AppLayout'
    import Welcome from './../Jetstream/Welcome'
    import LineChart from './Chart.vue'
    export default {
        components: {
            AppLayout,
            Welcome,
            LineChart
        },
        props: ['data', 'errors'],
        data() {
            return {
                editMode: false,
                isOpen: false,
                form: {
                    title: null,
                    description: null,
                },
                interval: null,
                loaded: false,
                chartdata: {
                    labels: [],
                    datasets: [{
                            label: 'Tasks',
                            backgroundColor: '#FCD25B',
                            data: []
                    }]
                }
            }
        },
        mounted () {
            this.loaded = true  
            this.interval = setInterval(() => {
                this.fillData();
            },60000);
            this.fillData();
            
        },
        
        methods: {
            openModal: function () {
                this.isOpen = true;
            },
            closeModal: function () {
                this.isOpen = false;
                this.reset();
                this.editMode=false;
            },
            reset: function () {
                this.form = {
                    title: null,
                    description: null,
                }
            },
            save: function (data) {
                this.$inertia.post('/tasks', data)
                this.reset();
                this.closeModal();
                this.editMode = false;
            },
            edit: function (data) {
                this.form = Object.assign({}, data);
                this.editMode = true;
                this.openModal();
            },
            update: function (data) {
                data._method = 'PUT';
                this.$inertia.post('/tasks/' + data.id, data)
                this.reset();
                this.closeModal();
            },
            deleteRow: function (data) {
                if (!confirm('Are you sure want to remove?')) return;
                data._method = 'DELETE';
                this.$inertia.post('/tasks/' + data.id, data)
                this.reset();
                this.closeModal();
            },
            updateStatus: function(data){
                data._method = 'POST';
                data.status = data.status == 1 ? 0:1;
                this.$inertia.post('/tasks/updatestatus/' + data.id, data)
                this.reset();
                this.closeModal();
            },
            countOpenTasks: function(){
                let res = 0;
                for(let x=0;x < this.data.length; x++){
                    if(this.data[x].status == 0) {
                        res++;
                    }
                }
                return res;
            },
            fillData () {
                var d = new Date();
                let time = d.getHours()+":"+d.getMinutes();

                this.chartdata.labels.push(time);  
                this.chartdata.datasets[0].data.push(this.countOpenTasks());
                
            },
        }
    }
</script>