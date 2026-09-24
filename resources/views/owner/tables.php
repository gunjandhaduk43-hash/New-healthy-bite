<!-- Header Section -->
<div class="page-header-row">
    <div>
        <h1 class="page-greeting">Tables & QR Codes</h1>
        <p class="page-subtitle"><?= e($restaurant['name'] ?? 'Greenhouse Kitchen') ?> · <?= e(!empty($branches[0]['name']) ? $branches[0]['name'] : 'Bengaluru Branch') ?></p>
    </div>
    <div class="header-action-group">
        <button type="button" onclick="openHbModal('addTableModal')" class="btn-hb btn-hb-primary">
            <i class="bi bi-plus-lg"></i> Add Table / QR
        </button>
        <button type="button" onclick="downloadAllQrCodes()" class="btn-hb btn-hb-secondary">
            <i class="bi bi-download"></i> Download All
        </button>
        <button type="button" onclick="printAllQrCodes()" class="btn-hb btn-hb-secondary">
            <i class="bi bi-printer"></i> Print All
        </button>
    </div>
</div>

<!-- 4-Column Tables Grid -->
<div class="hb-grid-4">
    <?php if (empty($tables)): ?>
        <div class="table-container-card" style="grid-column: 1 / -1; padding: 48px; text-align: center; color: #64748b;">
            <i class="bi bi-qr-code" style="font-size: 40px; color: #94a3b8; margin-bottom: 12px; display: block;"></i>
            <h3 style="font-size: 16px; font-weight: 700; color: #0f172a; margin-bottom: 4px;">No tables found</h3>
            <p style="font-size: 13px; margin: 0;">Click "Add Table / QR" to create tables and set up contactless ordering.</p>
        </div>
    <?php else: ?>
        <?php foreach ($tables as $table): ?>
            <?php
                $tableNum = (string)$table['table_number'];
                $displayLabel = str_starts_with($tableNum, 'Table') ? $tableNum : ('Table ' . $tableNum);
                $isOccupied = ($table['status'] ?? 'available') === 'occupied';
                $qrTargetUrl = full_url('/menu?token=' . ($table['qr_token'] ?? ''));
                $tableId = (int)$table['id'];
            ?>
            <div class="table-qr-card" id="table-card-<?= $tableId ?>" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:16px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.03); display:flex; flex-direction:column; justify-content:space-between;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                    <span style="font-size:16px; font-weight:800; color:#0f172a;"><?= e($displayLabel) ?></span>
                    <button type="button" 
                            id="table-status-pill-<?= $tableId ?>" 
                            onclick="toggleTableOccupancy(<?= $tableId ?>)" 
                            class="status-pill <?= $isOccupied ? 'occupied' : 'available' ?>" 
                            style="border:none; cursor:pointer; font-weight:700; font-size:11px; padding:4px 10px; transition:transform 0.1s;" 
                            title="Click to toggle Available / Occupied">
                        <?= $isOccupied ? 'Occupied' : 'Available' ?>
                    </button>
                </div>

                <!-- Real Scannable QR Code Render Box -->
                <div class="qr-preview-box" style="background:#f8fafc; border:1px solid #f1f5f9; border-radius:12px; height:150px; display:flex; align-items:center; justify-content:center; margin-bottom:14px;">
                    <div id="qrcode-box-<?= $tableId ?>" class="real-qr-target" data-table="<?= e($displayLabel) ?>" data-url="<?= e($qrTargetUrl) ?>"></div>
                </div>

                <div style="font-size:11px; color:#64748b; text-align:center; margin-bottom:12px; word-break:break-all;">
                    Token: <span style="font-family:monospace;"><?= substr(e($table['qr_token'] ?? ''), 0, 16) ?>...</span>
                </div>

                <div style="display:flex; gap:8px;">
                    <button type="button" onclick="downloadRealQr(<?= $tableId ?>, '<?= e($displayLabel) ?>')" class="btn-hb btn-hb-secondary btn-hb-sm" style="flex:1;">
                        <i class="bi bi-download"></i> Save
                    </button>
                    <button type="button" onclick="printRealQr(<?= $tableId ?>, '<?= e($displayLabel) ?>')" class="btn-hb btn-hb-outline-green btn-hb-sm" style="flex:1;">
                        <i class="bi bi-printer"></i> Tent Card
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal: Generate / Add Table -->
<div class="hb-modal-overlay hidden" id="addTableModal">
    <div class="hb-modal-dialog" style="max-width: 480px;">
        <div class="hb-modal-header">
            <h2 class="hb-modal-title">Add Table QR Code</h2>
            <button type="button" class="hb-modal-close" onclick="closeHbModal('addTableModal')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form action="<?= url('/owner/tables/create') ?>" method="POST">
            <?= \App\Core\Csrf::field() ?>
            <div style="display:flex; flex-direction:column; gap:16px;">
                <div class="form-field">
                    <label class="form-label">Branch Location *</label>
                    <select name="branch_id" class="form-control-hb" required>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= (int)$branch['id'] ?>" <?= ((int)($selectedBranchId ?? 0) === (int)$branch['id']) ? 'selected' : '' ?>>
                                <?= e($branch['name']) ?> (<?= e($branch['city'] ?? 'Bengaluru') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-field">
                    <label class="form-label">Table Number / Label *</label>
                    <input type="text" name="table_number" class="form-control-hb" placeholder="e.g. 13, 14 or Patio-1" required>
                    <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                        A unique cryptographic QR token and menu link will be automatically provisioned.
                    </small>
                </div>
            </div>
            <div class="hb-modal-actions">
                <button type="button" class="btn-hb btn-hb-secondary" onclick="closeHbModal('addTableModal')">Cancel</button>
                <button type="submit" class="btn-hb btn-hb-primary">Generate QR Code</button>
            </div>
        </form>
    </div>
</div>

<!-- Load Standalone QRCode.js Library -->
<script src="<?= asset('js/qrcode.min.js') ?>"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate real scannable QR codes for each table
    const targets = document.querySelectorAll('.real-qr-target');
    targets.forEach(el => {
        const url = el.getAttribute('data-url');
        if (url && typeof QRCode !== 'undefined') {
            new QRCode(el, {
                text: url,
                width: 110,
                height: 110,
                colorDark: "#0f172a",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.M
            });
        }
    });
});

function downloadRealQr(tableId, tableLabel) {
    const container = document.getElementById('qrcode-box-' + tableId);
    if (!container) return;
    const canvas = container.querySelector('canvas');
    const img = container.querySelector('img');
    
    let dataUrl = '';
    if (canvas) {
        dataUrl = canvas.toDataURL('image/png');
    } else if (img && img.src) {
        dataUrl = img.src;
    }

    if (dataUrl) {
        const a = document.createElement('a');
        a.href = dataUrl;
        a.download = tableLabel.replace(/\s+/g, '-') + '-QR.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    } else {
        alert('QR code is generating. Please wait a moment.');
    }
}

function printRealQr(tableId, tableLabel) {
    const container = document.getElementById('qrcode-box-' + tableId);
    if (!container) return;
    const canvas = container.querySelector('canvas');
    const img = container.querySelector('img');
    
    let qrSrc = '';
    if (canvas) {
        qrSrc = canvas.toDataURL('image/png');
    } else if (img && img.src) {
        qrSrc = img.src;
    }

    const printWin = window.open('', '_blank', 'width=450,height=580');
    if (!printWin) return;
    printWin.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Healthy Bite — \${tableLabel} QR</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; text-align: center; padding: 40px; margin: 0; background: #FFF; }
                .ticket { border: 2px solid #166534; border-radius: 20px; padding: 36px 28px; max-width: 320px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
                .logo { font-size: 22px; font-weight: 800; color: #166534; letter-spacing: 0.5px; text-transform: uppercase; margin: 0 0 4px 0; }
                .tagline { font-size: 11px; color: #64748b; margin: 0 0 20px 0; font-weight: 500; }
                .table-num { font-size: 26px; font-weight: 800; color: #0f172a; margin: 0 0 20px 0; }
                .qr-frame { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 14px; padding: 18px; display: inline-block; margin-bottom: 20px; }
                .qr-frame img { width: 160px; height: 160px; display: block; }
                .instruct { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0; }
                .sub { font-size: 11px; color: #64748b; margin: 0; line-height: 1.4; }
            </style>
        </head>
        <body>
            <div class="ticket">
                <div class="logo">HEALTHY BITE</div>
                <div class="tagline">Good Food. Better You.</div>
                <div class="table-num">\${tableLabel}</div>
                <div class="qr-frame">
                    <img src="\${qrSrc}" alt="QR Code">
                </div>
                <div class="instruct">Scan to View Menu & Order</div>
                <div class="sub">Track live macros, nutrition details, and order directly from your table.</div>
            </div>
            <script>window.onload = function() { window.print(); }<\/script>
        </body>
        </html>
    `);
    printWin.document.close();
}

function printAllQrCodes() {
    window.print();
}

function downloadAllQrCodes() {
    const targets = document.querySelectorAll('.real-qr-target');
    let delay = 0;
    targets.forEach(el => {
        const tableLabel = el.getAttribute('data-table') || 'Table';
        const canvas = el.querySelector('canvas');
        const img = el.querySelector('img');
        let src = '';
        if (canvas) src = canvas.toDataURL('image/png');
        else if (img && img.src) src = img.src;

        if (src) {
            setTimeout(() => {
                const a = document.createElement('a');
                a.href = src;
                a.download = tableLabel.replace(/\s+/g, '-') + '-QR.png';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }, delay);
            delay += 250;
        }
    });
}

function toggleTableOccupancy(tableId) {
    const pill = document.getElementById('table-status-pill-' + tableId);
    if (!pill) return;
    const isCurrentlyOccupied = pill.textContent.trim().toLowerCase() === 'occupied';
    const nextStatus = isCurrentlyOccupied ? 'available' : 'occupied';

    pill.textContent = 'Updating...';

    fetch(`<?= url('/owner/tables/') ?>${tableId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: nextStatus })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (nextStatus === 'occupied') {
                pill.className = 'status-pill occupied';
                pill.textContent = 'Occupied';
            } else {
                pill.className = 'status-pill available';
                pill.textContent = 'Available';
            }
        } else {
            pill.textContent = isCurrentlyOccupied ? 'Occupied' : 'Available';
        }
    })
    .catch(err => {
        pill.textContent = isCurrentlyOccupied ? 'Occupied' : 'Available';
    });
}
</script>
