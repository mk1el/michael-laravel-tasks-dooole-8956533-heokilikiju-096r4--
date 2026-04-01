<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager Dashboard</title>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-4 md:p-8">
    <div id="app" class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold mb-8 text-gray-800">Task Management System</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Forms -->
            <div class="lg:col-span-1 space-y-8">
                
                <!-- Create Task Form -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-semibold mb-4 text-blue-600 border-b pb-2">Create New Task</h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input v-model="newTask.title" type="text" placeholder="What needs to be done?" class="w-full border p-2 rounded mt-1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Due Date</label>
                            <input v-model="newTask.due_date" type="date" class="w-full border p-2 rounded mt-1">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority</label>
                            <select v-model="newTask.priority" class="w-full border p-2 rounded mt-1">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <button @click="createTask" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">
                            Save Task
                        </button>
                        <p v-if="error" class="text-red-500 text-sm mt-2 font-medium">@{{ error }}</p>
                    </div>
                </div>

                <!-- Daily Report Section (Bonus Requirement) -->
                <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-purple-500">
                    <h2 class="text-xl font-semibold mb-4">Daily Report</h2>
                    <div class="flex gap-2 mb-4">
                        <input v-model="reportDate" type="date" class="flex-1 border p-2 rounded text-sm">
                        <button @click="fetchReport" class="bg-purple-600 text-white px-3 py-2 rounded text-sm hover:bg-purple-700">Get</button>
                    </div>
                    
                    <div v-if="reportData" class="text-xs space-y-3">
                        <div v-for="(stats, priority) in reportData.summary" :key="priority" class="bg-gray-50 p-2 rounded">
                            <span class="font-bold uppercase text-gray-600">@{{ priority }}:</span>
                            <div class="flex justify-between mt-1 text-gray-500">
                                <span>Pending: @{{ stats.pending }}</span>
                                <span>In Progress: @{{ stats.in_progress }}</span>
                                <span>Done: @{{ stats.done }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Task List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 border-b flex justify-between items-center bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-700">Your Tasks</h2>
                        <div class="flex items-center gap-2 text-sm">
                            <label>Filter:</label>
                            <select v-model="filterStatus" @change="fetchTasks" class="border p-1 rounded">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="done">Done</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="p-4">Task</th>
                                    <th class="p-4">Due Date</th>
                                    <th class="p-4">Priority</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="task in tasks" :key="task.id" class="hover:bg-gray-50">
                                    <td class="p-4 font-medium">@{{ task.title }}</td>
                                    <td class="p-4 text-sm text-gray-600">@{{ task.due_date }}</td>
                                    <td class="p-4">
                                        <span :class="priorityClass(task.priority)">@{{ task.priority }}</span>
                                    </td>
                                    <td class="p-4">
                                        <span class="text-xs px-2 py-1 rounded bg-gray-200 text-gray-700 capitalize italic">
                                            @{{ task.status.replace('_', ' ') }}
                                        </span>
                                    </td>
                                    <td class="p-4 flex justify-center gap-2">
                                        <!-- Step Forward Button -->
                                        <button v-if="task.status !== 'done'" 
                                            @click="nextStatus(task)" 
                                            title="Move to next status"
                                            class="bg-green-100 text-green-700 p-2 rounded hover:bg-green-200">
                                            ➔
                                        </button>
                                        <!-- Delete Button -->
                                        <button @click="deleteTask(task.id)" 
                                            :disabled="task.status !== 'done'"
                                            :title="task.status !== 'done' ? 'Only done tasks can be deleted' : 'Delete task'"
                                            :class="task.status === 'done' ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'bg-gray-100 text-gray-400 cursor-not-allowed'"
                                            class="p-2 rounded">
                                            ✕
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="tasks.length === 0" class="p-12 text-center text-gray-400 italic">
                        No tasks found matching your filter.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    tasks: [],
                    filterStatus: '',
                    error: '',
                    reportDate: new Date().toISOString().substr(0, 10),
                    reportData: null,
                    newTask: {
                        title: '',
                        due_date: new Date().toISOString().substr(0, 10),
                        priority: 'medium'
                    }
                }
            },
            methods: {
                fetchTasks() {
                    let url = '/api/tasks';
                    if (this.filterStatus) url += `?status=${this.filterStatus}`;
                    
                    axios.get(url).then(res => {
                        // Laravel returns the list of tasks directly or the message
                        this.tasks = Array.isArray(res.data) ? res.data : [];
                    });
                },
                createTask() {
                    this.error = '';
                    // URL Updated to match: POST /api/tasks
                    axios.post('/api/tasks', this.newTask)
                        .then(() => {
                            this.newTask.title = '';
                            this.fetchTasks();
                        })
                        .catch(err => {
                            this.error = err.response.data.error || "Validation failed (check Title/Date)";
                        });
                },
                fetchReport() {
                    axios.get(`/api/tasks/report?date=${this.reportDate}`)
                        .then(res => {
                            this.reportData = res.data;
                        });
                },
                nextStatus(task) {
                    let next = task.status === 'pending' ? 'in_progress' : 'done';
                    // URL Matches: PATCH /api/tasks/{id}/status
                    axios.patch(`/api/tasks/${task.id}/status`, { status: next })
                        .then(() => this.fetchTasks());
                },
                deleteTask(id) {
                    if(!confirm('Are you sure you want to delete this task?')) return;
                    // URL Matches: DELETE /api/tasks/{id}
                    axios.delete(`/api/tasks/${id}`)
                        .then(() => this.fetchTasks())
                        .catch(err => alert(err.response.data.error));
                },
                priorityClass(p) {
                    return {
                        'px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider': true,
                        'bg-red-600 text-white': p === 'high',
                        'bg-yellow-400 text-yellow-900': p === 'medium',
                        'bg-blue-500 text-white': p === 'low',
                    };
                }
            },
            mounted() {
                this.fetchTasks();
                this.fetchReport();
            }
        }).mount('#app');
    </script>
</body>
</html>