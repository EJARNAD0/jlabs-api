<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IP Tracker</title>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        #map { height: 400px; width: 100%; z-index: 10; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen font-sans antialiased" x-data="ipTracker()" x-init="init()">

    <!-- Loading State -->
    <div x-show="loading" class="fixed inset-0 flex items-center justify-center bg-white bg-opacity-75 z-50">
        <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-gray-900"></div>
    </div>

    <!-- Login Screen -->
    <div x-show="view === 'login'" x-cloak class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded shadow-md w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
            <form @submit.prevent="login">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input x-model="loginForm.email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email" type="email" placeholder="Email">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                    <input x-model="loginForm.password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="Password">
                </div>
                <div class="flex items-center justify-between">
                    <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full" type="submit">
                        Sign In
                    </button>
                </div>
                <p x-show="error" x-text="error" class="text-red-500 text-xs italic mt-4"></p>
            </form>
        </div>
    </div>

    <!-- Home Screen -->
    <div x-show="view === 'home'" x-cloak class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">IP Tracker</h1>
            <div class="flex items-center gap-4">
                 <span x-text="user.name" class="font-semibold"></span>
                 <button @click="logout" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">Logout</button>
            </div>
        </div>

        <!-- Search & Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Search & Current Info -->
            <div class="bg-white p-6 rounded shadow">
                <div class="flex gap-2 mb-4">
                    <input x-model="searchQuery" type="text" placeholder="Enter IP Address" class="border p-2 rounded w-full">
                    <button @click="searchIp" class="bg-blue-500 text-white p-2 rounded">Search</button>
                    <button @click="clearSearch" class="bg-gray-300 text-gray-700 p-2 rounded">Clear</button>
                </div>
                <p x-show="searchError" x-text="searchError" class="text-red-500 text-sm mb-4"></p>

                <div x-show="ipInfo" class="space-y-2">
                    <h3 class="font-bold text-lg mb-2">IP Information</h3>
                    <p><strong>IP:</strong> <span x-text="ipInfo?.ip"></span></p>
                    <p><strong>City:</strong> <span x-text="ipInfo?.city"></span></p>
                    <p><strong>Region:</strong> <span x-text="ipInfo?.region"></span></p>
                    <p><strong>Country:</strong> <span x-text="ipInfo?.country"></span></p>
                    <p><strong>Loc:</strong> <span x-text="ipInfo?.loc"></span></p>
                    <p><strong>Org:</strong> <span x-text="ipInfo?.org"></span></p>
                </div>
            </div>

            <!-- Map -->
            <div class="bg-white p-6 rounded shadow">
                <div id="map"></div>
            </div>
        </div>

        <!-- History -->
        <div class="bg-white p-6 rounded shadow">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-xl">Search History</h3>
                <button @click="deleteHistory" x-show="selectedHistoryIds.length > 0" class="bg-red-500 text-white px-3 py-1 rounded text-sm">Delete Selected</button>
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="border-b p-2 w-10"><input type="checkbox" @change="toggleAllHistory" x-model="selectAll"></th>
                        <th class="border-b p-2">IP Address</th>
                        <th class="border-b p-2">Date</th>
                        <th class="border-b p-2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="item in history" :key="item.id">
                        <tr class="hover:bg-gray-50">
                            <td class="border-b p-2"><input type="checkbox" :value="item.id" x-model="selectedHistoryIds"></td>
                            <td class="border-b p-2" x-text="item.ip"></td>
                            <td class="border-b p-2" x-text="new Date(item.created_at).toLocaleString()"></td>
                            <td class="border-b p-2">
                                <button @click="loadHistory(item)" class="text-blue-500 hover:underline">View</button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="history.length === 0">
                        <td colspan="4" class="p-4 text-center text-gray-500">No history found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</body>
</html>
