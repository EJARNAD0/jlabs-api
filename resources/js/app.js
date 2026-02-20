import './bootstrap';

window.ipTracker = function () {
    return {
        view: 'login', // 'login' or 'home'
        user: null,
        token: localStorage.getItem('token') || null,
        loading: false,
        error: '',
        loginForm: {
            email: '',
            password: ''
        },

        // Geo & Map
        searchQuery: '',
        searchError: '',
        ipInfo: null,
        map: null,
        marker: null,

        // History
        history: [],
        selectedHistoryIds: [],
        selectAll: false,

        async init() {
            this.loading = true;
            if (this.token) {
                try {
                    window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                    await this.fetchUser();
                    this.view = 'home';
                    // Load home data
                    await this.fetchHistory();
                    await this.fetchIpInfo(); // Load current user IP info
                } catch (e) {
                    console.error("Init error", e);
                    this.logout();
                }
            } else {
                this.view = 'login';
            }
            this.loading = false;

            // Watchers
            this.$watch('view', (value) => {
                if (value === 'home') {
                    setTimeout(() => {
                        this.initMap();
                    }, 100);
                }
            });

            this.$watch('selectAll', (value) => {
                if (value) {
                    this.selectedHistoryIds = this.history.map(h => h.id);
                } else {
                    this.selectedHistoryIds = [];
                }
            });
        },

        async login() {
            this.loading = true;
            this.error = '';
            try {
                const response = await window.axios.post('/api/login', this.loginForm);
                this.token = response.data.token;
                this.user = response.data.user;
                localStorage.setItem('token', this.token);
                window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
                this.view = 'home';

                await this.fetchHistory();
                await this.fetchIpInfo();
            } catch (e) {
                this.error = e.response?.data?.message || 'Login failed';
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true;
            try {
                if (this.token) {
                    await window.axios.post('/api/logout');
                }
            } catch (e) {
                // Ignore logout errors
            }
            this.token = null;
            this.user = null;
            localStorage.removeItem('token');
            delete window.axios.defaults.headers.common['Authorization'];
            this.view = 'login';
            this.loginForm.email = '';
            this.loginForm.password = '';
            this.loading = false;
        },

        async fetchUser() {
            const response = await window.axios.get('/api/me');
            this.user = response.data.user;
        },

        // Geolocation Logic
        async fetchIpInfo(ip = '') {
            this.searchError = '';
            this.loading = true;
            try {
                let url = 'https://ipinfo.io/json';
                if (ip) {
                    url = `https://ipinfo.io/${ip}/json`;
                }

                const response = await window.axios.get(url);
                this.ipInfo = response.data;

                if (this.ipInfo.loc) {
                    this.updateMap(this.ipInfo.loc);
                }

                // If searching (ip provided), save to history
                if (ip) {
                     await this.saveHistory(this.ipInfo.ip, this.ipInfo);
                }

            } catch (e) {
                console.error(e);
                this.searchError = 'Failed to fetch IP information. Invalid IP or network error.';
                // Do not clear ipInfo if search fails, or maybe yes?
                // Requirement: "Should display an error if entered data is not a valid ip address."
            } finally {
                this.loading = false;
            }
        },

        async searchIp() {
            if (!this.searchQuery) return;

            // Simple validation
            // Check valid IP address format (IPv4)
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;

            if (!ipRegex.test(this.searchQuery)) {
                this.searchError = 'Invalid IP address format.';
                return;
            }

            await this.fetchIpInfo(this.searchQuery);
        },

        async clearSearch() {
            this.searchQuery = '';
            this.searchError = '';
            await this.fetchIpInfo(); // Revert to current user
        },

        initMap() {
             if (this.map) return;
             if (document.getElementById('map')) {
                 this.map = L.map('map').setView([0, 0], 2);
                 L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(this.map);

                if (this.ipInfo && this.ipInfo.loc) {
                    this.updateMap(this.ipInfo.loc);
                }
             }
        },

        updateMap(loc) {
            if (!this.map) return;
            const [lat, lng] = loc.split(',').map(Number);
            this.map.setView([lat, lng], 13);

            if (this.marker) {
                this.marker.setLatLng([lat, lng]);
            } else {
                this.marker = L.marker([lat, lng]).addTo(this.map);
            }
            // Bind popup with city/region
            const text = this.ipInfo ? `<b>${this.ipInfo.city}, ${this.ipInfo.region}</b>` : 'Location';
            this.marker.bindPopup(text).openPopup();
        },

        // History Logic
        async fetchHistory() {
             try {
                 const response = await window.axios.get('/api/histories');
                 this.history = response.data;
             } catch (e) {
                 console.error("Fetch history failed", e);
             }
        },

        async saveHistory(ip, payload) {
            try {
                await window.axios.post('/api/histories', { ip, payload });
                await this.fetchHistory();
            } catch (e) {
                 console.error("Save history failed", e);
            }
        },

        async deleteHistory() {
            if (this.selectedHistoryIds.length === 0) return;

            if (!confirm('Are you sure you want to delete selected items?')) return;

            this.loading = true;
            try {
                await window.axios.delete('/api/histories', { data: { ids: this.selectedHistoryIds } });
                this.selectedHistoryIds = [];
                this.selectAll = false;
                await this.fetchHistory();
            } catch (e) {
                console.error("Delete history failed", e);
            } finally {
                this.loading = false;
            }
        },

        loadHistory(item) {
            this.searchQuery = item.ip;
            this.ipInfo = item.payload || { ip: item.ip }; // Fallback
            if (this.ipInfo.loc) {
                this.updateMap(this.ipInfo.loc);
            }
        },

        toggleAllHistory() {
             // Logic handled by watcher on selectAll usually, but let's be explicit here if watcher fails
        }
    }
}
