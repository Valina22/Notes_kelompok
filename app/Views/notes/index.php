<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

<style>
body {
    background: #f9fafb;
}

/* Sidebar */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 90px;
    height: 100vh;
    background: #ffffff;
    border-right: 1px solid #eee;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding-top: 2rem;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.03);
    z-index: 10;
}

.add-note-btn {
    font-size: 24px;
    width: 50px;
    height: 50px;
    border: none;
    border-radius: 50%;
    background-color: #000;
    color: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    cursor: pointer;
    margin-bottom: 1rem;
    transition: background 0.3s;
}

.add-note-btn:hover {
    background-color: #333;
}

.color-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 1rem;
}

.color-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    transition: transform 0.2s;
}

.color-circle:hover {
    transform: scale(1.1);
}

.color-circle[data-color="#F5B5FC"] { background-color: #F5B5FC; }
.color-circle[data-color="#FFDFBA"] { background-color: #FFDFBA; }
.color-circle[data-color="#BAE1FF"] { background-color: #BAE1FF; }
.color-circle[data-color="#D4F8E8"] { background-color: #D4F8E8; }

.notes-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* New note from sidebar */
.note {
    width: 200px;
    min-height: 150px;
    border-radius: 12px;
    padding: 10px;
    color: #333;
    font-size: 14px;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    animation: fadeIn 0.3s ease-in-out;
}

.note:focus {
    outline: none;
    border: 1px dashed #aaa;
}

.hidden {
    display: none;
}

/* Main content area */
.main-content {
    margin-left: 110px;
    padding: 2rem;
}

.note-card {
    border-radius: 18px;
    padding: 1rem;
    color: #000;
    min-height: 150px;
    position: relative;
    transition: transform 0.2s;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.note-card:hover {
    transform: scale(1.02);
}

.edit-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    font-size: 16px;
    background-color: rgba(255, 255, 255, 0.7);
    border-radius: 50%;
    padding: 4px 6px;
    text-decoration: none;
    color: #000;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}
</style>

<div class="sidebar">
    <button id="addNoteBtn" class="add-note-btn">+</button>
    <div id="colorOptions" class="color-options hidden">
        <div class="color-circle" data-color="#F5B5FC"></div>
        <div class="color-circle" data-color="#FFDFBA"></div>
        <div class="color-circle" data-color="#BAE1FF"></div>
        <div class="color-circle" data-color="#D4F8E8"></div>
    </div>
    <div id="notesContainer" class="notes-container"></div>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">Notes</h1>
        <form action="/notes" method="get" class="d-flex" style="max-width: 300px;">
            <input type="text" name="keyword" class="form-control me-2" placeholder="Search..." value="<?= esc($_GET['keyword'] ?? '') ?>">
            <button class="btn btn-outline-secondary" title="Cari">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="row">
        <?php if (empty($notes)) : ?>
            <div class="alert alert-warning">Tidak ada catatan ditemukan.</div>
        <?php else: ?>
            <?php 
                $colors = ['#fcd34d', '#f87171', '#c084fc', '#60a5fa', '#34d399', '#fbbf24', '#fdba74'];
                $i = 0;
            ?>
            <?php foreach ($notes as $note): ?>
                <div class="col-md-4 mb-4">
                    <div class="note-card" style="background-color: <?= $colors[$i++ % count($colors)] ?>;">
                        <strong><?= esc($note['title']) ?></strong>
                        <p class="mt-2"><?= esc($note['content']) ?></p>
                        <small><?= date('F j, Y', strtotime($note['created_at'])) ?></small>
                        <a href="/notes/edit/<?= $note['id'] ?>" class="edit-btn shadow">✏️</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('addNoteBtn').addEventListener('click', () => {
    document.getElementById('colorOptions').classList.toggle('hidden');
});

document.querySelectorAll('.color-circle').forEach(circle => {
    circle.addEventListener('click', () => {
        const color = circle.getAttribute('data-color');
        const note = document.createElement('div');
        note.className = 'note';
        note.style.backgroundColor = color;
        note.contentEditable = true;
        note.textContent = 'Tulis catatan...';
        document.getElementById('notesContainer').appendChild(note);
        document.getElementById('colorOptions').classList.add('hidden');
    });
});
</script>

<?= $this->endSection() ?>
