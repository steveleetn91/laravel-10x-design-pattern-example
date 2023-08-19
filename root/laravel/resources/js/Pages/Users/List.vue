<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
defineProps({ loggedData: {
    id : 0
},token : "" })
</script>
<script>

export default {
    data() {
        return {
            responseData: {
                data: []
            }
        }
    },
    created() {
        this.pullData();
    },
    methods: {
        pullData(){
            fetch('/api/dashboard/users',{
                headers: {
                    "authorization" : "Bearer " + this.token
                }
            }).then((res) => {
                if (res.status === 200) {
                    res.json().then((data) => {
                        this.responseData = data.response
                        console.log(this.responseData)
                    })
                }
            });
        },
        deleteUser(userId) {
            console.log(this.loggedData);
            if(confirm("do you wanna delete it?")) {
                fetch('/api/dashboard/users/' + userId,{
                method: "DELETE",
                headers: {
                    "content-type" : "application/json",
                    "authorization" : "Bearer " + this.token
                },
                body: JSON.stringify({})
                }).then((resp) => {
                    if(resp.status === 200 ) {
                        this.pullData();
                    } else {
                        resp.json().then((data) => {
                            alert(JSON.stringify(data));
                        })
                    }
                });
            }
        }
    }
}
</script>

<template>
    <Head title="User" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-4xl">List User</h1>
                <p>Manage users</p>
                <Link :href="route('users-create')" class="text-blue-800 border-blue-800 p-2">+ Create new</Link>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-10">
                    <table class="table-auto w-full border-radius">
                        <thead class="border text-left">
                            <tr class="">
                                <th>Name</th>
                                <th>Email</th>
                                <th>Created time</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in responseData.data">
                                <td>{{ item.name }}</td>
                                <td>{{item.email}}</td>
                                <td>{{ new Date(item.created_at).getUTCFullYear()
                                 +'/'+ (new Date(item.created_at).getMonth() + 1)
                                 +'/'+ (new Date(item.created_at).getDate() + 1) }}</td>
                                <td>
                                    <Link class="text-blue-400" :href="route('users-edit',item.id)">Edit</Link>
                                </td>
                                <td>
                                    <p :style="loggedData.id === item.id ? 'visibility:hidden' : ''" class="text-red-400" @click="deleteUser(item.id)">Delete</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
