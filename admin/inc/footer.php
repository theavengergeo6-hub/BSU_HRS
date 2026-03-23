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

        // Mobile menu toggle (already in header, but keeping for compatibility)
        document.addEventListener('DOMContentLoaded', function() {
            // Any additional footer-specific scripts
        });
    </script>
</body>
</html>