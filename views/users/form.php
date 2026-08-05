<?php
/** User create/edit form. Variables: $editing (array|null), $errors, $old */
$isEdit = $editing !== null;
$errors = $errors ?? [];
$old = $old ?? [];
$vals = function (string $key) use ($editing, $old): string {
    if (isset($old[$key]) && $old[$key] !== '') {
        return $old[$key];
    }
    return $editing[$key] ?? '';
};
$field = fn(string $key): string => isset($errors[$key]) ? ' is-invalid' : '';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h4 class="mb-1"><i class="fa-solid <?= $isEdit ? 'fa-pen' : 'fa-user-plus' ?> me-2 text-primary"></i><?= $isEdit ? 'Edit User' : 'Create New User' ?></h4>
        <span class="text-muted small"><?= $isEdit ? 'Update the profile of ' . e($editing['full_name']) : 'Register a new administrator or staff account' ?></span>
    </div>
    <a href="<?= url('users') ?>" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i>Back</a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card dashboard-card">
            <div class="card-header bg-transparent"><i class="fa-solid fa-id-card me-2 text-primary"></i>Account Details</div>
            <div class="card-body">
                <form method="post" action="<?= url($isEdit ? 'users/update' : 'users/store') ?>" novalidate>
                    <?= csrf_field() ?>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= (int) $editing['id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control<?= $field('full_name') ?>" name="full_name" value="<?= e($vals('full_name')) ?>" required>
                        <?php if (!empty($errors['full_name'])): ?><div class="invalid-feedback d-block"><?= e($errors['full_name']) ?></div><?php endif; ?>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control<?= $field('username') ?>" name="username" value="<?= e($vals('username')) ?>" required>
                            <?php if (!empty($errors['username'])): ?><div class="invalid-feedback d-block"><?= e($errors['username']) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone <span class="text-muted">(optional)</span></label>
                            <input type="text" class="form-control" name="phone" value="<?= e($vals('phone')) ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control<?= $field('email') ?>" name="email" value="<?= e($vals('email')) ?>" required>
                        <?php if (!empty($errors['email'])): ?><div class="invalid-feedback d-block"><?= e($errors['email']) ?></div><?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?= $isEdit ? 'New Password' : 'Password' ?> <span class="text-muted"><?= $isEdit ? '(leave blank to keep current)' : '' ?></span></label>
                        <input type="password" class="form-control<?= $field('password') ?>" name="password" <?= $isEdit ? '' : 'required' ?> placeholder="<?= $isEdit ? 'Min 8 chars, letters + numbers' : 'Min 8 chars, letters + numbers' ?>">
                        <?php if (!empty($errors['password'])): ?><div class="invalid-feedback d-block"><?= e($errors['password']) ?></div><?php endif; ?>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select class="form-select<?= $field('role') ?>" name="role">
                                <option value="staff" <?= ($editing['role'] ?? '') === 'staff' || $vals('role') === 'staff' ? 'selected' : '' ?>>Business Staff</option>
                                <option value="admin" <?= ($editing['role'] ?? '') === 'admin' || $vals('role') === 'admin' ? 'selected' : '' ?>>Administrator</option>
                            </select>
                            <?php if (!empty($errors['role'])): ?><div class="invalid-feedback d-block"><?= e($errors['role']) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select<?= $field('status') ?>" name="status">
                                <option value="approved" <?= ($editing['status'] ?? '') === 'approved' || $vals('status') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="pending" <?= ($editing['status'] ?? '') === 'pending' || $vals('status') === 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                                <option value="rejected" <?= ($editing['status'] ?? '') === 'rejected' || $vals('status') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                <option value="inactive" <?= ($editing['status'] ?? '') === 'inactive' || $vals('status') === 'inactive' ? 'selected' : '' ?>>Inactive (Suspended)</option>
                            </select>
                            <div class="form-text">Only <strong>approved</strong> users can log in.</div>
                            <?php if (!empty($errors['status'])): ?><div class="invalid-feedback d-block"><?= e($errors['status']) ?></div><?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i><?= $isEdit ? 'Save Changes' : 'Create User' ?>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card dashboard-card">
            <div class="card-header bg-transparent"><i class="fa-solid fa-shield-halved me-2 text-info"></i>Role Permissions</div>
            <div class="card-body">
                <div class="permission-block">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?= role_badge('admin') ?>
                        <i class="fa-solid fa-arrow-right text-muted small"></i>
                    </div>
                    <ul class="permission-list">
                        <li><i class="fa-solid fa-check text-success"></i>Verify transactions</li>
                        <li><i class="fa-solid fa-check text-success"></i>Search & view all records</li>
                        <li><i class="fa-solid fa-check text-success"></i>Record & verify transactions</li>
                        <li><i class="fa-solid fa-check text-success"></i>Generate & export reports</li>
                        <li><i class="fa-solid fa-check text-success"></i>Approve / reject / suspend users</li>
                    </ul>
                </div>
                <div class="permission-block">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <?= role_badge('staff') ?>
                        <i class="fa-solid fa-arrow-right text-muted small"></i>
                    </div>
                    <ul class="permission-list">
                        <li><i class="fa-solid fa-check text-success"></i>Search & view all records</li>
                        <li><i class="fa-solid fa-check text-success"></i>Print receipts</li>
                        <li><i class="fa-solid fa-xmark text-danger"></i>Record / verify transactions</li>
                        <li><i class="fa-solid fa-xmark text-danger"></i>Reports & user management</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
