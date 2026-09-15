<?php
session_start();
include '../../db/db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../authentication/login/login.php');
    exit();
}

$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $category_name = $_POST['category_name'];
    $description = $_POST['description'];

    if (empty($category_name)) {
        $error_msg = 'Category name is required.';
    } else {
        if ($conn->query("INSERT INTO categories (category_name, description) VALUES ('$category_name', '$description')")) {
            $success_msg = 'Category created successfully!';
        } else {
            $error_msg = 'Error creating category: Category name may already exist.';
        }
    }
}

if (isset($_GET['delete'])) {
    $cat_id = intval($_GET['delete']);
    if ($cat_id > 0) {
        $conn->query("DELETE FROM categories WHERE category_id = $cat_id");
        $success_msg = 'Category deleted successfully!';
    }
}

$result = $conn->query('SELECT * FROM categories ORDER BY category_name ASC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - NSBM EventHub</title>
    <link rel="stylesheet" href="../../components/style.css">
    <link rel="stylesheet" href="../../main.css">
    <link rel="stylesheet" href="event.css">
    <script>
        function validateCategoryForm() {
            var name = document.getElementById("category_name").value;
            if (name === "") {
                alert("Please enter a category name.");
                document.getElementById("category_name").focus();
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="page">
        <?php include '../../components/admin_navbar.php'; ?>

        <section class="page-header">
            <h1>Manage Event Categories</h1>
            <p>Add or delete categories for university events</p>
        </section>

        <main style="max-width: 1000px; margin: 0 auto 40px; padding: 0 20px;">
            <?php if (!empty($success_msg)): ?>
                <div style="background:#dcfce7; color:#166534; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                    <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_msg)): ?>
                <div style="background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:20px; text-align:center; font-weight:bold;">
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 30px; flex-wrap: wrap;">
                <!-- Form to Add Category -->
                <div style="flex: 1; min-width: 300px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <h3 style="color:#006633; margin-top:0;">Add New Category</h3>
                    <form action="categories.php" method="POST" onsubmit="return validateCategoryForm();">
                        <input type="hidden" name="action" value="create">
                        
                        <div style="margin-bottom: 15px;">
                            <label style="display:block; font-weight:bold; margin-bottom:5px;">Category Name *</label>
                            <input type="text" id="category_name" name="category_name" placeholder="e.g. Hackathon" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px;" required>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label style="display:block; font-weight:bold; margin-bottom:5px;">Description</label>
                            <textarea name="description" placeholder="Brief description..." style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; height:80px;"></textarea>
                        </div>

                        <button type="submit" style="background:#006633; color:white; padding:10px 20px; border:none; border-radius:6px; font-weight:bold; cursor:pointer;">+ Create Category</button>
                    </form>
                </div>

                <!-- Categories List -->
                <div style="flex: 2; min-width: 320px; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <h3 style="color:#006633; margin-top:0;">Existing Categories</h3>
                    <table style="width:100%; border-collapse: collapse; margin-top:15px;">
                        <thead>
                            <tr style="background:#f1f5f9; text-align:left; border-bottom:2px solid #e2e8f0;">
                                <th style="padding:10px;">ID</th>
                                <th style="padding:10px;">Category Name</th>
                                <th style="padding:10px;">Description</th>
                                <th style="padding:10px; text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($cat = $result->fetch_assoc()): ?>
                                    <tr style="border-bottom:1px solid #e2e8f0;">
                                        <td style="padding:10px;"><?php echo $cat['category_id']; ?></td>
                                        <td style="padding:10px; font-weight:bold;"><?php echo $cat['category_name']; ?></td>
                                        <td style="padding:10px; color:#64748b; font-size:14px;"><?php echo $cat['description']; ?></td>
                                        <td style="padding:10px; text-align:center;">
                                            <a href="categories.php?delete=<?php echo $cat['category_id']; ?>" onclick="return confirm('Delete category <?php echo $cat['category_name']; ?>?');" style="background:#dc2626; color:white; padding:6px 12px; border-radius:4px; text-decoration:none; font-size:12px; font-weight:bold;">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" style="padding:20px; text-align:center; color:#94a3b8;">No categories found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <p>© 2026 NSBM EventHub | University Event Management System</p>
    </footer>
</body>
</html>

