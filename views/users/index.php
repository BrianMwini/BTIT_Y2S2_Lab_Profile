<?php
/**
 * User management view (SRS 4.2.7) — admin only.
 * Includes the staff approval workflow (pending registrations) plus
 * approve / reject / suspend / activate actions. Variables: $users,
 * $pending, $search, $user.
 */
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="mb-1"><i class="fa-solid fa-users-gear me-2 text-primary"></i>User Management</h4>
        <span class="text-muted small">Approve new staff, manage roles and account statuses</span>
    </div>
    <a href="<?= url('users/create') ?>" class="btn btn-primary"><i class="fa-solid fa-user-plus me-2"></i>New User</a>
</div>

<?php if (!empty($pending)): ?>
    <!-- ============ PENDING APPROVALS ============ -->
    <div class="card dashboard-card mb-4 border-warning-subtle">
        <div class="card-header bg-transparent d-flex align-items-center gap-2">
            <i class="fa-solid fa-user-clock text-warning"></i>
            <span>Pending Approvals</span>
            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle">
                <?= count($pending) ?> awaiting review
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 dashboard-table">
                <thead>
                    <tr>
                        <th>Applicant</th>
                        <th>Contact</th>
                        <th>Registered</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="user-avatar-sm"><?= e(strtoupper(mb_substr($u['full_name'], 0, 1))) ?></span>
                                    <div>
                                        <span class="fw-medium d-block"><?= e($u['full_name']) ?></span>
                                        <span class="text-muted small">@<?= e($u['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block small"><?= e($u['email']) ?></span>
                                <span class="text-muted small"><?= e($u['phone'] ?? '—') ?></span>
                            </td>
                            <td class="text-muted small"><?= e(format_datetime($u['created_at'], 'd M Y')) ?></td>
                            <td><?= user_status_badge($u['status']) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <form method="post" action="<?= url('users/status') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="btn btn-sm btn-success" title="Approve this user">
                                            <i class="fa-solid fa-user-check me-1"></i>Approve
                                        </button>
                                    </form>
                                    <form method="post" action="<?= url('users/status') ?>" class="d-inline"
                                          data-confirm="Reject this application? The user will not be able to log in.">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject this user">
                                            <i class="fa-solid fa-ban me-1"></i>Reject
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<div class="card dashboard-card">
    <div class="card-header bg-transparent d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span><i class="fa-solid fa-users me-2 text-success"></i>User Accounts</span>
        <span class="badge text-bg-light border"><?= count($users) ?> user<?= count($users) === 1 ? '' : 's' ?></span>
    </div>
    <div class="card-body border-bottom">
        <form method="get" action="<?= url('users') ?>" class="row g-2 align-items-end">
            <div class="col-md-8">
                <label class="form-label small text-muted mb-1">Search users</label>
                <input type="text" class="form-control" name="q" value="<?= e($search) ?>" placeholder="Search by name, username, email or phone…">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary w-100"><i class="fa-solid fa-magnifying-glass me-2"></i>Search</button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 dashboard-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Contact</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6">
                        <div class="empty-state p-5">
                            <i class="fa-regular fa-users text-muted"></i>
                            <h6 class="mt-3 mb-1">No users found</h6>
                            <p class="text-muted small mb-0">Try a different search term.</p>
                        </div>
                    </td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="user-avatar-sm"><?= e(strtoupper(mb_substr($u['full_name'], 0, 1))) ?></span>
                                    <div>
                                        <span class="fw-medium d-block"><?= e($u['full_name']) ?></span>
                                        <span class="text-muted small">@<?= e($u['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block small"><?= e($u['email']) ?></span>
                                <span class="text-muted small"><?= e($u['phone'] ?? '—') ?></span>
                            </td>
                            <td><?= role_badge($u['role']) ?></td>
                            <td><?= user_status_badge($u['status']) ?></td>
                            <td class="text-muted small"><?= e(format_datetime($u['last_login_at'])) ?></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-1">
                                    <a href="<?= url('users/edit/' . $u['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Edit user">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <?php if ((int) $u['id'] !== (int) $user['id']): ?>
                                        <?php if ($u['status'] === 'pending'): ?>
                                            <form method="post" action="<?= url('users/status') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-success" title="Approve user">
                                                    <i class="fa-solid fa-user-check"></i>
                                                </button>
                                            </form>
                                            <form method="post" action="<?= url('users/status') ?>" class="d-inline" data-confirm="Reject this application?">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Reject user">
                                                    <i class="fa-solid fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($u['status'] === 'approved'): ?>
                                            <form method="post" action="<?= url('users/status') ?>" class="d-inline" data-confirm="Suspend this user? They will no longer be able to log in.">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                                <input type="hidden" name="status" value="inactive">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Suspend user">
                                                    <i class="fa-solid fa-user-slash"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="<?= url('users/status') ?>" class="d-inline" data-confirm="Activate this user? They will be able to log in again.">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-sm btn-outline-success" title="Activate user">
                                                    <i class="fa-solid fa-user-check"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
