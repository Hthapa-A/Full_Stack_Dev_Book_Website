<?php
// search.php - Enhanced multi-criteria search + AJAX live results
include('templates/header.php');
include('db_connect.php');

// Server-side search (when the user submits the form with GET)
$results = [];
$hasSearched = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && (isset($_GET['title']) || isset($_GET['author']) || isset($_GET['genre']) || isset($_GET['year']) || isset($_GET['q']))) {
    // Only treat as a search if any criteria present
    if ((isset($_GET['title']) && $_GET['title'] !== '') ||
        (isset($_GET['author']) && $_GET['author'] !== '') ||
        (isset($_GET['genre']) && $_GET['genre'] !== '') ||
        (isset($_GET['year']) && $_GET['year'] !== '') ||
        (isset($_GET['q']) && $_GET['q'] !== '')) {

        $hasSearched = true;

        // sanitize inputs
        $title = isset($_GET['title']) ? trim($_GET['title']) : '';
        $author = isset($_GET['author']) ? trim($_GET['author']) : '';
        $genre = isset($_GET['genre']) ? trim($_GET['genre']) : '';
        $year = (isset($_GET['year']) && $_GET['year'] !== '') ? intval($_GET['year']) : null;
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';

        // Build query and parameters
        $sql = "SELECT * FROM books WHERE 1=1";
        $params = [];
        $types = '';

        if ($title !== '') {
            $sql .= " AND title LIKE ?";
            $params[] = '%' . $title . '%';
            $types .= 's';
        }
        if ($author !== '') {
            $sql .= " AND author LIKE ?";
            $params[] = '%' . $author . '%';
            $types .= 's';
        }
        if ($genre !== '') {
            $sql .= " AND genre LIKE ?";
            $params[] = '%' . $genre . '%';
            $types .= 's';
        }
        if ($year !== null) {
            $sql .= " AND published_year = ?";
            $params[] = $year;
            $types .= 'i';
        }
        if ($q !== '') {
            // free-text across title/author/genre
            $sql .= " AND (title LIKE ? OR author LIKE ? OR genre LIKE ?)";
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
            $params[] = '%' . $q . '%';
            $types .= 'sss';
        }

        $sql .= " ORDER BY published_year DESC, title ASC LIMIT 100"; // limit for safety

        $stmt = $mysqli->prepare($sql);
        if ($stmt) {
            if (!empty($params)) {
                // dynamic bind_param
                $bind_names = [];
                $bind_names[] = $types;
                for ($i = 0; $i < count($params); $i++) {
                    // create variables for reference binding
                    $bind_name = 'param' . $i;
                    $$bind_name = $params[$i];
                    $bind_names[] = &$$bind_name;
                }
                call_user_func_array(array($stmt, 'bind_param'), $bind_names);
            }
            $stmt->execute();
            $res = $stmt->get_result();
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $results[] = $row;
                }
            }
            $stmt->close();
        }
    }
}

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<div class="container mt-4">
  <h3>Search Books</h3>

  <!-- Multi-criteria search form -->
  <form method="get" class="row g-2 mb-3" id="search-form">
    <div class="col-md-4">
      <input name="title" id="search-box" value="<?= isset($_GET['title']) ? h($_GET['title']) : '' ?>" class="form-control" placeholder="Title (autocomplete)">
    </div>
    <div class="col-md-3">
      <input name="author" value="<?= isset($_GET['author']) ? h($_GET['author']) : '' ?>" class="form-control" placeholder="Author">
    </div>
    <div class="col-md-2">
      <input name="genre" value="<?= isset($_GET['genre']) ? h($_GET['genre']) : '' ?>" class="form-control" placeholder="Genre">
    </div>
    <div class="col-md-1">
      <input name="year" value="<?= isset($_GET['year']) ? h($_GET['year']) : '' ?>" type="number" min="0" max="9999" class="form-control" placeholder="Year">
    </div>
    <div class="col-md-2 d-flex">
      <button class="btn btn-primary me-2" type="submit">Search</button>
      <a href="search.php" class="btn btn-outline-secondary">Reset</a>
    </div>
  </form>

  <!-- Live suggestions (shown while typing in title) -->
  <ul id="suggestions" class="list-group mb-3" style="display:none;"></ul>

  <!-- Server-side results (after form submit) -->
  <?php if ($hasSearched): ?>
    <?php if (!empty($results)): ?>
      <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>Title</th><th>Author</th><th>Genre</th><th>Year</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($results as $row): ?>
            <tr>
              <td><?= h($row['title']) ?></td>
              <td><?= h($row['author']) ?></td>
              <td><?= h($row['genre']) ?></td>
              <td><?= h($row['published_year']) ?></td>
              <td>
                <a class="btn btn-sm btn-info" href="details.php?id=<?= (int)$row['id'] ?>">View</a>
                <a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int)$row['id'] ?>">Edit</a>
                <a class="btn btn-sm btn-danger" href="delete.php?id=<?= (int)$row['id'] ?>" onclick="return confirm('Delete?');">Del</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    <?php else: ?>
      <p>No results found.</p>
    <?php endif; ?>
  <?php else: ?>
    <p class="text-muted">Use the form above to search by title, author, genre and year. You can combine filters (for example: <em>Sci-Fi</em> + <strong>2023</strong>).</p>
  <?php endif; ?>
</div>

<script>
/*
 Search page JS:
  - AJAX live suggestions for the title field (debounced)
  - Clicking a suggestion fills the title input and submits the form
  - Escaping HTML for safe insertion
*/
const input = document.getElementById('search-box');
const suggestions = document.getElementById('suggestions');
let timeout = null;

function escapeHtml(text) {
  if (text === null || text === undefined) return '';
  return text.toString()
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

input.addEventListener('input', () => {
  clearTimeout(timeout);
  const query = input.value.trim();
  if (!query) {
    suggestions.style.display = 'none';
    suggestions.innerHTML = '';
    return;
  }
  timeout = setTimeout(async () => {
    try {
      const res = await fetch(`ajax_books.php?q=${encodeURIComponent(query)}`);
      const data = await res.json();
      if (!data || data.length === 0) {
        suggestions.style.display = 'none';
        suggestions.innerHTML = '';
        return;
      }
      suggestions.style.display = 'block';
      suggestions.innerHTML = data.map(book =>
        `<li class="list-group-item suggestion-item" data-id="${book.id}" data-title="${escapeHtml(book.title)}">
          <strong>${escapeHtml(book.title)}</strong> by ${escapeHtml(book.author)} <span class="text-muted">(${escapeHtml(book.genre)}, ${escapeHtml(book.published_year)})</span>
          <button class="btn btn-sm btn-info float-end ms-2 view-btn" data-id="${book.id}">View</button>
        </li>`
      ).join('');
    } catch (e) {
      suggestions.style.display = 'none';
      suggestions.innerHTML = '';
    }
  }, 220); // debounce
});

// click handlers (delegation)
suggestions.addEventListener('click', (ev) => {
  const li = ev.target.closest('.suggestion-item');
  if (!li) return;

  // If user clicked the View button, follow details link
  if (ev.target.classList.contains('view-btn')) {
    const id = ev.target.getAttribute('data-id');
    window.location.href = `details.php?id=${encodeURIComponent(id)}`;
    return;
  }

  // otherwise set the title input and submit the form
  const title = li.getAttribute('data-title') || '';
  input.value = title;
  suggestions.style.display = 'none';
  suggestions.innerHTML = '';
  // auto-submit the form to run server-side search with the chosen title
  document.getElementById('search-form').submit();
});
</script>

<?php include('templates/footer.php'); ?>
