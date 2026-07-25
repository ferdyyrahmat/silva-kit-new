<!-- Vendor -->
{{-- <script src="assets/libs/jquery/jquery.min.js"></script>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
<script src="assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script> --}}
<style>
    .swal2-toast-modern {
        width: min(380px, calc(100vw - 2rem)) !important;
        min-height: 84px;
        padding: 0 !important;
        border-radius: 16px !important;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.16) !important;
        border: 1px solid rgba(148, 163, 184, 0.22) !important;
        overflow: hidden;
        background: #ffffff !important;
    }

    .swal2-toast-modern .swal2-title {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        margin: 0 0 0.2rem 0 !important;
    }

    .swal2-toast-modern .swal2-html-container {
        font-size: 0.84rem !important;
        color: #475569 !important;
        line-height: 1.45 !important;
        margin: 0 !important;
    }

    .swal2-toast-modern .swal2-icon {
        margin: 0 !important;
        width: 54px !important;
        height: 100% !important;
        border-radius: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #ffffff !important;
    }

    .swal2-toast-modern .swal2-icon-content {
        font-size: 1.15rem !important;
    }

    .swal2-toast-modern .swal2-actions {
        margin: 0.4rem 0 0 0 !important;
    }

    .swal2-toast-modern .swal2-popup {
        padding: 0 !important;
    }

    .swal2-toast-show {
        animation: toast-slide-in 0.25s ease-out;
    }

    .swal2-toast-hide {
        animation: toast-slide-out 0.2s ease-in forwards;
    }

    @keyframes toast-slide-in {
        from {
            opacity: 0;
            transform: translateY(-10px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes toast-slide-out {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(-10px) scale(0.98);
        }
    }
</style>
<script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    window.showRealtimeToast = function(options) {
        if (typeof Swal === 'undefined') {
            return;
        }

        const iconKey = (options.icon || 'info').toLowerCase();
        const palette = {
            success: { bg: 'linear-gradient(135deg, #10b981 0%, #059669 100%)', color: '#ffffff' },
            info: { bg: 'linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)', color: '#ffffff' },
            warning: { bg: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)', color: '#ffffff' },
            error: { bg: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)', color: '#ffffff' },
            question: { bg: 'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)', color: '#ffffff' }
        };

        const paletteStyle = palette[iconKey] || palette.info;
        const text = options.text || '';
        const title = options.title || 'Notification';

        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: iconKey,
            title: title,
            text: text,
            showConfirmButton: false,
            timer: options.timer || 4500,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#0f172a',
            iconColor: paletteStyle.color,
            width: 'min(380px, calc(100vw - 2rem))',
            customClass: {
                popup: 'swal2-toast-modern',
                title: 'swal2-toast-title',
                htmlContainer: 'swal2-toast-text',
                icon: 'swal2-toast-icon'
            },
            showClass: {
                popup: 'swal2-toast-show'
            },
            hideClass: {
                popup: 'swal2-toast-hide'
            },
            didOpen: (toast) => {
                const iconEl = toast.querySelector('.swal2-icon');
                if (iconEl) {
                    iconEl.style.background = paletteStyle.bg;
                    iconEl.style.border = '0';
                    iconEl.style.width = '56px';
                    iconEl.style.height = '100%';
                    iconEl.style.margin = '0';
                    iconEl.style.borderRadius = '0';
                    iconEl.style.display = 'flex';
                    iconEl.style.alignItems = 'center';
                    iconEl.style.justifyContent = 'center';
                }

                const popup = toast.closest('.swal2-container')?.querySelector('.swal2-popup');
                if (popup) {
                    popup.style.display = 'flex';
                    popup.style.alignItems = 'stretch';
                    popup.style.minHeight = '84px';
                }
            }
        });
    };
    window.PUSHER_CONFIG = {
        enabled: {{ \App\Models\SystemSetting::getByKey('websocket_enabled', false) ? 'true' : 'false' }},
        key: "{{ e(\App\Models\SystemSetting::getByKey('pusher_app_key', '')) }}",
        cluster: "{{ e(\App\Models\SystemSetting::getByKey('pusher_app_cluster', 'ap1')) }}",
        userId: {{ auth()->id() ?? 'null' }}
    };

    window.PUSHER_NOTIFICATION_ENABLED = false;

    if (window.PUSHER_CONFIG.enabled && window.PUSHER_CONFIG.key && window.PUSHER_CONFIG.userId) {
        try {
            var globalPusher = new Pusher(window.PUSHER_CONFIG.key, {
                cluster: window.PUSHER_CONFIG.cluster || 'ap1',
                forceTLS: true
            });

            var userChannel = globalPusher.subscribe('user-' + window.PUSHER_CONFIG.userId);
            userChannel.bind('notification-created', function(data) {
                var badge = $('#topbar-bell-count');
                if (badge.length) {
                    var current = parseInt(badge.text() || 0);
                    badge.text(current + 1).removeClass('d-none');
                }

                if (typeof window.fetchNotifications === 'function') {
                    window.fetchNotifications();
                }

                if (typeof window.showRealtimeToast === 'function') {
                    window.showRealtimeToast({
                        icon: 'info',
                        title: data.title || 'New notification',
                        text: data.message || 'You have a new update.'
                    });
                }
            });

            window.PUSHER_NOTIFICATION_ENABLED = true;
        } catch (e) {
            console.error("Global Pusher error:", e);
            window.PUSHER_NOTIFICATION_ENABLED = false;
        }
    }
</script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            xhrFields: {
                withCredentials: true
            }
        });

        $('.page-loading').fadeIn();
        setTimeout(function() {
            $('.page-loading').fadeOut();
        }, 1500); // Adjust the timeout duration as needed
    });

    function showLoading() {
        $('#page-loading').fadeIn();
    }

    function hideLoading() {
        $('#page-loading').fadeOut();
    }

    $('form').on('submit', function(e) {
        e.preventDefault();

        // Disable tombol submit setelah form disubmit
        var $form = $(this);
        $form.find('button[type="submit"]').attr('disabled', true);
        $form.find('button[type="submit"]').text('Loading...');

        var formData = new FormData(this);

        $.ajax({
            type: $form.attr('method'), // Method form POST atau GET
            url: $form.attr('action'), // URL tujuan
            data: formData, // Gunakan FormData
            processData: false, // Jangan memproses data
            contentType: false, // Jangan set content type
            beforeSend: function() {
                showLoading();
                $form.find('button[type="submit"]').attr('disabled', true);
                $form.find('button[type="submit"]').text('Loading...');
            },
            success: function(response) {
                // Proses selesai, enable kembali tombol
                $form.find('button[type="submit"]').attr('disabled', false);
                $form.find('button[type="submit"]').text('Submit');
                // console.log(response);

                // Opsional: tangani respons dari Laravel
                if (response.success) {
                    // Menampilkan SweetAlert dengan pesan sukses
                    Swal.fire({
                        title: 'Success!',
                        html: response.message,
                        icon: 'success',
                        allowOutsideClick: false, // Tidak bisa ditutup dengan klik luar
                        allowEscapeKey: false, // Tidak bisa ditutup dengan tombol escape
                        timer: 3000, // Timer 3 detik sebelum redirect
                        timerProgressBar: true, // Progress bar di bawah modal
                        didOpen: () => {
                            Swal.showLoading(); // Menampilkan loading di dalam modal
                        },
                        willClose: () => {
                            // Redirect ke halaman setelah timer selesai
                            window.location.href = response.redirect;
                        }
                    });
                }
                else {
                    // Menampilkan SweetAlert dengan pesan error
                    Swal.fire({
                        title: 'Error System',
                        text: response.message,
                        icon: 'error',
                        allowOutsideClick: false, // Tidak bisa ditutup dengan klik luar
                        allowEscapeKey: false, // Tidak bisa ditutup dengan tombol escape
                    });

                    // Enable kembali tombol submit
                    $form.find('button[type="submit"]').attr('disabled', false).text(
                        'Submit');
                }
            },
            error: function(xhr) {
                // Enable kembali tombol submit
                $form.find('button[type="submit"]').attr('disabled', false).text(
                    'Submit');

                // Tangani error validasi dari Laravel
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;

                    // Hapus pesan error sebelumnya
                    $('.invalid-feedback').remove();
                    $('.is-invalid').removeClass('is-invalid');

                    var firstErrorField; // Variabel untuk menyimpan elemen error pertama

                    // Tampilkan pesan error
                    $.each(errors, function(key, value) {
                        var inputField = $form.find(`[name="${key}"]`);
                        inputField.addClass('is-invalid');
                        inputField.after(
                            `<span class="invalid-feedback" role="alert"><strong>${value[0]}</strong></span>`
                        );

                        // Simpan elemen error pertama
                        if (!firstErrorField) {
                            firstErrorField = inputField;
                        }
                    });

                    // Scroll ke elemen error pertama
                    if (firstErrorField) {
                        $('html, body').animate({
                            scrollTop: firstErrorField.offset().top -
                                100 // Offset agar tidak terlalu menempel di atas
                        }, 'slow');
                    }
                } else {
                    alert('Terjadi kesalahan, coba lagi.');
                }
            },
            complete: function() {
                hideLoading();
                $form.find('button[type="submit"]').attr('disabled', false);
            }
        });
    });

</script>

@yield('script')
@vite(['resources/js/app.js'])
@yield('script-bottom')
