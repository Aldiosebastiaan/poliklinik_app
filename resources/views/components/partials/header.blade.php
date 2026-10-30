<header class="bg-base-100 border-b border-base-300 h-16 flex items-center px-6 gap-4 sticky top-0 z-30 shadow-sm">
    @php($namaPengguna = auth()->user()->nama ?? 'Pengguna')

    {{-- Mobile Hamburger --}}
    <button onclick="toggleSidebar()" class="btn btn-square btn-ghost lg:hidden">
        <i data-lucide="menu" class="w-5 h-5"></i>
    </button>

    {{-- Breadcrumb --}}
    <div class="flex-1">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-base-content/50">Poliklinik</span>
            <i data-lucide="chevron-right" class="w-4 h-4 text-base-content/30"></i>
            <span class="font-semibold text-base-content">
                {{ $title ?? 'Dashboard' }}
            </span>
        </div>
    </div>

    {{-- Fullscreen --}}
    <button onclick="toggleFullscreen()" class="btn btn-square btn-ghost">
        <i id="fsIcon" class="fas fa-expand w-5 h-5"></i>
    </button>

    {{-- User Info --}}
    <div class="flex items-center gap-3">

        <div class="text-right hidden sm:block">
            <div class="text-sm font-semibold leading-tight">
                {{ $namaPengguna }}
            </div>
            <div class="text-xs text-base-content/50 leading-tight">
                {{ auth()->user()->role ?? 'Admin Sistem' }}
            </div>
        </div>

        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr($namaPengguna, 0, 1)) }}
                </span>
            </div>
        </div>

        {{-- Bell Icon with Unread Count --}}
        <div class="relative">
            <a href="#" id="notifications-btn" class="btn btn-ghost text-sm p-0" aria-label="Notifikasi" onclick="toggleNotifications(event)">
                <i data-lucide="bell" class="w-5 h-5"></i>
            </a>
            <span id="unread-count"
                class="hidden absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 text-[10px] font-bold leading-none
                       bg-warning text-warning-content rounded-full flex items-center justify-center">
            </span>
        </div>
    </div>

    {{-- Notifications Dropdown --}}
    <div id="notifications-dropdown" class="fixed right-0 top-16 w-64 bg-base-100 rounded-md shadow-lg p-4 hidden z-50" role="menu" aria-label="Daftar Notifikasi">
        <span class="text-sm font-medium mb-2 block">Notifikasi</span>
        <div id="notifications-list" class="space-y-2 max-h-80 overflow-y-auto">
            {{-- Loaded via Javascript --}}
        </div>
        <div id="empty-notifications" class="hidden text-center text-sm text-base-content/50 py-4">
            <i class="fas fa-bell-slash mb-1"></i>
            <p>Tidak ada notifikasi</p>
        </div>
    </div>
</header>

<script>
    // Initialize Lucide Icons
    lucide.createIcons();

    let notificationsTimeout = null;

    function toggleNotifications(event) {
        event.preventDefault();
        const dropdown = document.getElementById('notifications-dropdown');
        const btn = document.getElementById('notifications-btn');

        if (dropdown.classList.contains('hidden')) {
            fetchNotifications();
            dropdown.classList.remove('hidden');
            dropdown.classList.add('block');
        } else {
            dropdown.classList.add('hidden');
            dropdown.classList.remove('block');
        }
    }

    function fetchNotifications() {
        fetch('/notifications')
            .then(response => response.json())
            .then(data => {
                const list = document.getElementById('notifications-list');
                const emptyState = document.getElementById('empty-notifications');

                if (data.notifications.length > 0) {
                    list.innerHTML = '';

                    data.notifications.forEach(notification => {
                        const readClass = notification.read_at ? 'line-through opacity-50' : '';
                        const type = notification.data.type;
                        const dotColor = type === 'pembayaran_ditolak' ? 'bg-error' :
                            (type === 'pembayaran_diverifikasi' ? 'bg-success' : 'bg-primary');
                        const timeAgo = getTimeAgo(notification.created_at);

                        const div = document.createElement('div');
                        div.className = `flex items-start gap-3 cursor-pointer p-2 rounded hover:bg-base-200 ${readClass}`;
                        div.innerHTML = `
                            <div class="w-3 h-3 rounded-full ${dotColor} ${readClass}"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm ${notification.read_at ? 'font-normal' : 'font-medium'} ${readClass}">${escapeHtml(notification.data.message)}</p>
                                <p class="text-xs text-base-content/60 ${readClass}">${timeAgo}</p>
                            </div>
                        `;
                        div.addEventListener('click', () => markAsRead(notification.id));
                        list.appendChild(div);
                    });

                    emptyState.classList.add('hidden');
                } else {
                    list.innerHTML = '';
                    emptyState.classList.remove('hidden');
                }

                updateUnreadCount(data.unreadCount);
            })
            .catch(() => {
                console.error('Gagal memuat notifikasi');
            });
    }

    function getTimeAgo(createdAt) {
        const now = new Date();
        const created = new Date(createdAt);
        const diffMs = now - created;

        const diffMinutes = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffDays > 0) {
            return `${diffDays} ${diffDays > 1 ? 'hari' : 'hari yang lalu'}`;
        }
        if (diffHours > 0) {
            return `${diffHours} ${diffHours > 1 ? 'jam' : 'jam yang lalu'}`;
        }
        if (diffMinutes > 0) {
            return `${diffMinutes} ${diffMinutes > 1 ? 'menit' : 'menit yang lalu'}`;
        }
        return 'baru saja';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function markAsRead(id) {
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(() => fetchNotifications())
            .catch(() => {
                console.error('Gagal menandai baca');
            });
    }

    function updateUnreadCount(count) {
        const badge = document.getElementById('unread-count');
        const notificationsBtn = document.getElementById('notifications-btn');

        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
            notificationsBtn.classList.add('ring', 'ring-offset-2');
        } else {
            badge.textContent = '';
            badge.classList.add('hidden');
            notificationsBtn.classList.remove('ring', 'ring-offset-2');
        }
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        const dropdown = document.getElementById('notifications-dropdown');
        const btn = document.getElementById('notifications-btn');

        if (!e.target.closest('#notifications-btn') && !e.target.closest('#notifications-dropdown')) {
            dropdown.classList.add('hidden');
            dropdown.classList.remove('block');
        }
    });

    // Fetch notifications on page load
    document.addEventListener('DOMContentLoaded', () => {
        fetchNotifications();
    });
</script>
