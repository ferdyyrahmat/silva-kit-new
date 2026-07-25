<!-- Global Feedback & Bug Report Ticket Modal -->
<div class="modal fade" id="global-feedback-modal" tabindex="-1" aria-labelledby="globalFeedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header bg-primary text-white py-3 rounded-top-3">
                <h5 class="modal-title fw-bold text-white fs-16 d-flex align-items-center" id="globalFeedbackModalLabel">
                    <i class="mdi mdi-comment-edit-outline me-2 fs-20"></i>Support Ticket & Bug Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="global-feedback-form" action="{{ route('v1.tickets.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted fs-13 mb-3">
                        Found a bug 🐛 or have a feature idea 💡? Submit a ticket below to track progress and get alerts!
                    </p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Category</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="category" id="cat-bug" value="bug" checked>
                                <label class="btn btn-outline-danger btn-sm w-100 py-2 fw-semibold" for="cat-bug">
                                    <i class="mdi mdi-bug-outline me-1"></i>Report Bug
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="category" id="cat-feature" value="feature_request">
                                <label class="btn btn-outline-info btn-sm w-100 py-2 fw-semibold" for="cat-feature">
                                    <i class="mdi mdi-lightbulb-on-outline me-1"></i>Feature Request
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="category" id="cat-inquiry" value="general_inquiry">
                                <label class="btn btn-outline-primary btn-sm w-100 py-2 fw-semibold" for="cat-inquiry">
                                    <i class="mdi mdi-comment-question-outline me-1"></i>General Inquiry
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="category" id="cat-server" value="server_issue">
                                <label class="btn btn-outline-warning btn-sm w-100 py-2 fw-semibold" for="cat-server">
                                    <i class="mdi mdi-server-network me-1"></i>Server Issue
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Subject / Issue Summary</label>
                        <input type="text" class="form-control" name="subject" required placeholder="e.g. Mobile profile menu button overflow">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Priority Level</label>
                        <select class="form-select" name="priority" required>
                            <option value="low">🟢 Low (Minor suggestion)</option>
                            <option value="medium" selected>🟡 Medium (Standard issue)</option>
                            <option value="high">🔴 High (Feature blocked)</option>
                            <option value="urgent">🔥 Urgent (Critical system failure)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-13">Detailed Description</label>
                        <textarea class="form-control" name="description" rows="4" required placeholder="Describe what happened or what feature you would like our developers to build..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        <i class="mdi mdi-send me-1"></i>Submit Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Floating Support Ticket Action Button -->
<div class="position-fixed bottom-0 end-0 p-3 mb-2 me-2" style="z-index: 1050;">
    <button type="button" class="btn btn-primary rounded-pill shadow-lg d-inline-flex align-items-center justify-content-center border-2 border-white btn-floating-feedback" data-bs-toggle="modal" data-bs-target="#global-feedback-modal" title="Submit Support Ticket or Bug Report">
        <i class="mdi mdi-comment-edit-outline"></i>
        <span class="fw-bold fs-13 d-none d-md-inline ms-2">Ticket & Bug</span>
    </button>
</div>

<style>
    .btn-floating-feedback {
        box-shadow: 0 8px 24px rgba(13, 110, 253, 0.4) !important;
        transition: all 0.25s ease-in-out;
        animation: pulse-glow 3s infinite ease-in-out;
        min-width: 44px;
        height: 44px;
        padding: 0 16px;
    }
    .btn-floating-feedback i {
        font-size: 20px;
    }
    @media (max-width: 767.98px) {
        .btn-floating-feedback {
            width: 48px !important;
            height: 48px !important;
            min-width: 48px !important;
            padding: 0 !important;
            border-radius: 50% !important;
        }
        .btn-floating-feedback i {
            font-size: 22px !important;
            margin: 0 !important;
        }
    }
    .btn-floating-feedback:hover {
        transform: translateY(-3px) scale(1.06);
        box-shadow: 0 12px 28px rgba(13, 110, 253, 0.55) !important;
    }
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: 0 6px 18px rgba(13, 110, 253, 0.35);
        }
        50% {
            box-shadow: 0 10px 30px rgba(13, 110, 253, 0.65);
        }
    }
</style>
