<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
defineProps({ loggedData: {
    id : 0
},token : ""  })
</script>
<script>

export default {
    data() {
        return {
            responseData: {
                data: []
            },
            email : "",
            name: "",
            password: "",
            processing: false,
            disableButton : true,
            errors: {
                email: "",
                name: "",
                password: ""
            } 
        }
    },
    created() {
       
    },
    methods: {
        createUser() {
            this.processing = true;
            fetch('/api/dashboard/users',{
                method: "POST",
                headers: {
                    "content-type" : "application/json",
                    "authorization" : "Bearer " + this.token
                },
                body: JSON.stringify({
                    email: this.email,
                    password: this.password,
                    name: this.name
                })
            }).then((resp) => {
                if(resp.status === 200 ) {
                    alert("Create success");
                    this.errors.email = ''
                       this.errors.password = ''
                       this.errors.name = ''
                       return window.location.href = route('users-list');
                } else {
                    resp.json().then((data) => {
                        this.processing = false;
                       this.errors.email = data.response.email ? data.response.email[0] : ''
                       this.errors.password = data.response.password ? data.response.password[0] : ''
                       this.errors.name = data.response.name ? data.response.name[0] : ''
                    });
                }
            });
        }
    },
    watch:{
        email(newdata,old) {
            this.email = newdata;
            if(this.email !== ""
            && this.name !== ""
            && this.password !== "") {
                this.disableButton = false;
            }
        },
        password(newdata,old) {
            this.password = newdata;
            if(this.email !== ""
            && this.name !== ""
            && this.password !== "") {
                this.disableButton = false;
            }
        },
        name(newdata,old) {
            this.name = newdata;
            if(this.email !== ""
            && this.name !== ""
            && this.password !== "") {
                this.disableButton = false;
            }
        }
    }
}
</script>

<template>
    <Head title="Create User" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">User</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-4xl">Create User</h1>
                <p>Manage users</p>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-10">
                    <div class="p-5">
                        <InputLabel for="email" value="Email" />

                        <TextInput
                            id="email"
                            type="email"
                            v-model="email"
                            class="mt-1 block w-full"
                        />
                        
                        <p v-show=" errors.email === '' ? false : true" class="text-red-800">{{ errors.email }}</p>
                    </div>
                    <div class="p-5">
                        <InputLabel for="password" value="Password" />

                        <TextInput
                            id="password"
                            type="password"
                            v-model="password"
                            class="mt-1 block w-full"
                        />
                        <p v-show=" errors.password === '' ? false : true" class="text-red-800">{{ errors.password }}</p>
                    </div>
                    <div class="p-5">
                        <InputLabel for="name" value="Name" />

                        <TextInput
                            id="name"
                            type="text"
                            v-model="name"
                            class="mt-1 block w-full"
                        />
                        <p v-show=" errors.name === '' ? false : true" class="text-red-800">{{ errors.name }}</p>
                    </div>
                    <div class="p-5">
                        <PrimaryButton 
                        @click="createUser()"
                        :disabled="disableButton || processing">Save</PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
