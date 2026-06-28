<?php
require_once __DIR__ . '/../../path.php';
require_once CONFIG . '/auth.php';
require_once CONFIG . '/db.php';
require_once ADMIN_PATH . '/includes/header.php';

// ============================================================
// PAGE SETUP
// ============================================================
$page_title = 'Inquiries';
$page_route = 'inquiries';

// ============================================================
// HANDLE ACTIONS
// ============================================================

// Mark as confirmed
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $booking_id = (int)$_POST['booking_id'];

    if ($action === 'confirm') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed', replied_at = NOW() WHERE id = ?");
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
    } elseif ($action === 'pending') {
        $stmt = $conn->prepare("UPDATE bookings SET status = 'pending' WHERE id = ?");
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->bind_param('i', $booking_id);
        $stmt->execute();
    }
}

// ============================================================
// FILTERS & SEARCH
// ============================================================
$status_filter = $_GET['status'] ?? '';
$search_query = $_GET['search'] ?? '';
$page = (int)($_GET['page'] ?? 1);
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Build WHERE clause
$where = "1=1";
if ($status_filter && in_array($status_filter, ['pending', 'confirmed', 'rejected'])) {
    $where .= " AND status = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
}
if ($search_query) {
    $search = mysqli_real_escape_string($conn, $search_query);
    $where .= " AND (name LIKE '%$search%' OR email LIKE '%$search%' OR artist LIKE '%$search%' OR service LIKE '%$search%')";
}

// ============================================================
// FETCH INQUIRIES
// ============================================================
$sql = "SELECT * FROM bookings WHERE $where ORDER BY created_at DESC LIMIT $offset, $per_page";
$result = mysqli_query($conn, $sql);
$inquiries = [];
while ($row = mysqli_fetch_assoc($result)) {
    $inquiries[] = $row;
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM bookings WHERE $where";
$count_result = mysqli_query($conn, $count_sql);
$total = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total / $per_page);

// Status badge colors
$status_colors = [
    'pending' => 'bg-yellow-900 text-yellow-100',
    'confirmed' => 'bg-green-900 text-green-100',
    'rejected' => 'bg-red-900 text-red-100'
];
?>

<!-- ─────────── FILTERS ─────────── -->
<div class="flex flex-col md:flex-row gap-4 mb-6">
    <form method="GET" class="flex flex-col md:flex-row gap-4 flex-1">
        <!-- Search -->
        <div class="flex-1">
            <input type="text" name="search" placeholder="Search by name, email, artist..." 
                value="<?= htmlspecialchars($search_query) ?>" class="field w-full" />
        </div>

        <!-- Status filter -->
        <select name="status" class="field bg-ink" onchange="this.form.submit()">
            <option value="">All statuses</option>
            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="confirmed" <?= $status_filter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
            <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>

        <!-- Submit -->
        <button type="submit" class="btn-primary">Search</button>
        
        <!-- Clear -->
        <a href="<?= APP_URL ?>admin/inquiries/" class="btn-secondary">Clear</a>
    </form>
</div>

<!-- ─────────── STATS ─────────── -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="card p-4">
        <div class="eyebrow text-cream-3 mb-2">Total inquiries</div>
        <div class="display text-3xl"><?= $total ?></div>
    </div>
    <div class="card p-4">
        <div class="eyebrow text-cream-3 mb-2">Pending</div>
        <div class="display text-3xl text-yellow-400">
            <?php 
            $pending_sql = "SELECT COUNT(*) as c FROM bookings WHERE status = 'pending'";
            $pending_result = mysqli_query($conn, $pending_sql);
            echo mysqli_fetch_assoc($pending_result)['c'];
            ?>
        </div>
    </div>
    <div class="card p-4">
        <div class="eyebrow text-cream-3 mb-2">Confirmed</div>
        <div class="display text-3xl text-green-400">
            <?php 
            $confirmed_sql = "SELECT COUNT(*) as c FROM bookings WHERE status = 'confirmed'";
            $confirmed_result = mysqli_query($conn, $confirmed_sql);
            echo mysqli_fetch_assoc($confirmed_result)['c'];
            ?>
        </div>
    </div>
    <div class="card p-4">
        <div class="eyebrow text-cream-3 mb-2">Rejected</div>
        <div class="display text-3xl text-red-400">
            <?php 
            $rejected_sql = "SELECT COUNT(*) as c FROM bookings WHERE status = 'rejected'";
            $rejected_result = mysqli_query($conn, $rejected_sql);
            echo mysqli_fetch_assoc($rejected_result)['c'];
            ?>
        </div>
    </div>
</div>

<!-- ─────────── INQUIRIES TABLE ─────────── -->
<div class="card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="border-b border-ink-3 bg-ink-3/30">
                <tr>
                    <th class="px-5 py-4 text-left eyebrow">Name</th>
                    <th class="px-5 py-4 text-left eyebrow">Artist / Project</th>
                    <th class="px-5 py-4 text-left eyebrow">Service</th>
                    <th class="px-5 py-4 text-left eyebrow">Status</th>
                    <th class="px-5 py-4 text-left eyebrow">Date</th>
                    <th class="px-5 py-4 text-left eyebrow">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-ink-3">
                <?php if (empty($inquiries)): ?>
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-cream-3">
                            No inquiries found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr class="hover:bg-ink-3/20 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-medium text-cream"><?= htmlspecialchars($inquiry['name']) ?></div>
                                <div class="text-xs text-cream-3"><?= htmlspecialchars($inquiry['email']) ?></div>
                            </td>
                            <td class="px-5 py-4 text-sm text-cream-2">
                                <?= htmlspecialchars($inquiry['artist'] ?? '—') ?>
                            </td>
                            <td class="px-5 py-4 text-sm text-cream-2">
                                <?= htmlspecialchars($inquiry['service']) ?>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-block px-2 py-1 rounded text-xs font-medium <?= $status_colors[$inquiry['status']] ?>">
                                    <?= ucfirst($inquiry['status']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-sm text-cream-3">
                                <?= date('M d, H:i', strtotime($inquiry['created_at'])) ?>
                            </td>
                            <td class="px-5 py-4">
                                <button class="btn-link text-gold hover:text-cream" 
                                    onclick="viewInquiry(<?= $inquiry['id'] ?>)">
                                    View →
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ─────────── PAGINATION ─────────── -->
<?php if ($total_pages > 1): ?>
    <div class="flex justify-center gap-2 mt-6">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?><?= $status_filter ? '&status=' . $status_filter : '' ?><?= $search_query ? '&search=' . urlencode($search_query) : '' ?>"
                class="px-3 py-2 rounded <?= $page === $i ? 'bg-gold text-ink' : 'bg-ink-3 text-cream hover:bg-ink-3/80' ?> transition-colors">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<!-- ═════════ MODAL: VIEW INQUIRY ═════════ -->
<div id="inquiryModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-ink-2 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-ink-3">
        <!-- Header -->
        <div class="sticky top-0 bg-ink-2 border-b border-ink-3 px-6 py-4 flex items-center justify-between">
            <h2 class="display text-2xl">Inquiry Details</h2>
            <button onclick="closeModal()" class="btn-icon">
                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M5 5l10 10M15 5l-10 10" stroke="currentColor" stroke-width="1.5" />
                </svg>
            </button>
        </div>

        <!-- Content -->
        <div class="p-6 space-y-6" id="modalContent">
            <!-- Loaded via JavaScript -->
        </div>
    </div>
</div>

<script>
async function viewInquiry(id) {
    try {
        const response = await fetch('<?= APP_URL ?>admin/api/get-inquiry.php?id=' + id);
        const data = await response.json();

        if (!data.success) {
            alert('Error loading inquiry');
            return;
        }

        const inquiry = data.inquiry;
        const statusColors = {
            pending: 'bg-yellow-900 text-yellow-100',
            confirmed: 'bg-green-900 text-green-100',
            rejected: 'bg-red-900 text-red-100'
        };

        let html = `
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <div class="eyebrow text-cream-3 mb-1">Name</div>
                        <div class="text-cream">${inquiry.name}</div>
                    </div>
                    <div>
                        <div class="eyebrow text-cream-3 mb-1">Email</div>
                        <a href="mailto:${inquiry.email}" class="text-gold hover:text-cream">${inquiry.email}</a>
                    </div>
                    <div>
                        <div class="eyebrow text-cream-3 mb-1">Artist / Project</div>
                        <div class="text-cream">${inquiry.artist || '—'}</div>
                    </div>
                    <div>
                        <div class="eyebrow text-cream-3 mb-1">Service</div>
                        <div class="text-cream">${inquiry.service}</div>
                    </div>
                    <div>
                        <div class="eyebrow text-cream-3 mb-1">Timeline</div>
                        <div class="text-cream">${inquiry.timeline || '—'}</div>
                    </div>
                    <div>
                        <div class="eyebrow text-cream-3 mb-1">Status</div>
                        <span class="inline-block px-2 py-1 rounded text-xs font-medium ${statusColors[inquiry.status]}">
                            ${inquiry.status.charAt(0).toUpperCase() + inquiry.status.slice(1)}
                        </span>
                    </div>
                </div>

                <div>
                    <div class="eyebrow text-cream-3 mb-2">Message</div>
                    <div class="bg-ink-3 p-4 rounded text-sm text-cream-2 whitespace-pre-wrap">
                        ${inquiry.message}
                    </div>
                </div>

                <div class="text-xs text-cream-3">
                    Submitted: ${new Date(inquiry.created_at).toLocaleString()}<br>
                    ${inquiry.replied_at ? 'Replied: ' + new Date(inquiry.replied_at).toLocaleString() : 'Not replied yet'}
                </div>
            </div>

            <div class="border-t border-ink-3 pt-4 flex gap-2">
                ${inquiry.status !== 'confirmed' ? `
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="confirm">
                        <input type="hidden" name="booking_id" value="${inquiry.id}">
                        <button type="submit" class="btn-primary">Mark Confirmed</button>
                    </form>
                ` : ''}
                
                ${inquiry.status !== 'pending' ? `
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="pending">
                        <input type="hidden" name="booking_id" value="${inquiry.id}">
                        <button type="submit" class="btn-secondary">Mark Pending</button>
                    </form>
                ` : ''}

                ${inquiry.status !== 'rejected' ? `
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="action" value="reject">
                        <input type="hidden" name="booking_id" value="${inquiry.id}">
                        <button type="submit" class="btn-secondary">Reject</button>
                    </form>
                ` : ''}

                <form method="POST" style="display:inline; margin-left:auto;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="booking_id" value="${inquiry.id}">
                    <button type="submit" class="btn-secondary text-red-400" onclick="return confirm('Delete this inquiry?')">Delete</button>
                </form>
            </div>
        `;

        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('inquiryModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error:', error);
        alert('Error loading inquiry');
    }
}

function closeModal() {
    document.getElementById('inquiryModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('inquiryModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>

<?php require_once ADMIN_PATH . '/includes/footer.php'; ?>