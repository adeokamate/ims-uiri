<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
requireRole('Administrator');
$pdo = db();

// Handle create permission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    if (isset($_POST['create_permission'])) {
        $name = trim($_POST['name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name) {
            $stmt = $pdo->prepare("INSERT INTO permissions (name, description) VALUES (?, ?)");
            try {
                $stmt->execute([$name, $desc]);
                setFlash('success', 'Permission created.');
            } catch (Exception $e) {
                setFlash('error', 'Could not create permission: ' . $e->getMessage());
            }
        } else {
            setFlash('error', 'Permission name is required.');
        }
    }

    if (isset($_POST['save_role_permissions'])) {
        $roleId = (int)($_POST['role_id'] ?? 0);
        $perms = $_POST['perms'] ?? [];
        // Remove existing
        $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?")->execute([$roleId]);
        // Insert selected
        $ins = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
        foreach ($perms as $pid) {
            $ins->execute([$roleId, (int)$pid]);
        }
        setFlash('success', 'Role permissions updated.');
    }

    header('Location: permissions.php'); exit;
}

$roles = $pdo->query("SELECT * FROM roles ORDER BY id")->fetchAll();
$permissions = $pdo->query("SELECT * FROM permissions ORDER BY name")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div><h1 class="page-title">Permissions</h1><p class="page-sub">Manage system permissions and assign them to roles</p></div>
    <div class="page-actions"><button class="btn btn-primary" onclick="document.getElementById('createPerm').style.display='block'">New Permission</button></div>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($flash = getFlash()): ?>
            <div class="alert <?= $flash['type']=='success'?'alert-success':'alert-error' ?>"><?= clean($flash['message']) ?></div>
        <?php endif; ?>

        <div style="display:flex;gap:20px;align-items:flex-start;">
            <div style="flex:1;min-width:340px;">
                <h3>Permissions</h3>
                <ul>
                    <?php foreach ($permissions as $p): ?>
                    <li><strong><?= clean($p['name']) ?></strong> — <?= clean($p['description']??'') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div style="flex:2;min-width:420px;">
                <h3>Role Permissions</h3>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <div class="form-group"><label>Role</label>
                        <select name="role_id" id="role_select" onchange="populate()">
                            <?php foreach ($roles as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= clean($r['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="perms_container">
                        <?php foreach ($permissions as $p): ?>
                        <div><label><input type="checkbox" name="perms[]" value="<?= $p['id'] ?>"> <?= clean($p['name']) ?> — <?= clean($p['description']??'') ?></label></div>
                        <?php endforeach; ?>
                    </div>

                    <div style="margin-top:12px;"><button type="submit" name="save_role_permissions" class="btn btn-primary">Save</button></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="createPerm" class="modal-overlay" style="display:none;">
    <div class="modal">
        <div class="modal-header"><h3>Create Permission</h3><button class="modal-close" onclick="document.getElementById('createPerm').style.display='none'">×</button></div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="create_permission" value="1">
            <div class="modal-body">
                <div class="form-group"><label>Name</label><input type="text" name="name" required></div>
                <div class="form-group"><label>Description</label><input type="text" name="description"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('createPerm').style.display='none'">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
        </form>
    </div>
</div>

<script>
function populate(){
    const roleId = document.getElementById('role_select').value;
    // Fetch role permissions via AJAX (simple endpoint)
    fetch('permissions_ajax.php?role_id='+roleId).then(r=>r.json()).then(data=>{
        document.querySelectorAll('#perms_container input[type=checkbox]').forEach(cb=>{
            cb.checked = data.includes(parseInt(cb.value));
        });
    });
}
// populate on load
populate();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
