/**
 * Global Real-Time Updates for BSU HRS Admin
 */

window.globalFacMaxId = 0;
window.globalGuestMaxId = 0;

function initGlobalRealTime(facMax, guestMax) {
    window.globalFacMaxId = facMax;
    window.globalGuestMaxId = guestMax;
    
    // Start polling
    checkGlobalUpdates();
}

function checkGlobalUpdates() {
    fetch(`${BASE_URL}admin/ajax/check_new_reservations.php?last_fac_id=${window.globalFacMaxId}&last_guest_id=${window.globalGuestMaxId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.has_new) {
                console.log('New reservations detected globally!');
                
                // Update sidebar badge
                updateSidebarBadge(data.new_fac_count + data.new_guest_count);
                
                // If we are on the pending reservations page, trigger its refresh
                if (typeof updateReservationGrid === 'function') {
                    updateReservationGrid();
                } else {
                    // Optional: Show a toast/notification to the admin
                    showNewReservationToast(data.new_fac_count + data.new_guest_count);
                }
                
                // Update max IDs so we don't keep notifying for the same ones
                window.globalFacMaxId = data.current_max_fac;
                window.globalGuestMaxId = data.current_max_guest;
            }
        })
        .catch(err => console.error('Global polling error:', err))
        .finally(() => {
            setTimeout(checkGlobalUpdates, 8000); // Poll every 8 seconds
        });
}

function updateSidebarBadge(newCount) {
    const badge = document.querySelector('.sidebar-nav a[href="reservations.php"] .badge-count');
    const banner = document.getElementById('pendingBanner');
    const bannerText = document.getElementById('bannerText');

    if (badge || banner) {
        // Here we need the TOTAL pending count, not just the NEW ones.
        fetch(`${BASE_URL}admin/ajax/get_pending_reservations_data.php`)
            .then(res => res.json())
            .then(data => {
                // Update sidebar badge
                if (badge) {
                    badge.textContent = data.counts.pending;
                    if (data.counts.pending > 0) {
                        badge.classList.remove('d-none');
                    } else {
                        badge.classList.add('d-none');
                    }
                }

                // Update calendar banner if it exists
                if (banner && bannerText) {
                    const total = data.counts.pending + data.counts.pencil;
                    if (total > 0) {
                        banner.style.display = 'flex';
                        bannerText.textContent = `${data.counts.pending} pending & ${data.counts.pencil} pencil-booked reservation${total != 1 ? 's' : ''} awaiting action`;
                    } else {
                        banner.style.display = 'none';
                    }
                }
            });
    }
}

function showNewReservationToast(count) {
    // Check if a toast container exists, if not create one
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.style.position = 'fixed';
        container.style.bottom = '20px';
        container.style.right = '20px';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = 'alert alert-success alert-dismissible fade show shadow-lg';
    toast.style.minWidth = '250px';
    toast.style.borderLeft = '5px solid #28a745';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi bi-bell-fill me-2 fs-4"></i>
            <div>
                <strong>New Reservation!</strong><br>
                ${count} new request(s) received.
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Play a subtle sound if possible (optional)
    try {
        const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3');
        audio.volume = 0.3;
        audio.play();
    } catch(e) {}

    // Auto remove after 10 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 500);
    }, 10000);
}
