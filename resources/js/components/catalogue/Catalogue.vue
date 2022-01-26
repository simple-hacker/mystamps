<template>
    <div>
        <div class="flex-1 ml-1 flex-col">
            <div class="mb-4 p-4 bg-darker text-white rounded shadow">
                <div class="flex justify-between items-center relative">
                    <h1 class="text-4xl p-2 flex-1 text-center">Stamps for {{ year }}</h1>

                    <div v-if="admin" class="absolute top-0 right-0 flex flex-col items-end">
                        <dropdown-menu>
                            <template v-slot:dropdown-toggle>
                                <button class="flex border bg-white hover:bg-gray-100 text-dark rounded shadow px-4 py-2 mb-1">
                                    <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32">
                                        <path d="M11.366 22.564l1.291-1.807-1.414-1.414-1.807 1.291c-0.335-0.187-0.694-0.337-1.071-0.444l-0.365-2.19h-2l-0.365 2.19c-0.377 0.107-0.736 0.256-1.071 0.444l-1.807-1.291-1.414 1.414 1.291 1.807c-0.187 0.335-0.337 0.694-0.443 1.071l-2.19 0.365v2l2.19 0.365c0.107 0.377 0.256 0.736 0.444 1.071l-1.291 1.807 1.414 1.414 1.807-1.291c0.335 0.187 0.694 0.337 1.071 0.444l0.365 2.19h2l0.365-2.19c0.377-0.107 0.736-0.256 1.071-0.444l1.807 1.291 1.414-1.414-1.291-1.807c0.187-0.335 0.337-0.694 0.444-1.071l2.19-0.365v-2l-2.19-0.365c-0.107-0.377-0.256-0.736-0.444-1.071zM7 27c-1.105 0-2-0.895-2-2s0.895-2 2-2 2 0.895 2 2-0.895 2-2 2zM32 12v-2l-2.106-0.383c-0.039-0.251-0.088-0.499-0.148-0.743l1.799-1.159-0.765-1.848-2.092 0.452c-0.132-0.216-0.273-0.426-0.422-0.629l1.219-1.761-1.414-1.414-1.761 1.219c-0.203-0.149-0.413-0.29-0.629-0.422l0.452-2.092-1.848-0.765-1.159 1.799c-0.244-0.059-0.492-0.109-0.743-0.148l-0.383-2.106h-2l-0.383 2.106c-0.251 0.039-0.499 0.088-0.743 0.148l-1.159-1.799-1.848 0.765 0.452 2.092c-0.216 0.132-0.426 0.273-0.629 0.422l-1.761-1.219-1.414 1.414 1.219 1.761c-0.149 0.203-0.29 0.413-0.422 0.629l-2.092-0.452-0.765 1.848 1.799 1.159c-0.059 0.244-0.109 0.492-0.148 0.743l-2.106 0.383v2l2.106 0.383c0.039 0.251 0.088 0.499 0.148 0.743l-1.799 1.159 0.765 1.848 2.092-0.452c0.132 0.216 0.273 0.426 0.422 0.629l-1.219 1.761 1.414 1.414 1.761-1.219c0.203 0.149 0.413 0.29 0.629 0.422l-0.452 2.092 1.848 0.765 1.159-1.799c0.244 0.059 0.492 0.109 0.743 0.148l0.383 2.106h2l0.383-2.106c0.251-0.039 0.499-0.088 0.743-0.148l1.159 1.799 1.848-0.765-0.452-2.092c0.216-0.132 0.426-0.273 0.629-0.422l1.761 1.219 1.414-1.414-1.219-1.761c0.149-0.203 0.29-0.413 0.422-0.629l2.092 0.452 0.765-1.848-1.799-1.159c0.059-0.244 0.109-0.492 0.148-0.743l2.106-0.383zM21 15.35c-2.402 0-4.35-1.948-4.35-4.35s1.948-4.35 4.35-4.35 4.35 1.948 4.35 4.35c0 2.402-1.948 4.35-4.35 4.35z"></path>
                                    </svg>
                                </button>
                            </template>
                            <template v-slot:dropdown-contents>
                                <div class="flex flex-col items-stretch">
                                        <a :href="'/scraper/issuesByYear/'+year" class="flex bg-blue-500 hover:bg-blue-600 text-white border rounded px-4 py-2">
                                            <svg class="fill-current mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 512">
                                                <path d="M378.24,243.712l-96-80c-4.768-3.968-11.424-4.832-17.024-2.208C259.584,164.128,256,169.792,256,176v48H16
                                                    c-8.832,0-16,7.168-16,16v32c0,8.832,7.168,16,16,16h240v48c0,6.208,3.584,11.84,9.216,14.496c2.144,0.992,4.48,1.504,6.784,1.504
                                                    c3.68,0,7.328-1.248,10.24-3.712l96-80c3.68-3.04,5.76-7.552,5.76-12.288C384,251.264,381.92,246.752,378.24,243.712z"/>
                                                <path d="M480,0H32C14.336,0,0,14.336,0,32v160h64V64h384v384H64V320H0v160c0,17.696,14.336,32,32,32h448c17.696,0,32-14.304,32-32
                                                    V32C512,14.336,497.696,0,480,0z"/>
                                            </svg>
                                            Import Issues
                                        </a>
                                        <a :href="'/issue/create/'+year" class="flex bg-green-500 hover:bg-green-600 text-white border rounded px-4 py-2">
                                            <svg class="fill-current mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                                                <path d="M38 6H10c-2.21 0-4 1.79-4 4v28c0 2.21 1.79 4 4 4h28c2.21 0 4-1.79 4-4V10c0-2.21-1.79-4-4-4zm-4 20h-8v8h-4v-8h-8v-4h8v-8h4v8h8v4z"></path>
                                            </svg>
                                            Add Issue
                                        </a>
                                </div>
                            </template>
                        </dropdown-menu>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loop through issues in catalogue year -->
        <div
            v-for="issue in issuesArray"
            :key="issue.id"
            class="flex flex-col mb-8 bg-white rounded shadow">
                <a :href="issue.path">
                    <div class="flex justify-between items-center relative px-4 py-2 border border-darker bg-dark hover:bg-highlight hover:border-dark">
                        <div>
                            <h2 class="text-white text-2xl" v-text="issue.title"></h2>
                            <small class="mb-3 text-white" v-if="issue.release_date" v-text="'Released on '+convertDate(issue.release_date)"></small>
                        </div>
                        <div class="flex">
                            <a v-if="issue.url" :href="issue.url" target="_blank" class="border bg-white hover:bg-gray-200 text-dark rounded shadow px-4 py-2 mb-1 mr-5 flex items-center justify-center">
                                <svg focusable="false" data-prefix="fab" data-icon="google-drive" class="w-5 h-5" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M339 314.9L175.4 32h161.2l163.6 282.9H339zm-137.5 23.6L120.9 480h310.5L512 338.5H201.5zM154.1 67.4L0 338.5 80.6 480 237 208.8 154.1 67.4z"></path></svg>
                            </a>
                            <a v-if="issue.sg_url" :href="issue.sg_url" target="_blank" class="border bg-white hover:bg-gray-200 text-dark rounded shadow px-4 py-2 mb-1 mr-5 flex items-center justify-center">
                                <span class="text-lg font-bold text-dark">SG</span>
                            </a>
                            <!-- <div
                                v-if="admin && issue.cgbs_issue"
                                class="flex flex-col items-end">
                                <dropdown-menu>
                                    <template v-slot:dropdown-toggle>
                                        <button class="flex border bg-white hover:bg-gray-200 rounded shadow px-4 py-2 mb-1">
                                            <svg class="fill-current" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 32 32">
                                                <path d="M29.181 19.070c-1.679-2.908-0.669-6.634 2.255-8.328l-3.145-5.447c-0.898 0.527-1.943 0.829-3.058 0.829-3.361 0-6.085-2.742-6.085-6.125h-6.289c0.008 1.044-0.252 2.103-0.811 3.070-1.679 2.908-5.411 3.897-8.339 2.211l-3.144 5.447c0.905 0.515 1.689 1.268 2.246 2.234 1.676 2.903 0.672 6.623-2.241 8.319l3.145 5.447c0.895-0.522 1.935-0.82 3.044-0.82 3.35 0 6.067 2.725 6.084 6.092h6.289c-0.003-1.034 0.259-2.080 0.811-3.038 1.676-2.903 5.399-3.894 8.325-2.219l3.145-5.447c-0.899-0.515-1.678-1.266-2.232-2.226zM16 22.479c-3.578 0-6.479-2.901-6.479-6.479s2.901-6.479 6.479-6.479c3.578 0 6.479 2.901 6.479 6.479s-2.901 6.479-6.479 6.479z"></path>
                                            </svg>
                                        </button>
                                    </template>
                                    <template v-slot:dropdown-contents>
                                        <div class="flex flex-col items-stretch">
                                            <a v-if="issue.cgbs_issue" :href="'/scraper/issue/'+issue.cgbs_issue" class="flex bg-blue-500 hover:bg-blue-600 text-white border rounded px-4 py-2">
                                                <svg class="fill-current mr-2" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 512 512">
                                                    <path d="M378.24,243.712l-96-80c-4.768-3.968-11.424-4.832-17.024-2.208C259.584,164.128,256,169.792,256,176v48H16
                                                    c-8.832,0-16,7.168-16,16v32c0,8.832,7.168,16,16,16h240v48c0,6.208,3.584,11.84,9.216,14.496c2.144,0.992,4.48,1.504,6.784,1.504
                                                    c3.68,0,7.328-1.248,10.24-3.712l96-80c3.68-3.04,5.76-7.552,5.76-12.288C384,251.264,381.92,246.752,378.24,243.712z"/>
                                                    <path d="M480,0H32C14.336,0,0,14.336,0,32v160h64V64h384v384H64V320H0v160c0,17.696,14.336,32,32,32h448c17.696,0,32-14.304,32-32
                                                    V32C512,14.336,497.696,0,480,0z"/>
                                                </svg>
                                                Import Issue
                                            </a>
                                        </div>
                                    </template>
                                </dropdown-menu>
                            </div> -->
                        </div>

                    </div>
                </a>
                <div class="flex px-4 py-2">
                    <div
                        v-for="stamp in issue.stamps.slice(0, stampsToShow)"
                        :key="stamp.id"
                        class="flex flex-col items-center justify-start mr-1 p-2 border w-1/6">
                        <img :src="stamp.image_src" :alt="stamp.title" class="h-20 mb-1">
                        <p class="italic w-full text-center" v-if="stamp.sg_number" v-text="stamp.prefixedSgNumber"></p>
                        <p class="italic w-full text-center" v-if="stamp.sg_illustration" v-text="stamp.sg_illustration"></p>
                        <p class="text-sm w-full text-center" v-text="stamp.title"></p>
                    </div>
                    <a
                        v-if="(issue.stamps.length - stampsToShow) > 0"
                        :href="issue.path"
                        class="flex flex-col items-center justify-center mr-1 p-1 border w-1/6 hover:bg-light hover:border-highlight"
                    >
                        <p class="p-4 text-4xl" v-text="'+'+(issue.stamps.length - stampsToShow)"></p>
                    </a>
                </div>
            </div>
    </div>
</template>

<script>

    export default {
        data() {
            return {
                stampsToShow: 5,
            }
        },
        props: ['catalogue', 'admin', 'year'],
        computed: {
            issuesArray() {
                return Object.values(this.catalogue).sort(function(issue1, issue2) {
                    return issue1.release_date < issue2.release_date ? -1 : 0;
                });
            },
        },
        methods: {
            convertDate(date) {
                date = new Date(date);
                // Jesus fucking christ it's 2019 and you still have to format a date manually. PHP > JS
                return date.toLocaleString('default', { month: 'long' }) + ' ' + date.getDate() + ', ' + date.getFullYear();
            }
        },
    }
</script>

<style>

</style>
