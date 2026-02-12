<?php
require_once '../includes/config.php';
checkLogin();

$message = '';
$action = isset($_GET['action']) ? $_GET['action'] : 'posts';

// Handle Post Actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_post'])) {
        $title = sanitize($_POST['title']);
        $content = sanitize($_POST['content']);
        $category = (int)$_POST['category'];
        $author = sanitize($_POST['author']);
        
        $stmt = $conn->prepare("INSERT INTO posts (title, content, category_id, author) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $title, $content, $category, $author);
        
        if ($stmt->execute()) {
            $post_id = $conn->insert_id;
            
            // Add tags
            if (!empty($_POST['tags'])) {
                $tags = explode(',', $_POST['tags']);
                foreach ($tags as $tag) {
                    $tag = trim($tag);
                    if (!empty($tag)) {
                        // Check if tag exists
                        $tag_check = $conn->query("SELECT id FROM tags WHERE name = '$tag'");
                        if ($tag_check->num_rows > 0) {
                            $tag_id = $tag_check->fetch_assoc()['id'];
                        } else {
                            $conn->query("INSERT INTO tags (name) VALUES ('$tag')");
                            $tag_id = $conn->insert_id;
                        }
                        $conn->query("INSERT INTO post_tags (post_id, tag_id) VALUES ($post_id, $tag_id)");
                    }
                }
            }
            
            $message = "Post added successfully!";
        }
    }
    
    if (isset($_POST['add_category'])) {
        $name = sanitize($_POST['category_name']);
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $message = "Category added successfully!";
        }
    }
}

// Handle Delete Actions
if (isset($_GET['delete_post'])) {
    $id = (int)$_GET['delete_post'];
    $conn->query("DELETE FROM posts WHERE id = $id");
    $message = "Post deleted successfully!";
}

if (isset($_GET['delete_category'])) {
    $id = (int)$_GET['delete_category'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    $message = "Category deleted successfully!";
}

if (isset($_GET['delete_comment'])) {
    $id = (int)$_GET['delete_comment'];
    $conn->query("DELETE FROM comments WHERE id = $id");
    $message = "Comment deleted successfully!";
}

// Get Statistics
$total_posts = $conn->query("SELECT COUNT(*) as count FROM posts")->fetch_assoc()['count'];
$total_comments = $conn->query("SELECT COUNT(*) as count FROM comments")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(*) as count FROM categories")->fetch_assoc()['count'];

// Get Data
$posts = $conn->query("SELECT p.*, c.name as category FROM posts p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
$categories = $conn->query("SELECT * FROM categories ORDER BY name");
$comments = $conn->query("SELECT c.*, p.title as post_title FROM comments c JOIN posts p ON c.post_id = p.id ORDER BY c.created_at DESC");

$page_title = "Dashboard";
include '../includes/header.php';
?>

<div class="dashboard">
    <div class="container">
        <div class="dashboard-header">
            <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <p>Welcome back, <?php echo $_SESSION['admin_username']; ?>!</p>
        </div>

        <?php if ($message): ?>
            <div class="alert success"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_posts; ?></h3>
                    <p>Total Posts</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-comments"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_comments; ?></h3>
                    <p>Total Comments</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-folder"></i></div>
                <div class="stat-info">
                    <h3><?php echo $total_categories; ?></h3>
                    <p>Categories</p>
                </div>
            </div>
        </div>

        <div class="dashboard-tabs">
            <button class="tab-btn <?php echo $action == 'posts' ? 'active' : ''; ?>" onclick="showTab('posts')">
                <i class="fas fa-file-alt"></i> Posts
            </button>
            <button class="tab-btn <?php echo $action == 'categories' ? 'active' : ''; ?>" onclick="showTab('categories')">
                <i class="fas fa-folder"></i> Categories
            </button>
            <button class="tab-btn <?php echo $action == 'comments' ? 'active' : ''; ?>" onclick="showTab('comments')">
                <i class="fas fa-comments"></i> Comments
            </button>
        </div>

        <!-- Posts Tab -->
        <div id="posts" class="tab-content <?php echo $action == 'posts' ? 'active' : ''; ?>">
            <div class="section-header">
                <h2>Manage Posts</h2>
                <button class="btn btn-primary" onclick="toggleForm('addPostForm')">
                    <i class="fas fa-plus"></i> Add New Post
                </button>
            </div>

            <div id="addPostForm" class="form-container" style="display: none;">
                <h3>Add New Post</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="content" rows="8" required></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <?php 
                                $cats = $conn->query("SELECT * FROM categories");
                                while($cat = $cats->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Author</label>
                            <input type="text" name="author" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Tags (comma-separated)</label>
                        <input type="text" name="tags" placeholder="PHP, Tutorial, News">
                    </div>
                    <button type="submit" name="add_post" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Post
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm('addPostForm')">Cancel</button>
                </form>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($post = $posts->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $post['id']; ?></td>
                            <td><?php echo htmlspecialchars($post['title']); ?></td>
                            <td><span class="badge"><?php echo $post['category']; ?></span></td>
                            <td><?php echo htmlspecialchars($post['author']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                            <td>
                                <a href="../post.php?id=<?php echo $post['id']; ?>" class="btn-sm btn-info" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?delete_post=<?php echo $post['id']; ?>" 
                                   class="btn-sm btn-danger" 
                                   onclick="return confirm('Delete this post?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Categories Tab -->
        <div id="categories" class="tab-content <?php echo $action == 'categories' ? 'active' : ''; ?>">
            <div class="section-header">
                <h2>Manage Categories</h2>
                <button class="btn btn-primary" onclick="toggleForm('addCategoryForm')">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>

            <div id="addCategoryForm" class="form-container" style="display: none;">
                <h3>Add New Category</h3>
                <form method="POST">
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="category_name" required>
                    </div>
                    <button type="submit" name="add_category" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Category
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm('addCategoryForm')">Cancel</button>
                </form>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $categories->data_seek(0);
                    while($cat = $categories->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($cat['created_at'])); ?></td>
                            <td>
                                <a href="?delete_category=<?php echo $cat['id']; ?>&action=categories" 
                                   class="btn-sm btn-danger" 
                                   onclick="return confirm('Delete this category?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Comments Tab -->
        <div id="comments" class="tab-content <?php echo $action == 'comments' ? 'active' : ''; ?>">
            <h2>Manage Comments</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Post</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($comment = $comments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $comment['id']; ?></td>
                            <td><?php echo htmlspecialchars($comment['name']); ?></td>
                            <td><?php echo htmlspecialchars($comment['post_title']); ?></td>
                            <td><?php echo substr($comment['comment'], 0, 50); ?>...</td>
                            <td><?php echo date('M d, Y', strtotime($comment['created_at'])); ?></td>
                            <td>
                                <a href="?delete_comment=<?php echo $comment['id']; ?>&action=comments" 
                                   class="btn-sm btn-danger" 
                                   onclick="return confirm('Delete this comment?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>