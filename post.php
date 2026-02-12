<?php
require_once 'includes/config.php';

$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $comment = sanitize($_POST['comment']);
    
    if (!empty($name) && !empty($email) && !empty($comment)) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $post_id, $name, $email, $comment);
        
        if ($stmt->execute()) {
            $success_message = "Comment posted successfully!";
        }
        $stmt->close();
    }
}

// Get post details
$query = "SELECT p.*, c.name as category_name 
          FROM posts p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE p.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    redirect('index.php');
}

// Get tags for this post
$tags_query = "SELECT t.name FROM tags t 
               JOIN post_tags pt ON t.id = pt.tag_id 
               WHERE pt.post_id = ?";
$stmt = $conn->prepare($tags_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$tags_result = $stmt->get_result();

// Get comments
$comments_query = "SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($comments_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments = $stmt->get_result();

$page_title = $post['title'];
include 'includes/header.php';
?>

<div class="container post-detail">
    <article class="post-content">
        <div class="post-header">
            <span class="category-badge"><?php echo htmlspecialchars($post['category_name']); ?></span>
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
            <div class="post-info">
                <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($post['author']); ?></span>
                <span><i class="far fa-calendar"></i> <?php echo date('F d, Y', strtotime($post['created_at'])); ?></span>
                <span><i class="far fa-clock"></i> <?php echo ceil(str_word_count($post['content']) / 200); ?> min read</span>
            </div>
        </div>

        <div class="post-body">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>

        <?php if ($tags_result->num_rows > 0): ?>
            <div class="post-tags">
                <strong>Tags:</strong>
                <?php while($tag = $tags_result->fetch_assoc()): ?>
                    <span class="tag"><?php echo htmlspecialchars($tag['name']); ?></span>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </article>

    <section class="comments-section">
        <h2><i class="far fa-comments"></i> Comments (<?php echo $comments->num_rows; ?>)</h2>

        <?php if (isset($success_message)): ?>
            <div class="alert success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <div class="comment-form">
            <h3>Leave a Comment</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <textarea name="comment" rows="5" placeholder="Your Comment" required></textarea>
                </div>
                <button type="submit" name="submit_comment" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Post Comment
                </button>
            </form>
        </div>

        <div class="comments-list">
            <?php if ($comments->num_rows > 0): ?>
                <?php while($comment = $comments->fetch_assoc()): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <div class="comment-avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <div class="comment-meta">
                                <strong><?php echo htmlspecialchars($comment['name']); ?></strong>
                                <span class="comment-date"><?php echo date('M d, Y \a\t g:i A', strtotime($comment['created_at'])); ?></span>
                            </div>
                        </div>
                        <div class="comment-body">
                            <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-comments">No comments yet. Be the first to comment!</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="back-link">
        <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Blog</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>