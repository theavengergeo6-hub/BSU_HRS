            </div> <!-- Close content-area -->
        </div> <!-- Close main-content -->
    </div> <!-- Close admin-wrapper -->

    <!-- Bootstrap JS is already included in header.php -->
    <!-- Additional custom scripts can go here -->
    
    <!-- Global Loading Overlay -->
    <style>
        .global-loading-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .global-loading-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        .global-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #fdeae8;
            border-top: 4px solid #b71c1c; 
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        .global-loader-text {
            color: #b71c1c;
            font-weight: 600;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
    <div id="globalLoadingOverlay" class="global-loading-overlay">
        <div class="global-spinner"></div>
        <div id="globalLoaderText" class="global-loader-text">Processing, please wait...</div>
    </div>
    
    <script>
        window.showGlobalLoader = function(msg) {
            const textEl = document.getElementById('globalLoaderText');
            if (textEl) textEl.innerText = msg || 'Processing, please wait...';
            const overlay = document.getElementById('globalLoadingOverlay');
            if (overlay) overlay.classList.add('show');
        };
        window.hideGlobalLoader = function() {
            const overlay = document.getElementById('globalLoadingOverlay');
            if (overlay) overlay.classList.remove('show');
        };

        // Real-time Notification System
        (function() {
            <?php
            // Get initial max IDs to set as baseline for this page instance
            $init_fac_id = $conn->query("SELECT MAX(id) FROM facility_reservations")->fetch_row()[0] ?? 0;
            $init_guest_id = $conn->query("SELECT MAX(id) FROM guest_room_reservations")->fetch_row()[0] ?? 0;
            ?>
            let lastFacId = <?= (int)$init_fac_id ?>;
            let lastGuestId = <?= (int)$init_guest_id ?>;
            let isFirstCheck = true;

            // Generate notification sound using Web Audio API
            function playNotificationSound() {
                try {
                    const AudioContext = window.AudioContext || window.webkitAudioContext;
                    if (!AudioContext) return;
                    
                    const ctx = new AudioContext();
                    const osc = ctx.createOscillator();
                    const gain = ctx.createGain();
                    
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(880, ctx.currentTime); // High pitch (A5)
                    osc.frequency.exponentialRampToValueAtTime(440, ctx.currentTime + 0.1); 
                    
                    gain.gain.setValueAtTime(0, ctx.currentTime);
                    gain.gain.linearRampToValueAtTime(0.3, ctx.currentTime + 0.05);
                    gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    
                    osc.start();
                    osc.stop(ctx.currentTime + 0.5);
                } catch (e) {
                    console.log('Audio disabled by browser policy until interaction');
                }
            }

            // Create notification Toast container
            function showToast(message) {
                let container = document.getElementById('notification-toast-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'notification-toast-container';
                    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                    container.style.zIndex = '1055';
                    document.body.appendChild(container);
                }

                const toastEl = document.createElement('div');
                toastEl.className = 'toast bg-white border-0 shadow-lg';
                toastEl.setAttribute('role', 'alert');
                toastEl.setAttribute('aria-live', 'assertive');
                toastEl.setAttribute('aria-atomic', 'true');
                toastEl.innerHTML = `
                    <div class="toast-header bg-danger text-white border-0" style="background-color: var(--bsu-red) !important;">
                        <i class="bi bi-bell-fill me-2"></i>
                        <strong class="me-auto">New Reservation</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body fw-semibold text-dark">
                        ${message}
                    </div>
                `;
                container.appendChild(toastEl);
                
                const toast = new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
                toast.show();
                
                toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
            }

            // Soft-refresh the main content area via AJAX
            function refreshContentArea() {
                const contentArea = document.querySelector('.content-area');
                if (!contentArea) return;

                fetch(window.location.href)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Replace main content
                        const newContentArea = doc.querySelector('.content-area');
                        if (newContentArea) {
                            // Clear and append to ensure scripts are NOT ignored if we manually handle them,
                            // OR just replace innerHTML and then find/run scripts.
                            contentArea.innerHTML = newContentArea.innerHTML;
                            
                            // Re-execute scripts inside the new content area
                            const scripts = newContentArea.querySelectorAll('script');
                            scripts.forEach(oldScript => {
                                const newScript = document.createElement('script');
                                Array.from(oldScript.attributes).forEach(attr => {
                                    newScript.setAttribute(attr.name, attr.value);
                                });
                                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                contentArea.appendChild(newScript);
                                // Remove the dummy script tag we just added to keep DOM clean
                                newScript.parentNode.removeChild(newScript);
                            });
                        }

                        // Also update sidebar badge counters
                        const currentBadges = document.querySelectorAll('.badge-count');
                        const newBadges = doc.querySelectorAll('.badge-count');
                        newBadges.forEach((newBadge, index) => {
                            const currentBadge = currentBadges[index];
                            if (currentBadge) {
                                currentBadge.innerHTML = newBadge.innerHTML;
                                // Handle visibility
                                const count = parseInt(newBadge.innerHTML) || 0;
                                if (count > 0) {
                                    currentBadge.classList.remove('d-none');
                                } else {
                                    currentBadge.classList.add('d-none');
                                }
                            }
                        });

                        // Notify page that a refresh occurred
                        window.dispatchEvent(new CustomEvent('adminRefreshComplete'));
                    })
                    .catch(err => console.error('Failed to refresh content area:', err));
            }

            // Polling routine
            function checkNewReservations() {
                const url = `<?= BASE_URL ?>admin/ajax/check_new_reservations.php?last_fac_id=${lastFacId}&last_guest_id=${lastGuestId}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) return;

                        if (!isFirstCheck && data.has_new) {
                            playNotificationSound();
                            
                            let msg = "A new reservation has been submitted.";
                            if (data.new_fac_count > 0 && data.new_guest_count > 0) {
                                msg = `${data.new_fac_count} new Function Room and ${data.new_guest_count} new Guest Room reservations received.`;
                            } else if (data.new_fac_count > 0) {
                                msg = `${data.new_fac_count} new Function Room reservation(s) received.`;
                            } else if (data.new_guest_count > 0) {
                                msg = `${data.new_guest_count} new Guest Room reservation(s) received.`;
                            }
                            showToast(msg);
                            
                            // Refresh interface
                            refreshContentArea();
                        }

                        // Update trackers
                        if (data.current_max_fac > lastFacId) lastFacId = data.current_max_fac;
                        if (data.current_max_guest > lastGuestId) lastGuestId = data.current_max_guest;
                        
                        isFirstCheck = false;
                    })
                    .catch(err => console.error("Polling error:", err));
            }

            // Start polling every 5 seconds
            setInterval(checkNewReservations, 5000);
            checkNewReservations(); // initial call
        })();

        // Mobile menu toggle (already in header, but keeping for compatibility)
        document.addEventListener('DOMContentLoaded', function() {
            // Any additional footer-specific scripts
        });
    </script>
</body>
</html>