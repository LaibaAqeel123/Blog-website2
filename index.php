<?php
require_once 'includes/config.php';

// Pagination
$posts_per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $posts_per_page;

// Filter by category
$category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$where_clause = $category_filter ? "WHERE p.category_id = $category_filter" : "";

// Get total posts
$total_query = "SELECT COUNT(*) as total FROM posts p $where_clause";
$total_result = $conn->query($total_query);
$total_posts = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get posts
$query = "SELECT p.*, c.name as category_name, 
          (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
          FROM posts p 
          LEFT JOIN categories c ON p.category_id = c.id 
          $where_clause
          ORDER BY p.created_at DESC 
          LIMIT $offset, $posts_per_page";
$posts = $conn->query($query);

// Get categories for sidebar
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

$page_title = "Home";
include 'includes/header.php';
?>

<div class="hero">
    <div class="container">
        <h1>Welcome to Our Blog</h1>
        <p>Discover amazing stories, tutorials, and insights</p>
    </div>
</div>

<div class="container main-content">
    <div class="content-wrapper">
        <div class="posts-grid">
            <?php if ($posts->num_rows > 0): ?>
                <?php while($post = $posts->fetch_assoc()): ?>
                    <article class="post-card">
                        <div class="post-meta">
                            <span class="category"><?php echo htmlspecialchars($post['category_name']); ?></span>
                            <span class="date"><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        <h2><a href="post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        <p><?php echo substr(strip_tags($post['content']), 0, 150); ?>...</p>
                        <div class="post-footer">
                            <span class="author"><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                            <span class="comments"><i class="far fa-comments"></i> <?php echo $post['comment_count']; ?> Comments</span>
                        </div>
                        <a href="post.php?id=<?php echo $post['id']; ?>" class="read-more">Read More <i class="fas fa-arrow-right"></i></a>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-posts">
                    <i class="fas fa-inbox"></i>
                    <p>No posts found. Check back later!</p>
                </div>
            <?php endif; ?>
        </div>

        <aside class="sidebar">
            <div class="widget">
                <h3>Categories</h3>
                <ul class="category-list">
                    <li><a href="index.php" class="<?php echo !$category_filter ? 'active' : ''; ?>">All Posts</a></li>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <li>
                            <a href="index.php?category=<?php echo $cat['id']; ?>" 
                               class="<?php echo $category_filter == $cat['id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <div class="widget">
                <h3>About</h3>
                <p>Welcome to our blog where we share insights, tutorials, and stories from various topics.</p>
            </div>
        </aside>
    </div>

    <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page-1; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?>" class="page-link">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?>" 
                   class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page+1; ?><?php echo $category_filter ? '&category='.$category_filter : ''; ?>" class="page-link">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>