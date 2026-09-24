<!-- Header Section -->
<div class="page-header-row">
    <div>
        <h1 class="page-greeting">Customer Reviews & Feedback</h1>
        <p class="page-subtitle">Listen to real guest experiences, reply to ratings, and maintain dining satisfaction.</p>
    </div>
</div>

<!-- Top 3 Summary Cards -->
<div class="hb-grid-3" style="grid-template-columns: 1fr 1fr 1.4fr; margin-bottom: 24px;">
    <!-- Card 1: Average Rating -->
    <div class="stat-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="stat-card-title">Average Rating</span>
            <div class="stat-card-icon-box" style="background:#fef3c7; color:#b45309;">
                <i class="bi bi-star-fill"></i>
            </div>
        </div>
        <div>
            <div class="stat-card-value" style="font-size: 32px; margin: 12px 0 4px 0; color:#0f172a;">
                <?= number_format((float)($summary['avg_rating'] ?? 4.8), 1) ?> <span style="font-size:24px; color:#f59e0b;">★</span>
            </div>
            <div class="stat-card-trend" style="color: var(--brand-primary, #166534);">
                Based on <?= (int)($summary['total_reviews'] ?? 128) ?> verified customer reviews
            </div>
        </div>
    </div>

    <!-- Card 2: Total Reviews -->
    <div class="stat-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <span class="stat-card-title">Total Reviews</span>
            <div class="stat-card-icon-box" style="background:#e0f2fe; color:#0369a1;">
                <i class="bi bi-chat-quote-fill"></i>
            </div>
        </div>
        <div>
            <div class="stat-card-value" style="font-size: 32px; margin: 12px 0 4px 0; color:#0f172a;">
                <?= (int)($summary['total_reviews'] ?? 128) ?>
            </div>
            <div class="stat-card-trend" style="color: var(--brand-primary, #166534);">
                +14 new reviews this month
            </div>
        </div>
    </div>

    <!-- Card 3: Rating Distribution Bars -->
    <div class="stat-card" style="padding: 18px 22px;">
        <div style="display: flex; flex-direction: column; gap: 8px;">
            <?php 
                $counts = [
                    5 => (int)($summary['count_5'] ?? 92),
                    4 => (int)($summary['count_4'] ?? 24),
                    3 => (int)($summary['count_3'] ?? 8),
                    2 => (int)($summary['count_2'] ?? 3),
                    1 => (int)($summary['count_1'] ?? 1),
                ];
                $maxVal = max(array_values($counts)) ?: 1;
            ?>
            <?php foreach ([5, 4, 3, 2, 1] as $star): ?>
                <?php 
                    $count = $counts[$star];
                    $pct = round(($count / $maxVal) * 100);
                ?>
                <div class="rating-filter-row" onclick="filterByRating(<?= $star ?>, this)" title="Click to filter <?= $star ?>-star reviews" style="display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #334155; cursor: pointer; padding: 2px 6px; border-radius: 6px; transition: background 0.15s;">
                    <span style="width: 32px; display: flex; align-items: center; gap: 2px; color:#0f172a;">
                        <?= $star ?> <span style="color:#f59e0b;">★</span>
                    </span>
                    <div class="rating-bar-track" style="flex: 1; height: 8px; background: #E5E7EB; border-radius: 9999px; overflow: hidden;">
                        <div class="rating-bar-fill" style="height: 100%; width: <?= $pct ?>%; background: var(--brand-primary, #166534); border-radius: 9999px;"></div>
                    </div>
                    <span style="width: 28px; text-align: right; color: #64748b; font-weight: 500;">
                        <?= $count ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align: right; margin-top: 8px;">
            <button type="button" onclick="filterByRating('all', this)" style="background: none; border: none; font-size: 12px; color: var(--brand-primary, #166534); font-weight: 700; cursor: pointer; text-decoration: underline;">
                Show all star ratings
            </button>
        </div>
    </div>
</div>

<!-- Review Filter Tabs Bar -->
<div class="filter-bar-card" style="margin-bottom: 20px; gap: 10px;">
    <button type="button" class="btn-hb btn-hb-sm active review-status-tab" onclick="filterByReplyStatus('all', this)" style="border-radius:9999px; font-weight:700; background:var(--brand-primary, #166534); color:#fff;">
        All Reviews (<?= count($reviews) ?>)
    </button>
    <button type="button" class="btn-hb btn-hb-sm btn-hb-secondary review-status-tab" onclick="filterByReplyStatus('needs_reply', this)" style="border-radius:9999px;">
        <i class="bi bi-clock"></i> Awaiting Reply
    </button>
    <button type="button" class="btn-hb btn-hb-sm btn-hb-secondary review-status-tab" onclick="filterByReplyStatus('replied', this)" style="border-radius:9999px;">
        <i class="bi bi-check2-all"></i> Responded
    </button>
</div>

<!-- Customer Review Cards Stack -->
<div id="reviewsStack" style="display: flex; flex-direction: column; gap: 16px;">
    <?php if (empty($reviews)): ?>
        <div class="table-container-card" style="padding: 48px; text-align: center; color: #64748b;">
            <i class="bi bi-chat-left-text" style="font-size: 40px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">No customer reviews yet</h3>
            <p style="font-size: 13px; margin: 0;">Customer feedback submitted from the tracking page will appear here instantly.</p>
        </div>
    <?php else: ?>
        <?php foreach ($reviews as $r): ?>
            <?php 
                $rRating = (int)($r['rating'] ?? 5);
                $hasReply = !empty($r['restaurant_reply']);
            ?>
            <div class="review-item-card single-review-card" data-rating="<?= $rRating ?>" data-reply="<?= $hasReply ? 'replied' : 'needs_reply' ?>" id="review-card-<?= (int)$r['id'] ?>" style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 22px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 8px; flex-wrap:wrap; gap:8px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 15px; font-weight: 800; color: #0f172a;">
                            <?= e($r['customer_name'] ?? 'Verified Diner') ?>
                        </span>
                        <?php if ($hasReply): ?>
                            <span class="status-pill ready" style="font-size: 11px; padding: 2px 10px;">
                                <i class="bi bi-check2"></i> Responded
                            </span>
                        <?php else: ?>
                            <span class="status-pill preparing" style="font-size: 11px; padding: 2px 10px;">
                                <i class="bi bi-hourglass-split"></i> Awaiting Reply
                            </span>
                        <?php endif; ?>
                    </div>
                    <span style="font-size: 12px; color: #64748b;">
                        Order #<span style="font-family:monospace; font-weight:600;"><?= e($r['order_number'] ?? 'HB-1001') ?></span> · <?= date('d M Y, h:i A', strtotime($r['created_at'] ?? 'now')) ?>
                    </span>
                </div>

                <div style="color: #f59e0b; font-size: 15px; margin-bottom: 12px; display:flex; gap:3px;">
                    <?php 
                        for ($i = 0; $i < 5; $i++) {
                            echo ($i < $rRating) ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star" style="color:#cbd5e1;"></i>';
                        }
                    ?>
                    <span style="font-size:13px; font-weight:700; color:#0f172a; margin-left:6px;"><?= $rRating ?>.0</span>
                </div>

                <div style="font-size: 14.5px; color: #334155; line-height: 1.5; margin-bottom: 16px; background:#f8fafc; padding:12px 16px; border-radius:10px; border-left:3px solid #cbd5e1;">
                    “<?= e($r['comment'] ?? 'The food was fresh and delicious.') ?>”
                </div>

                <!-- Existing Owner Response Display -->
                <div id="reply-display-<?= (int)$r['id'] ?>" style="<?= $hasReply ? 'display:block;' : 'display:none;' ?> background: #eaf5ea; border-left: 3px solid #166534; border-radius: 10px; padding: 14px 18px; margin-bottom: 16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                        <span style="font-size:12px; font-weight:800; color:#166534; display:flex; align-items:center; gap:6px; text-transform:uppercase; letter-spacing:0.04em;">
                            <i class="bi bi-reply-fill"></i> Restaurant Owner Response
                        </span>
                        <span id="reply-time-<?= (int)$r['id'] ?>" style="font-size:11px; color:#166534; font-weight:500;">
                            <?= !empty($r['replied_at']) ? date('d M Y, h:i A', strtotime($r['replied_at'])) : 'Just now' ?>
                        </span>
                    </div>
                    <div id="reply-text-<?= (int)$r['id'] ?>" style="font-size:13.5px; color:#14532d; line-height:1.45;">
                        <?= e($r['restaurant_reply'] ?? '') ?>
                    </div>
                </div>

                <!-- Interactive Response Form with Quick Chips and Send Button -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px;">
                    <div style="display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; align-items:center;">
                        <span style="font-size: 12px; color: #64748b; font-weight:600; margin-right: 4px;">Quick templates:</span>
                        <button type="button" onclick="setQuickReply(<?= (int)$r['id'] ?>, 'Thank you so much! We take great pride in our organic ingredients and balanced nutrition.')" class="btn-hb btn-hb-sm btn-hb-secondary" style="font-size:11.5px; padding:3px 10px; border-radius:9999px;">
                            Organic ingredients pride
                        </button>
                        <button type="button" onclick="setQuickReply(<?= (int)$r['id'] ?>, 'We truly appreciate your feedback and hope to welcome you back again soon!')" class="btn-hb btn-hb-sm btn-hb-secondary" style="font-size:11.5px; padding:3px 10px; border-radius:9999px;">
                            Welcome back soon
                        </button>
                        <button type="button" onclick="setQuickReply(<?= (int)$r['id'] ?>, 'Thank you for dining with Healthy Bite! Your review inspires our whole kitchen team.')" class="btn-hb btn-hb-sm btn-hb-secondary" style="font-size:11.5px; padding:3px 10px; border-radius:9999px;">
                            Kitchen team inspiration
                        </button>
                    </div>

                    <form id="reply-form-<?= (int)$r['id'] ?>" action="<?= url('/owner/reviews/' . (int)$r['id'] . '/respond') ?>" method="POST" onsubmit="submitReviewReply(event, <?= (int)$r['id'] ?>)">
                        <?= \App\Core\Csrf::field() ?>
                        <div style="display: flex; gap: 8px;">
                            <input type="text" id="reply-input-<?= (int)$r['id'] ?>" name="reply" class="form-control-hb" 
                                   placeholder="<?= $hasReply ? 'Update your response to this guest...' : 'Write a thoughtful response to this guest...' ?>" 
                                   value=""
                                   style="flex: 1; font-size: 13.5px;">
                            <button type="submit" class="btn-hb btn-hb-primary" style="padding: 8px 20px; border-radius: 10px; font-size: 13.5px;">
                                <i class="bi bi-send"></i> <?= $hasReply ? 'Update Reply' : 'Post Reply' ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
function setQuickReply(reviewId, text) {
    const input = document.getElementById('reply-input-' + reviewId);
    if (input) {
        input.value = text;
        input.focus();
    }
}

function submitReviewReply(e, reviewId) {
    e.preventDefault();
    const form = document.getElementById('reply-form-' + reviewId);
    const input = document.getElementById('reply-input-' + reviewId);
    const replyText = input.value.trim();

    if (!replyText) {
        alert('Please enter a response message.');
        return;
    }

    const formData = new FormData(form);
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" style="width:12px; height:12px; display:inline-block; border:2px solid #fff; border-right-color:transparent; border-radius:50%; animation:hb-spin .6s linear infinite; margin-right:6px;"></span> Saving...';
    btn.disabled = true;

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        btn.innerHTML = originalText;
        btn.disabled = false;
        if (data.success) {
            const displayBox = document.getElementById('reply-display-' + reviewId);
            const textEl = document.getElementById('reply-text-' + reviewId);
            const timeEl = document.getElementById('reply-time-' + reviewId);
            if (textEl) textEl.textContent = replyText;
            if (timeEl) timeEl.textContent = 'Just now';
            if (displayBox) displayBox.style.display = 'block';

            const card = document.getElementById('review-card-' + reviewId);
            if (card) card.setAttribute('data-reply', 'replied');

            input.value = '';
            input.placeholder = 'Update your response to this guest...';

            showToast('Response saved and visible to customer!');
        } else {
            alert(data.error || 'Could not save response.');
        }
    })
    .catch(err => {
        form.submit();
    });
}

function filterByRating(star, el) {
    document.querySelectorAll('.rating-filter-row').forEach(r => r.style.background = 'transparent');
    if (el && star !== 'all') {
        el.style.background = '#eaf5ea';
    }

    const cards = document.querySelectorAll('.single-review-card');
    cards.forEach(card => {
        if (star === 'all' || card.getAttribute('data-rating') === String(star)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function filterByReplyStatus(status, btn) {
    document.querySelectorAll('.review-status-tab').forEach(b => {
        b.classList.remove('active');
        b.style.background = '';
        b.style.color = '';
    });

    if (btn) {
        btn.classList.add('active');
        btn.style.background = 'var(--brand-primary, #166534)';
        btn.style.color = '#ffffff';
    }

    const cards = document.querySelectorAll('.single-review-card');
    cards.forEach(card => {
        const replyState = card.getAttribute('data-reply');
        if (status === 'all' || replyState === status) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function showToast(message) {
    let toast = document.getElementById('hb-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'hb-toast';
        toast.style.cssText = 'position:fixed; bottom:24px; right:24px; background:#166534; color:#fff; padding:12px 20px; border-radius:10px; font-size:13px; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,0.18); z-index:999999; display:flex; align-items:center; gap:8px; transition:opacity 0.3s;';
        document.body.appendChild(toast);
    }
    toast.innerHTML = '<i class="bi bi-check2-circle"></i> ' + message;
    toast.style.opacity = '1';
    toast.style.display = 'flex';
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => { toast.style.display = 'none'; }, 300);
    }, 3500);
}
</script>
